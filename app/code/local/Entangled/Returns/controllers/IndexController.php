<?php

class Entangled_Returns_IndexController extends Mage_Core_Controller_Front_Action
{
    public function newRequestAction()
    {
        $params = $this->getRequest()->getPost();
        try {
            $request = Mage::getModel('entangled_returns/request');
            $request->setUserId(Mage::getSingleton('customer/session')->getCustomerId());
            $request->setDate(Mage::getModel('core/date')->timestamp());//Magento's timestamp function makes a usage of timezone and converts it to timestamp
            $request->addData($params);
            $request->save();
            $request->sendRequestEmail();
            Mage::getSingleton('customer/session')->addSuccess($this->__('The request was succesfully sent. Returns can take up to 48 hours to process'));
            return $this->_redirectSuccess(Mage::getUrl('downloadable/customer/products'));
        } catch (Exception $e) {
            Mage::getSingleton('customer/session')->addException($e, $e->getMessage());
            return $this->_redirectError(Mage::getUrl('downloadable/customer/products'));
        }
    }
}

