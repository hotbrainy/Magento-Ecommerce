<?php

/**
 * MageWorx
 * Loyalty Booster Extension
 *
 * @category   MageWorx
 * @package    MageWorx_CustomerCredit
 * @copyright  Copyright (c) 2016 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_CustomerCredit_Block_Customer_Refferals_Details extends MageWorx_CustomerCredit_Block_Customer_View_Credit
{
    const CC_MIN_CREDIT_CODE    = 1;
    const CC_MAX_CREDIT_CODE    = 1000;
    const CC_DEFAULT_QTY        = 5;
    const CC_MAX_QTY            = 100;
    
    public function __construct() {
        parent::__construct();
        $customer = Mage::getModel('customer/session')->getCustomer();
        $collection = Mage::getResourceModel('mageworx_customercredit/code_collection')->addOwnerFilter($customer->getId());
        $this->setCollection($collection);
    }
    
    public function minCreditCode()
    {
        return self::CC_MIN_CREDIT_CODE;
    }
    
    public function defaultCodes()
    {
        return self::CC_DEFAULT_QTY;
    }

    protected function _prepareLayout() {
        parent::_prepareLayout();
        $pager = $this->getLayout()->createBlock('page/html_pager', 'customercredit.credit.code.pager')->setCollection($this->getCollection());
        $this->setChild('pager', $pager);
        $this->getCollection()->load();
        return $this;
    }

    public function getPagerHtml() {
        return $this->getChildHtml('pager');
    }

}