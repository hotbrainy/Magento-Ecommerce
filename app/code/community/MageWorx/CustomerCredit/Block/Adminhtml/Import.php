<?php

/**
 * MageWorx
 * Loyalty Booster Extension
 *
 * @category   MageWorx
 * @package    MageWorx_CustomerCredit
 * @copyright  Copyright (c) 2016 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_CustomerCredit_Block_Adminhtml_Import extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();

        $this->removeButton('back')
            ->removeButton('reset')
            ->_updateButton('save', 'label', $this->__('Import Data'));
    }

    /**
     * Internal constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_mode = 'import';
        $this->_blockGroup = 'mageworx_customercredit';
        $this->_controller = 'adminhtml';
        parent::_construct();

    }

    /**
     * Get header text
     *
     * @return string
     */
    public function getHeaderText()
    {
        if($this->getRequest()->getControllerName()=='adminhtml_import_code') {
            return Mage::helper('mageworx_customercredit')->__('Recharge Code Import');
        }
        return Mage::helper('mageworx_customercredit')->__('Loyalty Booster Import');
    }
}
