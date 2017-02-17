<?php

$installer = $this;
$installer->startSetup();


/* Brand module: */

	//For backward compatibility set underscore as image URL key separator.
	//In previous versions of brand module this was the default separator in image file names.
	Mage::getConfig()->saveConfig('brands/general/img_url_key_separator', '_');

	/* List of options differs from previous version. To upgrade seamlessly, set previous values of options. */

		//Option: show brand image or text. Option was moved to other tab.
		$prevShowImage = Mage::getStoreConfig('brands/product_view/show_image');
		Mage::getConfig()->saveConfig('brands/general/show_image', $prevShowImage);

		//If "All Brands" was NOT selected, it means: display only brands which are currently assigned to products.
		//Modify the default values of options (list of options differs from previous version).
		$prevAllBrands = Mage::getStoreConfig('brands/slider/all_brands');
		if (!$prevAllBrands)
		{
			Mage::getConfig()->saveConfig('brands/list/all_brands',	'1');
			Mage::getConfig()->saveConfig('brands/list/brands',		'');
			Mage::getConfig()->saveConfig('brands/list/assigned',	'1');
		}

		//Option: brand logo is a link to search results. Option was changed and extended.
		$prevBrandIsLink = intval(Mage::getStoreConfig('brands/general/link_search_enabled'));
		$prevPageBasePath = Mage::getStoreConfig('brands/general/page_base_path');

		if ($prevBrandIsLink === 0)
		{
			if ($prevPageBasePath === '') //Base path was empty
			{
				//Brand logo is not a link
				Mage::getConfig()->saveConfig('brands/general/link_search_enabled', 3);
			}
		}


$installer->endSetup();
