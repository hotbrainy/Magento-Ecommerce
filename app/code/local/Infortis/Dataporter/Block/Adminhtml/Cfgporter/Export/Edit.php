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

class Infortis_Dataporter_Block_Adminhtml_Cfgporter_Export_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
	/**
	 * Constructor
	 */
	public function __construct()
	{
		parent::__construct();

		$this->_blockGroup = 'dataporter';
		$this->_controller = 'adminhtml_cfgporter_export';
		$this->_headerText = Mage::helper('dataporter')->__('Export Configuration');

		$this->_updateButton('save', 'label', Mage::helper('dataporter')->__('Export Configuration'));
		$this->_updateButton('save', 'style', 'background-image:none; border-color:#1986B1; background-color:#1EB5F0;');
		$this->_updateButton('save', 'class', '');

		$this->_removeButton('back');
		$this->_updateButton('reset', 'label', Mage::helper('dataporter')->__('Reset Form'));
	}
}
