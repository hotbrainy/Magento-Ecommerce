<?php

class Entangled_Custom_Block_Home_Genres extends Mage_Core_Block_Template {

    const GENRES_NAME = "Genres";

    protected $_template = "entangled/custom/home/genres.phtml";

    protected function _construct(){
        parent::_construct();

        $this->setCacheLifetime(3600);
    }

    public function getGenresData(){
        /** @var Mage_Catalog_Model_Category $category */
        $category = Mage::getModel("catalog/category")->loadByAttribute("name",self::GENRES_NAME);
        $childrenCategories = $category->getChildrenCategories();
        $categories = array_slice($childrenCategories,0,10);
        /** @var Mage_Catalog_Model_Category $childCategory */
        $categoriesWithDesc = array();
        foreach($categories as $childCategory){
            $newCategory = Mage::getModel('catalog/category')->load($childCategory->getId());
            $categoryProducts = $childCategory->getProductCollection();
            $categoryProducts->addAttributeToSelect(array("small_image","created_at","name"));
            $categoryProducts->getSelect()->order(new Zend_Db_Expr('RAND()'));
            $categoryProducts->setPageSize(1);
            $newCategory->setProducts($categoryProducts);
            array_push($categoriesWithDesc, $newCategory);
        }

        return $categoriesWithDesc;
    }
}