<?php

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */
if (method_exists('Mage', 'getEdition')) { // CE 1.7+, EE 1.12+

    if (Mage::helper('amshopby')->useSolr()) {
        class Amasty_Shopby_Model_Catalog_Layer_Filter_Price_Price17ce_Parent extends Enterprise_Search_Model_Catalog_Layer_Filter_Price {};
    }
    else {
        class Amasty_Shopby_Model_Catalog_Layer_Filter_Price_Price17ce_Parent extends Mage_Catalog_Model_Layer_Filter_Price {};
    }
    
    class Amasty_Shopby_Model_Catalog_Layer_Filter_Price_Adapter extends Amasty_Shopby_Model_Catalog_Layer_Filter_Price_Price17ce
    {
    }
} 
else { // CE 1.3.2 - 1.6.2 

    class Amasty_Shopby_Model_Catalog_Layer_Filter_Price_Adapter extends Amasty_Shopby_Model_Catalog_Layer_Filter_Price_Price14ce
    {
    }
}
