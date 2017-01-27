<?php
/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Regular License.
 * You may not use any part of the code in whole or part in any other software
 * or product or website.
 *
 * @author		Infortis
 * @copyright	Copyright (c) 2014 Infortis
 * @license		Regular License http://themeforest.net/licenses/regular 
 */

class Infortis_Dataporter_Block_Adminhtml_Cfgporter_Import_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
	/**
	 * Preparing form
	 *
	 * @return Mage_Adminhtml_Block_Widget_Form
	 */
	protected function _prepareForm()
	{
		$form = new Varien_Data_Form(
			array(
				'id'		=> 'edit_form',
				'method'	=> 'post',
				'enctype'	=> 'multipart/form-data'
			)
		);
		
		$fieldset = $form->addFieldset('display', array(
			'legend'	=> Mage::helper('dataporter')->__('Import settings'),
			'class'		=> 'fieldset-wide',
		));

		$fieldPreset = $fieldset->addField('preset_name', 'select', array(
			'name'		=> 'preset_name',
			'label'		=> Mage::helper('dataporter')->__('Select Configuration to Import'),
			'title'		=> Mage::helper('dataporter')->__('Select Configuration to Import'),
			'required'	=> true,
			'values'	=> Mage::getModel('dataporter/source_cfgporter_packagepresets')
				->toOptionArray($this->getRequest()->getParam('package')),
		));

		$fieldDataImportFile = $fieldset->addField('data_import_file', 'file', array(
			'name'		=> 'data_import_file',
			'label'		=> Mage::helper('dataporter')->__('Select File With Saved Configuration to Import'),
			'title'		=> Mage::helper('dataporter')->__('Select File With Saved Configuration to Import'),
			'required'	=> false,
		));
		//IMPORTANT: allow to select only one store per import
		$fieldStores = $fieldset->addField('store_id', 'select', array(
			'name'		=> 'stores',
			'label'		=> Mage::helper('cms')->__('Configuration Scope'),
			'title'		=> Mage::helper('cms')->__('Configuration Scope'),
			'note'		=> Mage::helper('dataporter')->__("Imported configuration settings will be applied to selected scope (selected store view or website). If you're not sure what is 'scope' in Magento system configuration, it is highly recommended to leave the default scope <strong>'Default Config'</strong>. In this case imported configuration will be applied to all existing store views."),
			'required'	=> true,
			'values'	=> Mage::getSingleton('infortis/config_scope')->getScopeSelectOptions(true, true),
			'value'		=> 'default@0',
		));
		$renderer = $this->getLayout()->createBlock('adminhtml/store_switcher_form_renderer_fieldset_element');
		$fieldStores->setRenderer($renderer);

		/**
		 * Send back the control parameters
		 */
		$fieldset->addField('action_type', 'hidden', array(
			'name'  => 'action_type',
			'value' => $this->getRequest()->getParam('action_type'),
		));

		$fieldset->addField('package', 'hidden', array(
			'name'  => 'package',
			'value' => $this->getRequest()->getParam('package'),
		));

		//Set action and other parameters
		$actionUrl = $this->getUrl('*/*/import');
		$form->setAction($actionUrl);
		$form->setUseContainer(true);

		$this->setForm($form);
		$this->setChild(
			'form_after',
			$this->getLayout()->createBlock('adminhtml/widget_form_element_dependence')
				->addFieldMap($fieldDataImportFile->getHtmlId(), $fieldDataImportFile->getName())
				->addFieldMap($fieldPreset->getHtmlId(), $fieldPreset->getName())
				->addFieldDependence(
					$fieldDataImportFile->getName(),
					$fieldPreset->getName(),
					'upload_custom_file'
				)
		);
		return parent::_prepareForm();
	}
}
