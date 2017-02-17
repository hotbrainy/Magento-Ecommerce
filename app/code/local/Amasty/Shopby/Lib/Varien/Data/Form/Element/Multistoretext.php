<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */


class Amasty_Shopby_Lib_Varien_Data_Form_Element_Multistoretext extends Varien_Data_Form_Element_Abstract
{

    public function __construct($attributes=array())
    {
        parent::__construct($attributes);
        $this->setType('textarea');
        $this->setExtType('textarea');
        $this->setRows(2);
        $this->setCols(15);
    }

    public function getElementHtml()
    {
        $this->addClass('textarea');
        $html = "<section>";
        $valuesByStore = @unserialize($this->getValue() );
        if (!$valuesByStore) $valuesByStore[0] = $this->getValue();
        foreach (Mage::helper('amshopby')->getStores() as $_store) {
            isset($valuesByStore[$_store->getId()]) ? $value = $valuesByStore[$_store->getId()] : $value = '';
            $store = '<label style="display:block;font-weight: bold;" >'.$_store->getName().'</label>';
            $input = '<textarea  style="display:block;width:150px;margin-right:10px"';
            $input .= 'name="multistore['.$this->getName().']['.$_store->getId().']"';
            $input .= $this->serialize($this->getHtmlAttributes());
            $input .= 'id="'.$this->getId().'">'.$value.'</textarea>';
            $html .= '<div style="float:left;">'.$store.$input.'</div>';
        }
        $html .= $this->getAfterElementHtml();
        $html .='<br style="clear:both;" /></section>';
        return $html;
    }

}