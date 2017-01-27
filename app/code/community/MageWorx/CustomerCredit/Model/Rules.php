<?php

/**
 * MageWorx
 * Loyalty Booster Extension
 *
 * @category   MageWorx
 * @package    MageWorx_CustomerCredit
 * @copyright  Copyright (c) 2016 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_CustomerCredit_Model_Rules extends Mage_Rule_Model_Rule
{
    const FREE_SHIPPING_ITEM = 1;
    const FREE_SHIPPING_ADDRESS = 2;
    
    const CC_RULE_TYPE_APPLY = 1;
    const CC_RULE_TYPE_GIVE  = 2;
    
    protected function _construct() {
        $this->_init('mageworx_customercredit/rules');
        $this->setIdFieldName('rule_id');
    }
    
    public function getRuleTypeArray()
    {
        return array(
            self::CC_RULE_TYPE_APPLY => Mage::helper('mageworx_customercredit')->__('Use Credits'),
            self::CC_RULE_TYPE_GIVE  => Mage::helper('mageworx_customercredit')->__('Give Credits'),
        );
    }

    public function getConditionsInstance()
    {
        return Mage::getModel('mageworx_customercredit/rules_condition_combine');
    }

    public function getActionsInstance()
    {
        return Mage::getModel('salesrule/rule_condition_product_combine');
    }

    /**
     * To String
     * @param string $format
     * @return string
     */
    public function toString($format='')
    {
        $str = Mage::helper('mageworx_customercredit')->__("Name: %s", $this->getName()) ."\n"
             . Mage::helper('mageworx_customercredit')->__("Start at: %s", $this->getStartAt()) ."\n"
             . Mage::helper('mageworx_customercredit')->__("Expire at: %s", $this->getExpireAt()) ."\n"
             . Mage::helper('mageworx_customercredit')->__("Customer registered: %s", $this->getCustomerRegistered()) ."\n"
             . Mage::helper('mageworx_customercredit')->__("Customer is new buyer: %s", $this->getCustomerNewBuyer()) ."\n"
             . Mage::helper('mageworx_customercredit')->__("Description: %s", $this->getDescription()) ."\n\n"
             . $this->getConditions()->toStringRecursive() ."\n\n"
             . $this->getActions()->toStringRecursive() ."\n\n";
        return $str;
    }

    /**
     * 
     * @param array $rule
     * @return MageWorx_CustomerCredit_Model_Rules
     */
    public function loadPost(array $rule)
    {
        $arr = $this->_convertFlatToRecursive($rule);
        if (isset($arr['conditions'])) {
            $this->getConditions()->setConditions(array())->loadArray($arr['conditions'][1]);
        }
        if (isset($arr['actions'])) {
            $this->getActions()->setActions(array())->loadArray($arr['actions'][1], 'actions');
        }

    	return $this;
    }

    /**
     * Returns rule as an array for admin interface
     *
     * Output example:
     * array(
     *   'name'=>'Example rule',
     *   'conditions'=>{condition_combine::toArray}
     *   'actions'=>{action_collection::toArray}
     * )
     *
     * @return array
     */
    public function toArray(array $arrAttributes = array())
    {
        $out = parent::toArray($arrAttributes);
        $out['customer_registered'] = $this->getCustomerRegistered();
        $out['customer_new_buyer'] = $this->getCustomerNewBuyer();

        return $out;
    }
    
    public function getResourceCollection()
    {
        return Mage::getResourceModel('mageworx_customercredit/rules_collection');
    }
    
    // for magento 1.7 fix
    protected function _beforeSave() {
        // check if discount amount > 0
        if ((int)$this->getDiscountAmount() < 0) {
            Mage::throwException(Mage::helper('mageworx_customercredit')->__('Invalid discount amount.'));
        }

        if ($this->getConditions()) {
            $this->setConditionsSerialized(serialize($this->getConditions()->asArray()));
            $this->unsConditions();
        }
        if ($this->getActions()) {
            $this->setActionsSerialized(serialize($this->getActions()->asArray()));
            $this->unsActions();
        }

        if (is_array($this->getWebsiteIds())) {
            $this->setWebsiteIds(join(',', $this->getWebsiteIds()));
        }

        if (is_array($this->getCustomerGroupIds())) {
            $this->setCustomerGroupIds(join(',', $this->getCustomerGroupIds()));
        }
    }

}
