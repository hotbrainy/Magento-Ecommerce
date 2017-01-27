<?php
/**
 * @category    Bubble
 * @package     Bubble_Elasticsearch
 * @version     4.1.2
 * @copyright   Copyright (c) 2016 BubbleShop (https://www.bubbleshop.net)
 */
class Bubble_Elasticsearch_Autocomplete
{
    /**
     * @var Bubble_Elasticsearch_Config
     */
    protected $_config;

    /**
     * @param Bubble_Elasticsearch_Config $config
     */
    public function __construct(Bubble_Elasticsearch_Config $config)
    {
        $this->_config = $config;
    }

    /**
     * @param $q
     * @param Bubble_Elasticsearch_Index $index
     * @return string
     */
    public function search($q, Bubble_Elasticsearch_Index $index)
    {
        $autocomplete = new Bubble_Elasticsearch_Block_Autocomplete_Result($q, $this->_config);
        $limit = (int) $this->_config->getConfig('autocomplete/limit', 5);

        foreach ($this->_config->getTypes() as $type => $settings) {
            $type = new Bubble_Elasticsearch_Type($index, $type);
            $type->setIndexProperties($settings['index_properties']);
            $type->setAdditionalFields($settings['additional_fields']);

            if (!class_exists($settings['block'])) {
                continue;
            }

            /** @var Bubble_Elasticsearch_Block_Autocomplete_Abstract $entityBlock */
            $entityBlock = new $settings['block'];
            $autocomplete->setEntityBlock($type->getName(), $entityBlock);

            if (!$type->exists()) {
                continue;
            }

            /** @var Bubble_Elasticsearch_Client $client */
            $client = $index->getClient();
            $ids = array();
            $search = $client->getSearch($type, $q)->search();
            foreach ($search->getResults() as $result) {
                /** @var \Elastica\Result $result */
                $ids[] = (int) $result->getId();
                if (isset($result->_parent_ids)) {
                    $ids = array_merge($ids, $result->_parent_ids);
                }
            }

            $ids = array_values(array_unique($ids));

            if (!empty($ids)) {
                $response = $type->request('_mget', \Elastica\Request::POST, array('ids' => $ids))
                    ->getData();

                $docs = array();
                $count = 0;
                foreach ($response['docs'] as $data) {
                    if (isset($data['_source'])) {
                        $data = $data['_source'];
                        if ($entityBlock->validate($data)) {
                            if ($count < $limit) {
                                $docs[] = new Varien_Object($data);
                            }
                            $count++; // do not break because we need all results count
                        }
                    }
                }

                $autocomplete->setEntityResults($type->getName(), $docs);
                $autocomplete->setEntityResultsCount($type->getName(), $count);
            }
        }

        return $autocomplete->toHtml();
    }
}