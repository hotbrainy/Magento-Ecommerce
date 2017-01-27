<?php
require_once "Mage/Catalog/controllers/CategoryController.php";
/**
 * Entangled Rest Category controller
 */
class Entangled_RestCatalog_CategoryController extends Mage_Catalog_CategoryController
{
    /**
     * Category view action
     */
    public function viewAction(){
        $isAJAX = Mage::app()->getRequest()->getParam('is_ajax', false);
        // $isAJAX = $isAJAX && Mage::app()->getRequest()->isXmlHttpRequest();
        if (!$isAJAX){
            $this->_forward('view','category','catalog',Mage::app()->getRequest()->getParams());
            return;
        }
        $attribute = Mage::getModel('eav/config')->getAttribute('catalog_product', 'book_genre');
        foreach ( $attribute->getSource()->getAllOptions(true, true) as $option){
            if ($option['value'] != '')
            {
                $genreArray[] = array('id' => $option['value'], 'label' => $option['label']);
            }
        }        

        $attribute = Mage::getModel('eav/config')->getAttribute('catalog_product', 'book_trope');
        foreach ( $attribute->getSource()->getAllOptions(true, true) as $option){
            if ($option['value'] != '')
            {
                $tropeArray[] = array('id' => $option['value'], 'label' => $option['label']);
            }
        }

        if ($category = $this->_initCatagory()) {
            $jsonArray = array('books' => '', 'filter' => '', 'total_results' => '', 'returned_results' => '', 'page' => '1');
            Mage::getSingleton('catalog/session')->setLastViewedCategoryId($category->getId());
            $this->loadLayout();
            $this->getLayout()->getBlock('product_list')->applySortWithoutRendering();
            $_productCollection = $this->getLayout()->getBlock('product_list')->getLoadedProductCollection();
            Mage::getModel('goodreads/review')->appendSummary($_productCollection,false);

            $productArray = $_productCollection->exportToArray();
            $newProductArray = array();
            $newKey = 0;
            $_product = Mage::getModel('catalog/product');
            foreach($productArray AS $key => $product){
                $_product->setData($product);
                unset($product['long_description']);
                unset($product['praise']);
                $product["medium_description"] = $product["short_description"];
                $product["short_description"] = Mage::helper("entangled_custom")->getShortDesc($product["short_description"]);
                $product["small_image"] = (string) Mage::helper('infortis/image')->getImg($_product, 165, 247, 'small_image');
                if (isset($_GET["mode"]) && $_GET["mode"] == "list"){
                    $product["author"] = Mage::helper('publish/author')->getAuthorsList($product['entity_id'],false);
                }
                if (strtotime($product["news_from_date"]) < time() && strtotime($product["news_to_date"]) > time()){
                    $product["new"] = true;
                }else{
                    $product["new"] = false;
                }
                if (strtotime($product["special_from_date"]) < time() && strtotime($product["special_to_date"]) > time()){
                    $product["on_sale"] = true;
                }else{
                    $product["on_sale"] = false;
                }
                $newProductArray[$newKey] = $product;
                $newKey++;
            }
    
            $jsonArray['books'] = $newProductArray;
            $jsonArray['returned_results'] = sizeof($newProductArray);
            if (isset($_GET['book_imprint']) && $_GET['book_imprint'] != ""){
                $imprints = explode(",", $_GET['book_imprint']);
                $attributeArray = array();
                foreach ($imprints as $imprint_id) {
                    $attributeArray[] = array('attribute' => 'book_imprint', 'like' => $imprint_id);
                }
                $_myProductCollection = Mage::getResourceModel('catalog/product_collection')->addAttributeToSelect('*')->addAttributeToFilter($attributeArray)->load();
            } else {
                $_myProductCollection = Mage::getResourceModel('catalog/product_collection')->addAttributeToSelect('*')->load();
            }
            $myProductArray = $_myProductCollection->exportToArray();
            $jsonArray['total_results'] = count($myProductArray);

            // Genres
            for ($i = 0; $i < sizeof($genreArray); $i++){
                $count = 0;
                foreach($myProductArray AS $product){
                    $genres = explode(',', $product['book_genre']);
                    for($j = 0; $j < sizeof($genres); $j++){
                        if($genres[$j] == $genreArray[$i]['id']){
                            $count++;
                        }
                    }
                    
                }

                $jsonArray['filter'][$genreArray[$i]['id']] = array(
                        'id' => $genreArray[$i]['id'],
                        'label' => $genreArray[$i]['label'],
                        'count' => $count
                        );
     
            }

            // Tropes
            for ($i = 0; $i < sizeof($tropeArray); $i++){
                
                $count = 0;
                foreach($myProductArray AS $product){
                    $tropes = explode(',', $product['book_trope']);
                    for($j = 0; $j < sizeof($tropes); $j++){
                        if($tropes[$j] == $tropeArray[$i]['id']){
                            $count++;
                        }
                    }
                    
                }

                $jsonArray['filter'][$tropeArray[$i]['id']] = array(
                        'id' => $tropeArray[$i]['id'],
                        'label' => $tropeArray[$i]['label'],
                        'count' => $count
                        );
            }

            if (isset($_GET["p"])){
                $jsonArray["page"] = intval($_GET["p"]);
            }
            $jsonArray["banner"] = Mage::getSingleton('core/layout')->createBlock('bannerslider/default')->setTemplate('bannerslider/bannerslider.phtml')->setBannersliderId(3)->toHtml();
            $test = (string)$_productCollection->getSelect();
            $this->getResponse()->clearHeaders()->setHeader('Content-type','application/json',true);
            return $this->getResponse()->setBody(json_encode($jsonArray));
        } elseif (!$this->getResponse()->isRedirect()) {
            $this->_forward('noRoute');
        }
    }
}
