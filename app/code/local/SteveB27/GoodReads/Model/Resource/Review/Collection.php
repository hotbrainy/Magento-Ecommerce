<?php
class SteveB27_GoodReads_Model_Resource_Review_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
	/**
     * Define module
     *
     */
    protected function _construct()
    {
        $this->_init('goodreads/review');
	}

    /**
     * Add entity filter
     *
     * @param int|string $entity
     * @param int $pkValue
     * @return Mage_Review_Model_Resource_Review_Collection
     */
    public function addEntityFilter($entity, $pkValue)
    {
        if (is_numeric($entity)) {
            $this->addFilter('entity',
                $this->getConnection()->quoteInto('main_table.review_id=?', $entity),
                'string');
        }

        $this->addFilter('entity_pk_value',
            $this->getConnection()->quoteInto('main_table.product_id=?', $pkValue),
            'string');

        return $this;
    }

    /**
     * Add status filter
     *
     * @param int|string $status
     * @return Mage_Review_Model_Resource_Review_Collection
     */
    public function addStatusFilter($status)
    {
		/* *****************
		 * TODO: Add status column
        if (is_string($status)) {
            $statuses = array_flip(Mage::helper('review')->getReviewStatuses());
            $status = isset($statuses[$status]) ? $statuses[$status] : 0;
        }
        if (is_numeric($status)) {
            $this->addFilter('status',
                $this->getConnection()->quoteInto('main_table.status_id=?', $status),
                'string');
        }
        */
        return $this;
    }

    /**
     * Set date order
     *
     * @param string $dir
     * @return Mage_Review_Model_Resource_Review_Collection
     */
    public function setDateOrder($dir = 'DESC')
    {
        $this->setOrder('main_table.timestamp', $dir);
        return $this;
    }

    /**
     * Add store filter
     *
     * @param int|array $storeId
     * @return Mage_Review_Model_Resource_Review_Collection
     */
    public function addStoreFilter($storeId)
    {
		/* *************************
		 * TODO: Add store column
        $inCond = $this->getConnection()->prepareSqlCondition('store.store_id', array('in' => $storeId));
        $this->getSelect()->join(array('store'=>$this->_reviewStoreTable),
            'main_table.review_id=store.review_id',
            array());
        $this->getSelect()->where($inCond);
        */
        return $this;
    }

    /**
     * Add rate votes
     *
     * @return Mage_Review_Model_Resource_Review_Collection
     */
    public function addRateVotes()
    {
		/*
        foreach ($this->getItems() as $item) {
            $votesCollection = Mage::getModel('rating/rating_option_vote')
                ->getResourceCollection()
                ->setReviewFilter($item->getId())
                ->setStoreFilter(Mage::app()->getStore()->getId())
                ->addRatingInfo(Mage::app()->getStore()->getId())
                ->load();
            $item->setRatingVotes($votesCollection);
        }
		*/
        return $this;
    }
}