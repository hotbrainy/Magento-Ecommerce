<?php
 /**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */

class Amasty_Shopby_Model_Catalog_Layer_Filter_Rating extends Mage_Catalog_Model_Layer_Filter_Abstract
{
	const NOT_RATED_LABEL = 'Not Yet Rated';

    /**
     * Class constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->_requestVar = 'rating';
    }

    protected $stars = array(
        1 => 20,
        2 => 40,
        3 => 60,
        4 => 80,
        5 => 100,
		6 => -1
    );

    /**
     * Apply category filter to layer
     *
     * @param   Zend_Controller_Request_Abstract $request
     * @param   Mage_Core_Block_Abstract $filterBlock
     * @return  Mage_Catalog_Model_Layer_Filter_Category
     */
    public function apply(Zend_Controller_Request_Abstract $request, $filterBlock)
    {
        $filter = (int) $request->getParam($this->getRequestVar());
        if (!$filter || Mage::registry('am_rating_filter')) {
            return $this;
        }

        $collection = $this->getLayer()->getProductCollection();
        $select = $collection->getSelect();

        $minRating = (array_key_exists($filter, $this->stars))
            ? $this->stars[$filter]
            : 0;

        $reviewSummary = $collection->getResource()->getTable('review/review_aggregate');
        $select->joinLeft(
            array('rating' => $reviewSummary),
            sprintf('`rating`.`entity_pk_value`=`e`.entity_id
                    AND `rating`.`entity_type` = 1
                    AND `rating`.`store_id`  =  %d',
                Mage::app()->getStore()->getId()
            ),
            ''
        );
		if($minRating == "-1") {
			$select->where('`rating`.`rating_summary` IS NULL');
		} else {
			$select->where('`rating`.`rating_summary` >= ?',
				$minRating);
		}

        $state = $this->_createItem($this->getLabelHtml($filter), $filter)
                      ->setVar($this->_requestVar);

        $this->getLayer()->getState()->addFilter($state);

        Mage::register('am_rating_filter', true);

        return $this;
    }

    /**
     * Get filter name
     *
     * @return string
     */
    public function getName()
    {
        return Mage::helper('amshopby')->__('Rating Filter');
    }

    /**
     * Get data array for building category filter items
     *
     * @return array
     */
    protected function _getItemsData()
    {
        /** @var Amasty_Shopby_Helper_Layer_Cache $cache */
        $cache = Mage::helper('amshopby/layer_cache');
        $cache->setStateKey($this->getLayer()->getStateKey());
        $key = 'RATING';
        $data = $cache->getFilterItems($key);

        if (is_null($data)) {
            $data = array();
            $count = $this->_getCount();
            $currentValue = Mage::app()->getRequest()->getQuery($this->getRequestVar());

            for ($i = 5; $i >= 1; $i--) {
                $data[] = array(
                    'label' => $this->getLabelHtml($i),
                    'value' => ($currentValue == $i) ? null : $i,
                    'count' => $count[($i - 1)],
                    'real_count' => ((isset($count[$i]) && $i != 5 ? $count[$i] : 0) - $count[($i - 1)]),
                    'option_id' => $i,
                );
            }
			$data[] = array(
				'label' => $this->getLabelHtml(6),
				'value' => ($currentValue == 6) ? null : 6,
				'count' => $count[5],
				'real_count' => $count[5],
				'option_id' => 6,
			);
            $cache->setFilterItems($key, $data);
        }

        return $data;
    }

    /**
     * @return array
     */
    protected function _getCount()
    {
        $collection = $this->getLayer()->getProductCollection();

        $connection = $collection->getConnection();
        $connection
            ->query('SET @ONE :=0, @TWO := 0, @THREE := 0, @FOUR := 0, @FIVE := 0, @NOT_RATED := 0');

        $select = clone $collection->getSelect();
        $select->reset(Zend_Db_Select::COLUMNS);
        $select->reset(Zend_Db_Select::ORDER);
        $select->reset(Zend_Db_Select::LIMIT_COUNT);
        $select->reset(Zend_Db_Select::LIMIT_OFFSET);
        $where = $select->getPart(Zend_Db_Select::WHERE);
        foreach($where as $key=>$part) {
            if(strpos($part, "rating_summary") !== false) {
                if($key == 0) {
                    $where[$key] = "1";
                } else {
                    unset($where[$key]);
                }

            }
        }
        $select->setPart(Zend_Db_Select::WHERE, $where);

        $reviewSummary = $collection->getResource()->getTable('review/review_aggregate');
        $select->joinLeft(
            array('rsc' => $reviewSummary),
            sprintf('`rsc`.`entity_pk_value`=`e`.entity_id
                AND `rsc`.entity_type = 1
                AND `rsc`.store_id  =  %d',
                Mage::app()->getStore()->getId()),
            array('e.entity_id','rsc.rating_summary')
        );

        $select2 = new Varien_Db_Select($connection);

        $select2->from($select);
        $select = $select2;

        $columns = new Zend_Db_Expr("
            IF(`t`.`rating_summary` >= 20, @ONE := @ONE + 1, 0),
            IF(`t`.`rating_summary` >= 40, @TWO := @TWO + 1, 0),
            IF(`t`.`rating_summary` >= 60, @THREE := @THREE + 1, 0),
            IF(`t`.`rating_summary` >= 80, @FOUR := @FOUR + 1, 0),
            IF(`t`.`rating_summary` >= 100, @FIVE := @FIVE + 1, 0),
            IF(`t`.`rating_summary` IS NULL, @NOT_RATED := @NOT_RATED + 1, 0)
        ");
        $select->columns($columns);
        $connection->query($select);
        $result = $connection->fetchRow('SELECT @ONE, @TWO, @THREE, @FOUR, @FIVE, @NOT_RATED;');
        return array_values($result);
    }

    protected function _initItems()
    {
        $data  = $this->_getItemsData();
        $items = array();
        foreach ($data as $itemData) {
            $item = $this->_createItem(
                $itemData['label'],
                $itemData['value'],
                $itemData['count']
            );
            $item->setOptionId($itemData['option_id']);
            $item->setRealCount($itemData['real_count']);
            $items[] = $item;
        }
        $this->_items = $items;
        return $this;
    }

    /**
     * @param int $countStars
     *
     * @return string
     */
    protected function getLabelHtml($countStars)
    {
		if($countStars == 6) {
			return Mage::helper('amshopby')->__(self::NOT_RATED_LABEL);
		}
        $block = new Mage_Core_Block_Template();
        $block->setStar($countStars);
        $html = $block->setTemplate('amasty/amshopby/rating.phtml')->toHtml();
        return $html;
    }

}