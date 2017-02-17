<?php
/**
 * @category    Bubble
 * @package     Bubble_Elasticsearch
 * @version     4.1.2
 * @copyright   Copyright (c) 2016 BubbleShop (https://www.bubbleshop.net)
 */
class Bubble_Elasticsearch_Block_Adminhtml_System_Config_Form_Field_Autocomplete_Labels
    extends Mage_Adminhtml_Block_System_Config_Form_Field_Array_Abstract
{
    /**
     * Initialization
     */
    public function __construct()
    {
        $this->addColumn('label', array(
            'label' => Mage::helper('elasticsearch')->__('Label'),
            'style' => 'width:120px',
        ));
        $this->addColumn('translation', array(
            'label' => Mage::helper('elasticsearch')->__('Translation'),
            'style' => 'width:120px',
        ));
        $this->_addAfter = false;
        $this->_addButtonLabel = Mage::helper('elasticsearch')->__('Add Label');
        parent::__construct();
    }
}
