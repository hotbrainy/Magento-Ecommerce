<?php
/**
 * Query default operator configuration
 *
 * @category    Bubble
 * @package     Bubble_Elasticsearch
 * @version     4.1.2
 * @copyright   Copyright (c) 2016 BubbleShop (https://www.bubbleshop.net)
 */
class Bubble_Elasticsearch_Model_System_Config_Source_Query_Operator
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value' => 'OR', 'label' => Mage::helper('elasticsearch')->__('OR')),
            array('value' => 'AND', 'label' => Mage::helper('elasticsearch')->__('AND')),
        );
    }
}