<?php
class SteveB27_GoodReads_Model_Cron extends SteveB27_GoodReads_Model_Review
{
	public function updateAll()
	{
        $attributeIsbn = Mage::getStoreConfig('catalog/goodreads/isbn_attribute');
		$helper = Mage::helper('goodreads');
		$products = Mage::getModel('catalog/product')->getCollection()->addAttributeToSelect($attributeIsbn);
        $products->addAttributeToFilter($attributeIsbn,array("neq"=>""));
        $products->addFieldToFilter("type_id",Mage_Downloadable_Model_Product_Type::TYPE_DOWNLOADABLE);
        Mage::log("Started goodreads import",null,"goodreads.cron.log",true);
		$i = 1;
        $count = $products->count();
        Mage::log("Products count: $count",null,"goodreads.cron.log",true);

        foreach($products as $product) {
		    try{
                $isbn = $product->getData($attributeIsbn);
                if($isbn){
                    $review = $helper->isbnBookReviews($isbn,true,false);
                    if($review){
                        $review->setData("reviews_json","");
                        $review->getReviewsData();
                        Mage::log("Imported product $i/$count #".$product->getId(),null,"goodreads.cron.log",true);
                    }
                }
            }catch(Exception $e){
                Mage::log("Error importing product $i/$count #".$product->getId(),null,"goodreads.cron.log",true);
            }
            $i++;
		}
	}
}