<?php

require_once "Magestore/Bannerslider/controllers/Adminhtml/Bannerslider/BannerController.php";

class Entangled_Bannerslider_Rewrite_Adminhtml_Bannerslider_BannerController extends Magestore_Bannerslider_Adminhtml_Bannerslider_BannerController {

    /**
     * save item action
     */
    public function saveAction() {
        if ($data = $this->getRequest()->getPost()) {
            //Zend_debug::dump($data);die();
            $store = $this->getRequest()->getParam('store');
            $model = Mage::getModel('bannerslider/banner');
            if (isset($data['image']['delete'])) {
                Mage::helper('bannerslider')->deleteImageFile($data['image']['value']);
            }
            if (isset($data['mobile_image']['delete'])) {
                Mage::helper('bannerslider')->deleteImageFile($data['mobile_image']['value']);
            }
            $image = Mage::helper('bannerslider')->uploadBannerImage();
            $bannerImage = Mage::helper('bannerslider')->uploadMobileBannerImage();
            if ($image || (isset($data['image']['delete']) && $data['image']['delete'])) {
                $data['image'] = $image;
            } else {
                unset($data['image']);
            }
            if ($bannerImage || (isset($data['mobile_image']['delete']) && $data['mobile_image']['delete'])) {
                $data['mobile_image'] = $bannerImage;
            } else {
                unset($data['mobile_image']);
            }

            $data = $this->_filterDateTime($data,array('start_time','end_time'));
            try {
                $data['start_time']=date('Y-m-d H:i:s',Mage::getModel('core/date')->gmtTimestamp(strtotime($data['start_time'])));
                $data['end_time']=date('Y-m-d H:i:s',Mage::getModel('core/date')->gmtTimestamp(strtotime($data['end_time'])));
            } catch (Exception $e) {}

            $model->setOrderBanner("7");
            $model->setData($data)
                ->setStoreId($store)
                ->setData('banner_id', $this->getRequest()->getParam('id'));
            try {


                $model->save();
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('bannerslider')->__('Banner was successfully saved'));
                Mage::getSingleton('adminhtml/session')->setFormData(false);
                //Zend_debug::dump($this->getRequest()->getParam('slider'));die();
                if($this->getRequest()->getParam('slider') == 'check'){
                    $this->_redirect('*/*/addin', array('id' => $model->getId()));
                    return;
                }
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('id' => $model->getId(), 'store' => $this->getRequest()->getParam("store")));
                    return;
                }
                if ($this->getRequest()->getParam('addin')) {
                    $this->_redirect('*/*/addin', array('id' => $model->getId(), 'store' => $this->getRequest()->getParam("store")));
                    return;
                }
                $this->_redirect('*/*/');
                return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('bannerslider')->__('Unable to find banner to save'));
        $this->_redirect('*/*/');
    }
}