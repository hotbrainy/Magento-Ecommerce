<?php
class SteveB27_GoodReads_Block_Review_Product_View_List extends Mage_Review_Block_Product_View_List
{
	protected function _beforeToHtml()
	{
        parent::_construct();
		$this->setTemplate('goodreads/review_list.phtml');
	}

	public function getGoodreadsReview(){
	    return Mage::registry("current_goodreads_review");
    }

    public function getAjaxUrl(){
        $goodreadsReview = $this->getGoodreadsReview();
        $product = Mage::registry("product");

        return $this->getUrl("goodreads/ajax/reviews",array("isbn"=>$goodreadsReview->getIsbn(),"id"=>$product->getId()));
    }
}