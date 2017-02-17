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

class Infortis_Dataporter_Model_Source_Cfgporter_Packagepresets
{
	protected $_options;

	public function toOptionArray($package = NULL)
	{
		if (!$this->_options)
		{
			$this->_options = array();
			$this->_options[] = array('value' => '', 'label' => Mage::helper('dataporter')->__('-- Please Select --')); //First option is empty

			$dir = Mage::helper('dataporter/cfgporter_data')->getPresetDir($package);
			if (is_dir($dir))
			{
				$files = scandir($dir);
				foreach ($files as $file)
				{
					if (!is_dir($dir . $file))
					{
						$path = pathinfo($file);
						$this->_options[] = array('value' => $path['filename'], 'label' => $path['filename']);
					}
				}
			}

			//Last option
			$this->_options[] = array('value' => 'upload_custom_file', 'label' => Mage::helper('dataporter')->__('Upload custom file...'));
		}
		return $this->_options;
	}
}
