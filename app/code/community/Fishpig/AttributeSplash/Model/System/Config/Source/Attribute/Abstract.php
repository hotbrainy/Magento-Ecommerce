<?php
/**
 * @category    Fishpig
 * @package     Fishpig_AttributeSplash
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

abstract class Fishpig_AttributeSplash_Model_System_Config_Source_Attribute_Abstract
{
	/**
	 * Cache for options
	 *
	 * @param
	 */
	protected $_options = null;
	
	/**
	 * Cache for collection
	 *
	 * @param
	 */
	protected $_collection = null;
	
	/**
	 * Cache for attribute options
	 *
	 * @var array
	 */
	protected $_attributes = null;
	
	/**
	 * Field name used for the label value of the options
	 *
	 * @var string
	 */
	protected $_labelField = 'frontend_label';
	
	/**
	 * Generate, cache and retrieve the collection
	 *
	 * @return
	 */
	abstract public function getCollection();

	/**
	 * Set the label field
	 *
	 * @param string $field
	 * @return $this
	 */
	public function setLabelField($field)
	{
		if ($this->_labelField !== $field) {
			$this->_options = null;
		}

		$this->_labelField = $field;
		
		return $this;
	}

	/**
	 * Retrieve an option array of results
	 *
	 * @return array
	 */
	public function toOptionArray($includeEmpty = false)
	{
		if (is_null($this->_options)) {
			foreach($this->getCollection() as $attribute) {
				$this->_options[] = array(
					'value' => $attribute->getAttributeId(),
					'label' => $attribute->getData($this->_labelField),
				);
			}
		}
		
		if ($includeEmpty) {
			array_unshift($this->_options, array('value' => '', 'label' => Mage::helper('adminhtml')->__('-- Please Select --')));
			
		}

		return (array)$this->_options;
	}
	
	/**
	 * Retrieve an option hash
	 *
	 * @return array
	 */
	public function toOptionHash()
	{
		$hash = array();
		
		foreach($this->toOptionArray() as $option) {
			$hash[$option['value']] = $option['label'];
		}
		
		return $hash;
		
	}
}
