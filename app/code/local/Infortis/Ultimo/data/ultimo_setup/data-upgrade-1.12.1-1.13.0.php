<?php

$installer = $this;
$installer->startSetup();



//In this version there are new options to set position of the search box in the header.
//Update the fields based on values configured in previous versions.
$oldSearchPosition = Mage::getStoreConfig('ultimo/header/search_position');
$newSearchPosition = NULL;
$newSearchInUserMenuPosition = NULL;
$makeRightColumnWide = FALSE;

switch ($oldSearchPosition)
{
	//Search box is displayed in the central column
	case '20':
		$newSearchPosition = 'primCentralCol';
		break;

	//In other cases, search box is displayed in the right column inside the User Menu (in different positions)
	//and the right column is wide (it spans the central column which is not displayed).
	case '30':
		$newSearchPosition = 'userMenu';
		$newSearchInUserMenuPosition = '1';
		$makeRightColumnWide = TRUE;
		break;
	case '31':
		$newSearchPosition = 'userMenu';
		$newSearchInUserMenuPosition = '2';
		$makeRightColumnWide = TRUE;
		break;
	case '32':
		$newSearchPosition = 'userMenu';
		$newSearchInUserMenuPosition = '3';
		$makeRightColumnWide = TRUE;
		break;
	case '33':
		$newSearchPosition = 'userMenu';
		$newSearchInUserMenuPosition = '4';
		$makeRightColumnWide = TRUE;
		break;
}

if ($newSearchPosition !== NULL)
{
	Mage::getConfig()->saveConfig('ultimo/header/search_position', $newSearchPosition);
}
if ($newSearchInUserMenuPosition !== NULL)
{
	Mage::getConfig()->saveConfig('ultimo/header/search_in_user_menu_position', $newSearchInUserMenuPosition);
}
if ($makeRightColumnWide)
{
	Mage::getConfig()->saveConfig('ultimo/header/left_column',		4);
	Mage::getConfig()->saveConfig('ultimo/header/central_column',	' ');
	Mage::getConfig()->saveConfig('ultimo/header/right_column',		8);
}



Mage::getSingleton('ultimo/cssgen_generator')->generateCss('grid',   NULL, NULL);
Mage::getSingleton('ultimo/cssgen_generator')->generateCss('layout', NULL, NULL);
Mage::getSingleton('ultimo/cssgen_generator')->generateCss('design', NULL, NULL);



$installer->endSetup();
