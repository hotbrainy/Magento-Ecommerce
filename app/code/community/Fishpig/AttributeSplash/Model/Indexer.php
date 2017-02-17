<?php
/**
 * @category    Fishpig
 * @package     Fishpig_AttributeSplash
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_AttributeSplash_Model_Indexer extends Mage_Index_Model_Indexer_Abstract
{
	/**
	 *
	 * @var array
	 */
	protected $_matchedEntities = array(
		'attributeSplash_page' => array(
			Mage_Index_Model_Event::TYPE_SAVE
		),
		'attributeSplash_group' => array(
			Mage_Index_Model_Event::TYPE_SAVE
		)
	);
	
	/**
	 * Get the indexer name
	 *
	 * @return string
	 */
	public function getName()
	{
		return Mage::helper('core')->__('Attribute Splash');
	}

	/**
	 * Get the indexer description
	 *
	 * @return string
	 */	
	public function getDescription()
	{
		return Mage::helper('core')->__('Attribute Splash page/group/store combination data.');
	}
	
	/**
	 * Abstract methods. required
	 *
	 * @return void
	 */
	protected function _registerEvent(Mage_Index_Model_Event $event)	{}
	protected function _processEvent(Mage_Index_Model_Event $event)	{}
	
	/**
	 * Reindex all Splash entities
	 *
	 * @return $this
	 */
	public function reindexAll()
	{
		Mage::getResourceModel('attributeSplash/group')->reindexAll();
		Mage::getResourceModel('attributeSplash/page')->reindexAll();
	}
}
