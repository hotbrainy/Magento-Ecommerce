<?php

/**
 * MageWorx
 * Loyalty Booster Extension
 *
 * @category   MageWorx
 * @package    MageWorx_CustomerCredit
 * @copyright  Copyright (c) 2016 MageWorx (http://www.mageworx.com/)
 */
 
class MageWorx_CustomerCredit_Block_Customer_View_Recent extends MageWorx_CustomerCredit_Block_Customer_Log 
{
    public function __construct()
    {
        parent::__construct();
        $this->getLogItems()->setPageSize(5);
    }
    
    
    protected function _prepareLayout()
    {
        return $this;
    }
    
    public function getViewAllUrl()
    {
        return $this->getUrl('*/*/log');
    }
}