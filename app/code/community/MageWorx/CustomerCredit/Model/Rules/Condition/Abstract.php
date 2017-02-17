<?php

/**
 * MageWorx
 * Loyalty Booster Extension
 *
 * @category   MageWorx
 * @package    MageWorx_CustomerCredit
 * @copyright  Copyright (c) 2016 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_CustomerCredit_Model_Rules_Condition_Abstract extends Mage_Rule_Model_Condition_Abstract
{
    public function loadAttributeOptions() {
        return $this;
    }

    public function loadOperatorOptions() {
        $this->setOperatorOption(array(
            '=='  => Mage::helper('mageworx_customercredit')->__('is'),
            '!='  => Mage::helper('mageworx_customercredit')->__('is not'),
            '>='  => Mage::helper('mageworx_customercredit')->__('equals or greater than'),
            '<='  => Mage::helper('mageworx_customercredit')->__('equals or less than'),
            '>'   => Mage::helper('mageworx_customercredit')->__('greater than'),
            '<'   => Mage::helper('mageworx_customercredit')->__('less than'),
            '{}'  => Mage::helper('mageworx_customercredit')->__('contains'),
            '!{}' => Mage::helper('mageworx_customercredit')->__('does not contain'),
            '()'  => Mage::helper('mageworx_customercredit')->__('is one of'),
            '!()' => Mage::helper('mageworx_customercredit')->__('is not one of'),
        ));
        $this->setOperatorByInputType(array(
            'string' => array('==', '!=', '>=', '>', '<=', '<', '{}', '!{}', '()', '!()'),
            'numeric' => array('==', '!=', '>=', '>', '<=', '<'),
            'date' => array('==', '>=', '<='),
            'select' => array('==', '!='),
            'multiselect' => array('==', '!=', '{}', '!{}'),
            'grid' => array('()', '!()'),
        ));
        return $this;
    }

    public function getAttributeObject() {
        try {
            $obj = Mage::getSingleton('eav/config')
                ->getAttribute('catalog_product', $this->getAttribute());
        }
        catch (Exception $e) {
            $obj = new Varien_Object();
            $obj->setEntity(Mage::getResourceSingleton('catalog/product'))
                ->setFrontendInput('text');
        }
        return $obj;
    }

    public function getValueElement() {
        $element = parent::getValueElement();
        switch ($this->getInputType()) {
            case 'date':
                $element->setImage(Mage::getDesign()->getSkinUrl('images/grid-cal.gif'));
                break;
        }
        return $element;
    }

    public function getExplicitApply() {
        switch ($this->getInputType()) {
            case 'date':
                return true;
        }
        return false;
    }

    public function getAttributeElement() {
        $element = parent::getAttributeElement();
        $element->setShowAsText(true);
        return $element;
    }
    
    public function asHtml() {
        $html = $this->getTypeElementHtml()
           .$this->getAttributeElementHtml()
           .$this->getOperatorElementHtml()
           .$this->getValueElementHtml()
           .$this->getRemoveLinkHtml()
           .$this->getChooserContainerHtml();
        return $html;
    }

    public function getInputType() {
        switch ($this->getAttribute()) {
            case 'total_amount':
                return 'numeric';
            case 'registration':
                return 'date';
            case 'place_order':
                return 'place_order';
            case 'number_of_orders':
                return 'number_of_orders';
            case 'order_total':
                return 'order_total';
        }
        return 'string';
    }

    public function getValueElementType() {
        switch ($this->getAttribute()) {
            case 'place_order':
                return 'select';
                break;
            case 'registration':
                return 'date';
        }
        return 'text';
    }

    public function getOperatorSelectOptions() {
        $helper = Mage::helper('mageworx_customercredit');
        $type = $this->getInputType();
        $opt = array();
        if($type == 'date'){
    		$opt[] = array('value'=>'==', 'label'=> $helper->__('is'));
    		$opt[] = array('value'=>'<=', 'label'=> $helper->__('is or earlier than'));
    		$opt[] = array('value'=>'>=', 'label'=> $helper->__('is or later than'));
    	}
        elseif($type == 'place_order') {
            $opt[] = array('value'=>'==', 'label'=> $helper->__('is'));
        } elseif ($type == 'number_of_orders' || $type == 'order_total') {
            $opt[] = array('value'=>'==', 'label'=> $helper->__('is'));
            $opt[] = array('value'=>'!=', 'label'=> $helper->__('is not'));
            $opt[] = array('value'=>'>=', 'label'=> $helper->__('equals or greater than'));
            $opt[] = array('value'=>'<=', 'label'=> $helper->__('equals or less than'));
            $opt[] = array('value'=>'>', 'label'=> $helper->__('greater than'));
            $opt[] = array('value'=>'<', 'label'=> $helper->__('less than'));
        } else {
            $operatorByType = $this->getOperatorByInputType();
            foreach ($this->getOperatorOption() as $k=>$v) {
                if (!$operatorByType || in_array($k, $operatorByType[$type])) {
                    $opt[] = array('value'=>$k, 'label'=>$v);
                }
            }
        }
        return $opt;
    }

    public function getOperatorElement() {
        if (is_null($this->getOperator())) {
            foreach ($this->getOperatorOption() as $k=>$v) {
                $this->setOperator($k);
                break;
            }
        }

        $operatorName = $this->getOperatorName();

        if($this->getInputType() == 'date'){
            switch ($this->getOperator()){
                case '<=':
                    $operatorName = 'is or earlier than'; break;
                case '>=':
                    $operatorName = 'is or later than'; break;
            }
        }

        return $this->getForm()->addField($this->getPrefix().'__'.$this->getId().'__operator', 'select', array(
            'name'=>'rule['.$this->getPrefix().']['.$this->getId().'][operator]',
            'values'=>$this->getOperatorSelectOptions(),
            'value'=>$this->getOperator(),
            'value_name'=>$operatorName,
        ))->setRenderer(Mage::getBlockSingleton('rule/editable'));
    }

    /**
     * Validate Address Rule Condition
     *
     * @param Varien_Object $object
     * @return bool
     */
    public function validate(Varien_Object $object) {
        
        $address = $object;
        if (!$address instanceof Mage_Sales_Model_Quote_Address) {
            if(!$object->getQuote() && Mage::app()->getRequest()->getParam('order_id')) {
                $order = Mage::getModel('sales/order')->load(Mage::app()->getRequest()->getParam('order_id'));
                $quote = Mage::getModel('sales/quote')->load($order->getQuoteId());
                $object->setQuote($quote);
            }
            if ($object->getQuote()->isVirtual()) {
                $address = $object->getQuote()->getBillingAddress();
            }
            else {
                $address = $object->getQuote()->getShippingAddress();
            }
        }
        if(!$object instanceof Varien_Object) {
            return false;
        }
        return parent::validate($address);
    }
    
    public function validateProduct(Varien_Object $object)
    {
        if(!$object instanceof Varien_Object) {
            return false;
        }
        return parent::validate($object);
    }
}
