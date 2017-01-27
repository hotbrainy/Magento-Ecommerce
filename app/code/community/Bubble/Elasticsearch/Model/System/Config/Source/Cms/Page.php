<?php
/**
 * @category    Bubble
 * @package     Bubble_Elasticsearch
 * @version     4.1.2
 * @copyright   Copyright (c) 2016 BubbleShop (https://www.bubbleshop.net)
 */
class Bubble_Elasticsearch_Model_System_Config_Source_Cms_Page
    extends Mage_Adminhtml_Model_System_Config_Source_Cms_Page
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        if (!$this->_options) {
            if (Mage::helper('core')->isModuleEnabled('Bubble_CmsTree')) {
                $this->_options = Mage::getModel('bubble_cmstree/adminhtml_system_config_source_cms_page')
                    ->toOptionArray();
            } else {
                $this->_options = array();
                $collection = Mage::getResourceModel('cms/page_collection');
                foreach ($collection as $page) {
                    $this->_options[] = array(
                        'value' => $page->getId(),
                        'label' => $page->getTitle(),
                    );
                }
            }
        }

        return $this->_options;
    }
}
