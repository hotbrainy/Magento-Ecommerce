<?php

/**
 * MageWorx
 * Loyalty Booster Extension
 *
 * @category   MageWorx
 * @package    MageWorx_CustomerCredit
 * @copyright  Copyright (c) 2016 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_CustomerCredit_Block_Adminhtml_Code extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'adminhtml_code';
        $this->_blockGroup = 'mageworx_customercredit';
        $this->_headerText = Mage::helper('mageworx_customercredit')->__('Manage Recharge Codes');
        $this->_addButtonLabel = Mage::helper('mageworx_customercredit')->__('Generate New Recharge Codes');

        parent::__construct();
        $this->_addButton('import', array(
            'label'     => Mage::helper('mageworx_customercredit')->__('Import Codes'),
            'onclick'   => 'setLocation(\'' . $this->getImportUrl() .'\')',
            'class'     => 'import',
        ));
        $this->_addButton('export', array(
            'label'     => Mage::helper('mageworx_customercredit')->__('Export Codes'),
            'onclick'   => 'setLocation(\'' . $this->getExportUrl() .'\')',
            'class'     => 'export',
        ));
    }
    
    public function getImportUrl(){
        return $this->getUrl('*/mageworx_customercredit_import_code');
    }
    public function getExportUrl(){
        return $this->getUrl('*/mageworx_customercredit_code/export');
    }
}