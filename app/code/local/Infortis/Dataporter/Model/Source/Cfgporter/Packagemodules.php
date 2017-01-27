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

class Infortis_Dataporter_Model_Source_Cfgporter_Packagemodules
{
	protected $_options;

	public function toOptionArray($package = NULL)
	{
		if (!$this->_options)
		{
			$this->_options = array();
			$this->_options[] = array('value' => '', 'label' => Mage::helper('dataporter')->__('-- Please Select --')); //First option is empty

			if (NULL !== $package)
			{
				$h = Mage::helper('dataporter/cfgporter_data');
				$modules = $h->getPackageModules($package);
				if ($modules)
				{
					$moduleNames = $h->getModuleNames();
					foreach ($modules as $mod)
					{
						$this->_options[] = array('value' => $mod, 'label' => $moduleNames[$mod]);
					}
				}
			}
			else
			{
				$modulesFromConfig = (array) Mage::getConfig()->getNode('modules')->children();
				$modules = array_keys($modulesFromConfig);
				sort($modules);
				foreach ($modules as $mod)
				{
					$this->_options[] = array('value' => $mod, 'label' => $mod);
				}
			}
		}
		return $this->_options;
	}
}
