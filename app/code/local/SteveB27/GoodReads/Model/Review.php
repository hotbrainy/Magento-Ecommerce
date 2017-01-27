<?php

spl_autoload_register(array('Bubble_Elasticsearch_Model_Autoload', 'load'), false, true);
use PHPHtmlParser\Dom;

class SteveB27_GoodReads_Model_Review extends Mage_Review_Model_Review
{
    const MAX_REVIEWS_COUNT = 25;
    const MAX_REVIEWS_PAGES = 3;

	protected function _construct()
    {
        parent::_construct();
        $this->_init('goodreads/review');
    }
    
	public function getEntitySummary($product, $storeId=0)
    {
		$helper = Mage::helper('goodreads');
		$isbn = $helper->getIsbn($product);
		
		$goodreads = $helper->isbnBookReviews($isbn);
		if($goodreads !== false) {	
			$data = array(
				'entity_pk_value' 	=> $goodreads->getProductId(),
				'entity_type'		=> Mage_Rating_Model_Rating::ENTITY_PRODUCT_CODE,
				'reviews_count'		=> $goodreads->getRatingsCount(),
				'rating_summary'	=> ceil($goodreads->getAverageRating() * 20),
				'store_id'			=> $storeId,
			);
			$summary = new Varien_Object();
			$summary->setData($data);
			$product->setRatingSummary($summary);
		} else {
			parent::getEntitySummary($product, $storeId);
		}
    }
    
    /**
     * Append review summary to product collection
     *
     * @param Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Collection $collection
     * @return Mage_Review_Model_Review
     */
    public function appendSummary($collection,$extraObject = true)
    {
        $entityIds = array();
        foreach ($collection->getItems() as $_itemId => $_item) {
            $entityIds[] = $_item->getEntityId();
        }

        if (sizeof($entityIds) == 0) {
            return $this;
        }

        $summaryData = Mage::getModel('goodreads/review')->getCollection();
        $summaryData->addFieldToFilter('product_id', array('in' => $entityIds));
       

        foreach ($collection->getItems() as $_item ) {
			$foundLocal = false;
            foreach ($summaryData as $_rating) {
                if ($_rating->getProductId() == $_item->getEntityId()) {
					$foundLocal = true;
                    if($extraObject){
                        $data = array(
                            'entity_pk_value' 	=> $_rating->getProductId(),
                            'entity_type'		=> Mage_Rating_Model_Rating::ENTITY_PRODUCT_CODE,
                            'rating_count'		=> $_rating->getRatingsCount(),
                            'rating_summary'	=> ceil($_rating->getAverageRating() * 20),
                        );
                        $_summary = new Varien_Object();
                        $_summary->setData($data);
                        $_item->setRatingSummary($_summary);
                    }else{
                        $_item->setRatingCount($_rating->getRatingsCount());
                        $_item->setRatingSummary(ceil($_rating->getAverageRating() * 20));
                    }
                }
            }
            if(!$foundLocal) {
				$product = Mage::getModel('catalog/product')->load($_item->getId());
				$this->getEntitySummary($product);
			}
        }

        return $this;
    }


    /**
     * Append review summary to product collection
     *
     * @param Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Collection $collection
     * @return Mage_Review_Model_Review
     */
    public function appendRatings($collection)
    {
        $attribute = Mage::getStoreConfig('catalog/goodreads/isbn_attribute');
        $ratingsTable = Mage::getSingleton('core/resource')->getTableName('goodreads/review');
        $collection->getSelect()->joinLeft(array('ratings'=>$ratingsTable),
            'ratings.isbn = TRIM(e.'.$attribute.')',
            array('average_rating'=>'ratings.average_rating','ratings_count'=>'ratings.ratings_count')
        );
        $collection->load();
        return $this;
    }

    public function getReviewsData($page = 1){
        if(!$this->getData("reviews_data")) {
            if($page == 1 && $this->getData("reviews_json")){
                return json_decode($this->getData("reviews_json"),true);
            }
            $widget = $this->getReviewsWidget();
            $iframeDom = new Dom;
            $iframeDom->load($widget);
            $iframe = $iframeDom->find("iframe");
            $iframe = $iframe[0];
            if (isset($iframe)){
                $url = $iframe->getAttribute("src") . "&min_rating=4&num_reviews=" . $this::MAX_REVIEWS_COUNT;
                $dom = new Dom;
                $dom->load($page == 1 ? $url : $url . "&page=" . $page);
                $reviewsDom = $dom->find(".gr_review_container");
                $reviews = [];
                $pages = 1;
                $from = 0;
                $to = 0;
                $total = 0;

                try{
                    $pagination = $dom->find(".gr_reviews_showing .smallText")->text;
                    $paginationNumbers = preg_match_all("/[0-9]+/", str_replace(",","",$pagination), $pagRegexpOutput);
                    $pages = ceil($pagRegexpOutput[0][2] / ($pagRegexpOutput[0][1] - $pagRegexpOutput[0][0] + 1));
                    $from = $pagRegexpOutput[0][0];
                    $to =  $pagRegexpOutput[0][1];
                    $total = $pagRegexpOutput[0][2];
                }catch(Exception $e){

                }

                foreach ($reviewsDom as $reviewDom) {
                    $reviewData = array();
                    $reviewData["date"] = $reviewDom->find(".gr_review_date")[0]->text;
                    $reviewData["body"] = $reviewDom->find(".gr_review_text")[0]->innerHtml;
                    $reviewData["author"] = $reviewDom->find(".gr_review_by a")[0]->text;
                    $reviewData["url"] = $reviewDom->find(".gr_review_by a")[0]->getAttribute("href");
                    $reviewData["rating"] = $reviewDom->find(".gr_rating")[0]->text;
                    $reviewData["stars"] = substr_count($reviewData["rating"],"★");
                    $reviews[] = $reviewData;
                }
                $response = array(
                    "reviews" => $reviews,
                    "from" => $from,
                    "to" => $to,
                    "pages" => $pages,
                    "cur_page" => $page,
                    "total" => $total,
                    "average" => $this->getData("average_rating"),
                );

                $this->setData("reviews_data", $response);
                if($page == 1){
                    $this->setData("reviews_json",json_encode($response));
                    $this->save();
                }
            }

        }

        return $this->getData("reviews_data");
    }

    public function getRatingSummary(){
        return $this->getData("average_rating") * 20;
    }


}