<?php

class Infortis_Ultimo_Model_Import_Cms extends Mage_Core_Model_Abstract
{
	/**
	 * Path to directory with import files
	 *
	 * @var string
	 */
	protected $_importPath;
	
	/**
	 * Create path
	 */
	public function __construct()
    {
        parent::__construct();
		$this->_importPath = Mage::getBaseDir() . '/app/code/local/Infortis/Ultimo/etc/import/';
    }
	
	/**
	 * Import CMS items
	 *
	 * @param string model string
	 * @param string name of the main XML node (and name of the XML file)
	 * @param bool overwrite existing items
	 */
	public function importCmsItems($modelString, $itemContainerNodeString, $overwrite = false)
    {
		try
		{
			$xmlPath = $this->_importPath . $itemContainerNodeString . '.xml';
			if (!is_readable($xmlPath))
			{
				throw new Exception(
					Mage::helper('ultimo')->__("Can't read data file: %s", $xmlPath)
					);
			}
			$xmlObj = new Varien_Simplexml_Config($xmlPath);
			
			$conflictingOldItems = array();
			$i = 0;
			foreach ($xmlObj->getNode($itemContainerNodeString)->children() as $b)
			{
				//Check if block already exists
				$oldBlocks = Mage::getModel($modelString)->getCollection()
					->addFieldToFilter('identifier', $b->identifier) //array('eq' => $b->identifier)
					->load();
				
				//If items can be overwritten
				if ($overwrite)
				{
					if (count($oldBlocks) > 0)
					{
						$conflictingOldItems[] = $b->identifier;
						foreach ($oldBlocks as $old)
							$old->delete();
					}
				}
				else
				{
					if (count($oldBlocks) > 0)
					{
						$conflictingOldItems[] = $b->identifier;
						continue;
					}
				}
				
				Mage::getModel($modelString)
					->setTitle($b->title)
					->setContent($b->content)
					->setIdentifier($b->identifier)
					->setIsActive($b->is_active)
					->setStores(array(0))
					->save();
				$i++;
			}
			
			//Final info
			if ($i)
			{
				Mage::getSingleton('adminhtml/session')->addSuccess(
					Mage::helper('ultimo')->__('Number of imported items: %s', $i)
				);
			}
			else
			{
				Mage::getSingleton('adminhtml/session')->addNotice(
					Mage::helper('ultimo')->__('No items were imported')
				);
			}
			
			if ($overwrite)
			{
				if ($conflictingOldItems)
					Mage::getSingleton('adminhtml/session')->addSuccess(
						Mage::helper('ultimo')
						->__('Items (%s) with the following identifiers were overwritten:<br />%s', count($conflictingOldItems), implode(', ', $conflictingOldItems))
					);
			}
			else
			{
				if ($conflictingOldItems)
					Mage::getSingleton('adminhtml/session')->addNotice(
						Mage::helper('ultimo')
						->__('Unable to import items (%s) with the following identifiers (they already exist in the database):<br />%s', count($conflictingOldItems), implode(', ', $conflictingOldItems))
					);
			}
		}
		catch (Exception $e)
		{
			Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
			Mage::logException($e);
		}
    }
	
}
