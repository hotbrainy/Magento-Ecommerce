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

class Infortis_Dataporter_Helper_Data extends Mage_Core_Helper_Abstract
{
	const FILE_TOP_LEVEL_DIR	= 'dataporter';
	const FILE_MAIN_DIR			= 'importexport';

	/**
	 * File path elements
	 *
	 * @var string
	 */
	protected $_tmpFileBaseDir; //Desitnation directory for files uploaded via form

	/**
	 * Resource initialization
	 */
	public function __construct()
	{
		$this->_tmpFileBaseDir = Mage::getBaseDir('media') . DS . 'tmp' . DS . self::FILE_TOP_LEVEL_DIR . DS;
	}

	/**
	 * Get desitnation directory for files uploaded via form
	 *
	 * @return string
	 */
	public function getTmpFileBaseDir()
	{
		return $this->_tmpFileBaseDir;
	}

}
