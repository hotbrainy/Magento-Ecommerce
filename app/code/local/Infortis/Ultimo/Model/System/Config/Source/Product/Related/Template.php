<?php

class Infortis_Ultimo_Model_System_Config_Source_Product_Related_Template
{
	public function toOptionArray()
	{
		return array(
			array('value' => 'catalog/product/list/related_tabbed.phtml',
				'label' => Mage::helper('ultimo')->__('Simple slider')),
			array('value' => 'catalog/product/list/related_multi.phtml',
				'label' => Mage::helper('ultimo')->__('Thumbnails slider')),
			/*array('value' => 'catalog/product/list/related.phtml',
				'label' => Mage::helper('ultimo')->__("Simple list (Magento's default template file)")),*/
		);
	}
}