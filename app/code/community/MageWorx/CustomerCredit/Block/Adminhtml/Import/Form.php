<?php

/**
 * MageWorx
 * Loyalty Booster Extension
 *
 * @category   MageWorx
 * @package    MageWorx_CustomerCredit
 * @copyright  Copyright (c) 2016 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_CustomerCredit_Block_Adminhtml_Import_Form extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * Add fieldset
     *
     * @return Mage_ImportExport_Block_Adminhtml_Import_Edit_Form
     */
    protected function _prepareForm()
    {
        $helper = Mage::helper('mageworx_customercredit');
        $form = new Varien_Data_Form(array(
            'id'      => 'edit_form',
            'action'  => $this->getUrl('*/*/import'),
            'method'  => 'post',
            'enctype' => 'multipart/form-data'
        ));
        $fieldset = $form->addFieldset('base_fieldset', array('legend' => $helper->__('Import Settings')));

        $fieldset->addField(Mage_ImportExport_Model_Import::FIELD_NAME_SOURCE_FILE, 'file', array(
            'name'     => Mage_ImportExport_Model_Import::FIELD_NAME_SOURCE_FILE,
            'label'    => $helper->__('Select File to Import'),
            'title'    => $helper->__('Select File to Import'),
            'required' => true,
            'after_element_html'=> '<br><a href="'.$this->getFileUrl().'">'.$this->__('Download')."</a> ".$this->__('example file')
        ));
    
        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }
    
    public function getFileUrl() {
        if($this->getRequest()->getControllerName()=='adminhtml_import_code') {
            return Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA).'mageworx/customercredit/import_codes.csv';
        }
        return Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA).'mageworx/customercredit/import_credits.csv';
    }
}
