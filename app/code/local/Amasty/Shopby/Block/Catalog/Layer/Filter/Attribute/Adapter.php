<?php

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */
if (Mage::helper('amshopby')->useSolr()) {
    class Amasty_Shopby_Block_Catalog_Layer_Filter_Attribute_Adapter extends Enterprise_Search_Block_Catalog_Layer_Filter_Attribute {}
} else {
    class Amasty_Shopby_Block_Catalog_Layer_Filter_Attribute_Adapter extends Mage_Catalog_Block_Layer_Filter_Attribute {}
}