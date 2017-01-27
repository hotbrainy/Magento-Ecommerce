<?php

//Add allowed blocks to Magento's white list
$version = Mage::getConfig()->getModuleConfig('Mage_Admin')->version;
if (version_compare($version, '1.6.1.2', '>=')) 
{
	$blockNames = array(
		'cms/block',
		'catalog/product_list',
		'ultimo/product_list_featured',
		'ultraslideshow/slideshow',
		'brands/brands',
		'ultramegamenu/navigation',
	);
	
	foreach ($blockNames as $name)
	{
		try
		{
			Mage::getModel('admin/block')->setData('block_name', $name)
				->setData('is_allowed', 1)
				->save();
		}
		catch (Exception $e)
		{
			Mage::log($e->getMessage());
		}
	}

	Mage::log("Successfully added allowed blocks to Magento's white list. Required for compatibility with Magento 1.9.2.2 and later.",
		null, "Infortis_Ultimo.log");
}
