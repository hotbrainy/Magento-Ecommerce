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

class Infortis_Dataporter_Block_Adminhtml_Cfgporter_Export_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
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
			)
		);

		$fieldset = $form->addFieldset('display', array(
			'legend'	=> Mage::helper('dataporter')->__('Export settings'),
			'class'		=> 'fieldset-wide'
		));

		$fieldset->addField('preset_name', 'text', array(
			'name'		=> 'preset_name',
			'label'		=> Mage::helper('dataporter')->__('File Name'),
			'title'		=> Mage::helper('dataporter')->__('File Name'),
			'note'		=> Mage::helper('dataporter')->__('This will be the name of the file in which configuration will be saved. You can enter any name you want.'),
			'required'	=> true,
		));

		$fieldset->addField('modules', 'multiselect', array(
			'name'		=> 'modules',
			'label'		=> Mage::helper('dataporter')->__('Select Elements of the Configuration to Export'),
			'title'		=> Mage::helper('dataporter')->__('Select Elements of the Configuration to Export'),
			'values'	=> Mage::getModel('dataporter/source_cfgporter_packagemodules')
				->toOptionArray($this->getRequest()->getParam('package')),
			'required'	=> true,
		));

		//IMPORTANT: allow to select only one store per export
		if (!Mage::app()->isSingleStoreMode()) //Check is single store mode
		{
			$fieldStores = $fieldset->addField('store_id', 'select', array(
				'name'		=> 'stores',
				'label'		=> Mage::helper('cms')->__('Configuration Scope'),
				'title'		=> Mage::helper('cms')->__('Configuration Scope'),
				'note'		=> Mage::helper('dataporter')->__('Configuration of selected store will be saved in a file.'),
				'required'	=> true,
				'values'	=> Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(false, true),
			));
			$renderer = $this->getLayout()->createBlock('adminhtml/store_switcher_form_renderer_fieldset_element');
			$fieldStores->setRenderer($renderer);
		}
		else
		{
			$fieldset->addField('store_id', 'hidden', array(
				'name'      => 'stores',
				'value'     => Mage::app()->getStore(true)->getId(),
			));
		}

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
		$actionUrl = $this->getUrl('*/*/export');
		$form->setAction($actionUrl);
		$form->setUseContainer(true);

		$this->setForm($form);
		return parent::_prepareForm();
	}
}