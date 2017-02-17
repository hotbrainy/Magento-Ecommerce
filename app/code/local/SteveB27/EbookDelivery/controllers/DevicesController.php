<?php

class SteveB27_EbookDelivery_DevicesController extends Mage_Core_Controller_Front_Action
{
    /**
     * Retrieve customer session object
     *
     * @return Mage_Customer_Model_Session
     */
    protected function _getSession()
    {
        return Mage::getSingleton('customer/session');
    }

	public function preDispatch()
    {
        parent::preDispatch();

        $exceptedActions = ['addajax'];
        if(in_array(strtolower($this->getRequest()->getActionName()), $exceptedActions)){
            return;
        }

        $loginUrl = Mage::helper('customer')->getLoginUrl();

        if (!Mage::getSingleton('customer/session')->authenticate($this, $loginUrl)) {
            $this->setFlag('', self::FLAG_NO_DISPATCH, true);
        }
    }
    
	public function indexAction()
    {
        $this->loadLayout();
        $this->_initLayoutMessages('customer/session');
        if ($block = $this->getLayout()->getBlock('ebookdelivery_devices')) {
            $block->setRefererUrl($this->_getRefererUrl());
        }
        $headBlock = $this->getLayout()->getBlock('head');
        if ($headBlock) {
            $headBlock->setTitle(Mage::helper('ebookdelivery')->__('My Devices'));
        }
        $this->renderLayout();
    }

    public function sendAction(){
        $id = $this->getRequest()->getParam("id");
        $customerId = Mage::getSingleton("customer/session")->getCustomerId();
        if($id){
            $item = Mage::getModel('downloadable/link_purchased_item')->load($id);
            $item->setPurchased(Mage::getModel('downloadable/link_purchased')->load($item->getPurchasedId()));
            if($item->getProductId()){
                $order = Mage::getModel("sales/order")->load($item->getPurchased()->getOrderId());
                if($order->getCustomerId() == $customerId){
                    $deliveryDevices = null;
                    $deliveryDevices = Mage::getModel('ebookdelivery/devices')->getCollection()->addFieldToFilter("customer_id",$customerId);

                    try{
                        $links = Mage::getModel('downloadable/link_purchased_item')->getCollection()->addFieldToFilter('order_item_id', $item->getOrderItemId());
                        foreach($deliveryDevices as $key => $deliveryDevice) {
                            $helper = 'ebookdelivery/'.$deliveryDevice['device_type'];
                            Mage::helper($helper)->deliver($deliveryDevice['device_email'],$links);
                        }
                        $this->_getSession()->addSuccess($this->__('The book has been delivered to all of your devices.'));

                        return $this->_redirectSuccess(Mage::getUrl('downloadable/customer/products'));
                    }catch (Exception $e) {
                        $this->_getSession()->addException($e, $this->__('An error occurred while sending the requested content. Please contact the store owner.'));
                        return $this->_redirectError(Mage::getUrl('downloadable/customer/products'));
                    }
                }
            }
        }
        $this->_getSession()->addError($this->__('An error occurred while sending the requested content. Please contact the store owner.'));

        return $this->_redirectError(Mage::getUrl('downloadable/customer/products'));
    }

    public function removeAction(){
        $id = $this->getRequest()->getParam("id");
        $customerId = Mage::getSingleton("customer/session")->getCustomerId();
        if($id){
            $deliveryDevice = Mage::getModel('ebookdelivery/devices')->load($id);
            if($deliveryDevice->getCustomerId() == $customerId){
                try{
                    $deliveryDevice->delete();
                    $this->_getSession()->addSuccess($this->__('The device was removed.'));

                    return $this->_redirectSuccess(Mage::getUrl('ebookdelivery/devices/index'));
                }catch (Exception $e) {
                    $this->_getSession()->addException($e, $this->__('An error occurred while removing the device. Please contact the store owner.'));
                    return $this->_redirectError(Mage::getUrl('ebookdelivery/devices/index'));
                }
            }
        }
        $this->_getSession()->addError($this->__('An error occurred while removing the device. Please contact the store owner.'));

        return $this->_redirectError(Mage::getUrl('ebookdelivery/devices/index'));
    }

