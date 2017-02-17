<?php
class SteveB27_GoodReads_Helper_Data extends Mage_Core_Helper_Abstract
{
	public function key()
	{
		return Mage::getStoreConfig('catalog/goodreads/api_key');
	}
	
	public function secret()
	{
		return Mage::getStoreConfig('catalog/goodreads/api_secret');
	}
	
	public function isbnBookReviews($isbn,$update=false,$returnIfIsFound = false)
	{
		if(!is_numeric($isbn)) {
			return false;
		}
		
		$found = false;
		$goodreads = Mage::getModel('goodreads/review')->load($isbn,'isbn');
		if($goodreads->getId() != false) {
			$found = $goodreads;
		}
		
		if((!$update AND ($found !== false)) || ($found && $returnIfIsFound)) {
			return $found;
		}
        $goodreads = Mage::getModel('goodreads/review')->load($isbn,'isbn');

        $url = sprintf('https://www.goodreads.com/book/isbn/%s/?key=%s', $isbn, $this->key());

		$xmlResponse = file_get_contents($url);
		$xml = simplexml_load_string($xmlResponse);
		$book = $xml->book;
		$data = array(
			'product_id'		=> $this->getProduct($isbn),
			'goodreads_id'		=> (string)$book->id,
			'isbn'				=> (string)$book->isbn13,
			'average_rating'	=> $book->average_rating,
			'ratings_count'		=> $book->work->ratings_count,
			'text_reviews_count'=> $book->work->text_reviews_count,
			'reviews_widget'	=> (string)$book->reviews_widget,
		);
		
		$reviews = Mage::getModel('goodreads/review');
		$reviews->setData($data);
		if($found) {
			$reviews->setReviewId($found->getId());
            $reviews->setReviewsJson("");
		}
		$reviews->save();
		
		return $reviews;
	}
	
	public function getProduct($isbn)
	{
		$attribute = Mage::getStoreConfig('catalog/goodreads/isbn_attribute');
		$collection = Mage::getModel('catalog/product')->getCollection();
		$collection->addAttributeToFilter($attribute,$isbn);
		//$product->getFirstItem(); // bug
		foreach($collection as $product) {
			return $product->getId();
		}
	}
	
	public function getIsbn($product)
	{
		$attribute = Mage::getStoreConfig('catalog/goodreads/isbn_attribute');
		if(is_numeric($product)) {
			$product = Mage::getModel('catalog/product')->load($product);
		}
		
		$isbn = $product->getData($attribute);
		if(!$isbn) {
			return false;
		}
		
		return trim($isbn);
	}

	public function getRating($product)
    {
        $isbn = $this->getIsbn($product);
        $review = $this->isbnBookReviews($isbn);
        return $review->average_rating;
    }
}