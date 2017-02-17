<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */


class Amasty_Shopby_Lib_Varien_Data_Form_Element_Multistoreinput extends Varien_Data_Form_Element_Abstract
{
    public function __construct($attributes=array())
    {
        parent::__construct($attributes);
        $this->setType('text');
        $this->addClass('input-text');
    }

    public function getElementHtml()
    {
        $html = "<section>";
        $valuesByStore = @unserialize($this->getValue() );
        if( !$valuesByStore) $valuesByStore[0] = $this->getValue();
        foreach (Mage::helper('amshopby')->getStores() as $_store) {
            isset($valuesByStore[$_store->getId()]) ? $value = $valuesByStore[$_store->getId()] : $value = '';
            $store = '<label style="display:block;font-weight: bold;" >'.$_store->getName().'</label>';
            $input = '<input style="display:block;width:150px;margin-right:10px" id="'.$this->getId().'"';
            $input .= 'name="multistore['.$this->getName().']['.$_store->getId().']"';
            $input .= $this->serialize($this->getHtmlAttributes());
            $input .= 'value="'.$value.'">';
            $html .= '<div style="float:left;">'.$store.$input.'</div>';
        }
        return $html.'<br style="clear:both;" /></section>';
    }

}