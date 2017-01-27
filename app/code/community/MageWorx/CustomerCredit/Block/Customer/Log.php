<?php

/**
 * MageWorx
 * Loyalty Booster Extension
 *
 * @category   MageWorx
 * @package    MageWorx_CustomerCredit
 * @copyright  Copyright (c) 2016 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_CustomerCredit_Block_Customer_Log extends Mage_Core_Block_Template
{
    public function __construct() {
        parent::__construct();
        $this->setTemplate('mageworx/customercredit/customer/log.phtml');
        $logCollection = Mage::getResourceModel('mageworx_customercredit/credit_log_collection')
                ->addWebsiteFilter((Mage::helper('mageworx_customercredit')->isScopePerWebsite()?(int)Mage::app()->getStore()->getWebsiteId():0))
                ->addCustomerFilter((int) Mage::getSingleton('customer/session')->getCustomerId())
                ->setOrder('action_date')
        ;
        $this->setLogItems($logCollection);
    }

    protected function _prepareLayout() {
        parent::_prepareLayout();
        $pager = $this->getLayout()->createBlock('page/html_pager', 'customercredit.credit.log.pager')->setCollection($this->getLogItems());
        $this->setChild('pager', $pager);
        $this->getLogItems()->load();
        return $this;
    }

    public function getPagerHtml() {
        return $this->getChildHtml('pager');
    }

    public function getActionTypeLabel($id) {
        $actionTypes = Mage::getSingleton('mageworx_customercredit/credit_log')->getActionTypesOptions();
        if (isset($actionTypes[$id])) {
            return $actionTypes[$id];
        }
        return '';
    }
}