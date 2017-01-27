<?php

class Entangled_Custom_Block_Rewrite_Review_Helper extends Mage_Review_Block_Helper {

    protected $_goodreadsData = array();

    public function getRatingSummary()
    {
        $goodreadsData = $this->_getGoodreadsData();
        $goodreadsData["average"] = is_array($goodreadsData["average"]) ?
            array_pop($goodreadsData["average"]) : $goodreadsData["average"];

        return $goodreadsData ? $goodreadsData["average"] / 5 * 100 : 0;
    }

    public function getReviewsCount()
    {
        $goodreadsData = $this->_getGoodreadsData();

        return $goodreadsData ? $goodreadsData["total"] : 0;
    }

    public function getRatingsCount()
    {
        $goodreadsData = $this->_getGoodreadsData();

        return $goodreadsData ? $goodreadsData["ratings_count"] : 0;
    }

    protected function _getGoodreadsData(){
        $id = $this->getProduct()->getId();
        if(!isset($this->_goodreadsData[$id])) {
            $helper = Mage::helper('goodreads');
            $isbn = $helper->getIsbn($this->getProduct());
            $review = $helper->isbnBookReviews($isbn);
            if(!$review){
                return false;
            }
            try{
                $this->_goodreadsData[$id] = $review->getReviewsData();
                $this->_goodreadsData[$id]['ratings_count'] = $review->ratings_count;
            }catch(Exception $e){
                Mage::log("Error importing product #".$id,null,"goodreads.cron.log",true);
            }
        }

        return $this->_goodreadsData[$id];
    }

}