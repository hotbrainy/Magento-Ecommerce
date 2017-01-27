<?php

/**
 * MageWorx
 * Loyalty Booster Extension
 *
 * @category   MageWorx
 * @package    MageWorx_CustomerCredit
 * @copyright  Copyright (c) 2016 MageWorx (http://www.mageworx.com/)
 */
 
class MageWorx_CustomerCredit_Block_Adminhtml_Code_Edit_Tab_Details extends Mage_Adminhtml_Block_Widget_Form
{
	protected function _prepareForm()
	{
		$model = Mage::registry('current_customercredit_code');
		$form = new Varien_Data_Form();
		$form->setHtmlIdPrefix('code_details_');
		$form->setFieldNameSuffix('details');
		
		$fieldset = $form->addFieldset('base_fieldset', array('legend'=>$this->_helper()->__('Details')));
        
		if ($model->getId()) {
            $fieldset->addField('code_id', 'hidden', array(
                'name' => 'code_id',
            ));
            $fieldset->addField('code', 'label', array(
                'name'      => 'code',
                'label'     => $this->_helper()->__('Code'),
                'title'     => $this->_helper()->__('Code')
            ));
        }
        
        $fieldset->addField('credit', 'text', array(
            'label'     => $this->_helper()->__('Credit Value'),
            'title'     => $this->_helper()->__('Credit Value'),
            'name'      => 'credit',
            'class'     => 'validate-number',
            'required'  => true,
            'after_element_html'      => '<div id="customercredit_currency_code"></div>',
        ));
        $fieldset->addField('website_id', 'select', array(
            'name'      => 'website_id',
            'label'     => $this->_helper()->__('Website'),
            'title'     => $this->_helper()->__('Website'),
            'required'  => true,
            'values'    => Mage::getSingleton('adminhtml/system_store')->getWebsiteValuesForForm(true),
        ));
        
        $fieldset->addField('is_onetime', 'select', array(
            'name'      => 'is_onetime',
            'label'     => $this->_helper()->__('Is Onetime'),
            'title'     => $this->_helper()->__('Is Onetime'),
            'required'  => true,
            'options'   => array_reverse(Mage::getModel('adminhtml/system_config_source_yesno')->toArray()),
        ));
        $fieldset->addField('is_active', 'select', array(
            'label'     => $this->_helper()->__('Is Active'),
            'title'     => $this->_helper()->__('Is Active'),
            'name'      => 'is_active',
            'required'  => true,
            'options'   => array_reverse(Mage::getModel('adminhtml/system_config_source_yesno')->toArray()),
        ));
        
        $dateFormatIso = Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);

        $fieldset->addField('from_date', 'date', array(
            'name'   => 'from_date',
            'label'  => $this->_helper()->__('From Date'),
            'title'  => $this->_helper()->__('From Date'),
            'image'  => $this->getSkinUrl('images/grid-cal.gif'),
            'format'       => $dateFormatIso,
        ));
        $fieldset->addField('to_date', 'date', array(
            'name'   => 'to_date',
            'label'  => $this->_helper()->__('To Date'),
            'title'  => $this->_helper()->__('To Date'),
            'image'  => $this->getSkinUrl('images/grid-cal.gif'),
            'format'       => $dateFormatIso,
        ));
        $form->setValues($model->getData());
        $this->setForm($form);

        return parent::_prepareForm();
	}
	
	protected function _prepareLayout()
	{
	    parent::_prepareLayout();
	    $jsCusrrency = $this->getLayout()->createBlock('core/template')->setTemplate('mageworx/customercredit/currency_js.phtml');
	    $this->setChild('js_currency', $jsCusrrency);
	}
	
	protected function _toHtml()
	{
	    $html = parent::_toHtml();
	    return $html . $this->getChild('js_currency')->toHtml();
	}
	
    public function getWebsiteHtmlId()
    {
        return 'code_details_website_id';
    }
	
	/**
	 * 
	 * @return MageWorx_CustomerCredit_Helper_Data
	 */
	protected function _helper()
	{
	    return Mage::helper('mageworx_customercredit');
	}
}