    public function addAction(){
        $this->loadLayout();
        $this->_initLayoutMessages('customer/session');
        $headBlock = $this->getLayout()->getBlock('head');
        if ($headBlock) {
            $headBlock->setTitle(Mage::helper('ebookdelivery')->__('Add New Device'));
        }
        $this->renderLayout();
    }

    public function addPostAction(){
        if ($this->getRequest()->isPost()) {
            $deliveryArray = $this->getRequest()->getPost('delivery', "");
            $customerId = Mage::getSingleton("customer/session")->getCustomerId();
            $devices = array();
            $added = 0;
            foreach ($deliveryArray as $key => $device) {
                foreach ($device as $deviceYype => $deviceFields) {
                    $model = Mage::getModel('ebookdelivery/devices');

                    $deviceFields["customer_id"] = $customerId;
                    $deviceFields["device_type"] = "amazonemail";
                    $existingModel = $model->getCollection()
                        ->addFieldToFilter("customer_id",$customerId)
                        ->addFieldToFilter("device_type",$deviceFields["device_type"])
                        ->addFieldToFilter("device_email",$deviceFields["device_email"]);
                    if(!$existingModel->count()){
                        $model->setData($deviceFields);
                        $model->save();
                        $added++;
                    }else{
                        $this->_getSession()->addError($this->__('The device '.$deviceFields["device_email"].' is already in your library.'));
                    }
                }
            }
            if($added > 0){
                $this->_getSession()->addSuccess($this->__('The devices were added.'));
            }
            return $this->_redirectSuccess(Mage::getUrl('ebookdelivery/devices/index'));
        }
        $this->_getSession()->addError($this->__('An error occurred while adding the device. Please try again.'));

        return $this->_redirectError(Mage::getUrl('ebookdelivery/devices/add'));
    }


    public function addAjaxAction(){
        $this->getResponse()->clearHeaders()->setHeader('Content-type','application/json',true);
        if(Mage::getSingleton("customer/session")->isLoggedIn()){
            $deliveryArray = $this->getRequest()->getPost('delivery', "");
            $customerId = Mage::getSingleton("customer/session")->getCustomerId();
            foreach ($deliveryArray as $key => $device) {
                foreach ($device as $deviceYype => $deviceFields) {
                    $model = Mage::getModel('ebookdelivery/devices');
                    $deviceFields["customer_id"] = $customerId;
                    $deviceFields["device_type"] = "amazonemail";
                    $existingModel = $model->getCollection()
                        ->addFieldToFilter("customer_id",$customerId)
                        ->addFieldToFilter("device_type",$deviceFields["device_type"])
                        ->addFieldToFilter("device_email",$deviceFields["device_email"]);
                    if(!$existingModel->count()){
                        $model->setData($deviceFields);
                        try{
                            $model->save();
                            $response_array['devices'][$key]['status'] = 'success';
                            $response_array['devices'][$key]['msg'] = $deviceFields["device_email"].' Kindle address saved successfully.';
                        }catch(Exception $e){
                            $response_array['devices'][$key]['status'] = 'error';
                            $response_array['devices'][$key]['msg'] = 'There was an error while adding '.$deviceFields["device_email"].' Kindle address to your account';
                        }
                    }else{
                        $response_array['devices'][$key]['status'] = 'error';
                        $response_array['devices'][$key]['msg'] = $deviceFields["device_email"].' Kindle address is already related to your account';
                    }
                }
            }
        }else{
            $response_array['status'] = 'success';
            $response_array['msg'] = 'Kindle saved successfully. Information will be saved to your new account';
        }
        if(!array_filter($response_array['devices'],function($response){return $response['status'] == "error";})) {
            $response_array['status'] = 'success';
            $response_array['msg'] = 'All Kindle addresses were saved successfully.';
        }else{
            $response_array['status'] = 'error';
        }
        $this->getResponse()->setBody(json_encode($response_array));
    }
}