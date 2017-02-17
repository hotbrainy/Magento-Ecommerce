<?php

class Entangled_Custom_Block_Home_Imprints extends Mage_Core_Block_Template {

    const IMPRINTS_NAME = "Imprints";

    protected $_template = "entangled/custom/home/imprints.phtml";

    public function getImprintsData(){
        /** @var Mage_Catalog_Model_Category $category */
        $category = Mage::getModel("catalog/category")->loadByAttribute("name",self::IMPRINTS_NAME);
        $childrenCategories = $category->getChildrenCategories();
        $categories = array_slice($childrenCategories,0,12);
        /** @var Mage_Catalog_Model_Category $childCategory */
        $categoriesWithDesc = array();
        foreach($categories as $childCategory){
            $newCategory = Mage::getModel('catalog/category')->load($childCategory->getId());
            $categoryProducts = $childCategory->getProductCollection();
            $categoryProducts
                ->setOrder("created_at")
                ->setPageSize(3)
                ->addAttributeToSelect(array("small_image","created_at","name"));
            $newCategory->setProducts($categoryProducts);
            array_push($categoriesWithDesc, $newCategory);
        }

        return $categoriesWithDesc;
    }
}