<?php
/**
 * MageWorx
 * All Extension
 *
 * @category   MageWorx
 * @package    MageWorx_All
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_All_Adminhtml_Mageworx_All_SupportController extends  Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        if (!$this->getRequest()->isPost()) {
            $this->_forward('adminhtml');
            return $this;
        }

        $data = $this->getRequest()->getPost();
        $data['reason'] = isset($data['other_reason']) ? $data['other_reason'] : $data['reason'];
        $support = Mage::getModel('mageworx_all/support')->setData($data);

        try {
            $support->send();
        } catch (Exception $e) {
            $result['message'] = $e->getMessage();
            $this->_ajaxResponse($result);
            return;
        }
        $result['message'] = $this->__('Message sent');
        $this->_ajaxResponse($result);
    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('system/config/mageworx/support');
    }

    protected function _ajaxResponse($result = array())
    {
        $this->getResponse()->setBody(Zend_Json::encode($result));
        return;
    }

}