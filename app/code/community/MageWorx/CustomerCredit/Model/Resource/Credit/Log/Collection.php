<?php

/**
 * MageWorx
 * Loyalty Booster Extension
 *
 * @category   MageWorx
 * @package    MageWorx_CustomerCredit
 * @copyright  Copyright (c) 2016 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_CustomerCredit_Model_Resource_Credit_Log_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract {

    protected function _construct() {
        $this->_init('mageworx_customercredit/credit_log');
    }

    /**
     * Filter collection by customer credit
     * 
     * @param int|array $id 
     * @return MageWorx_CustomerCredit_Model_Resource_Credit_Log_Collection
     */
    public function addCreditFilter($id) {
        $this->addFieldToFilter('credit_id', array('in' => $id));
        return $this;
    }

    /**
     * Filter collection by customers
     * 
     * @param int|array $id 
     * @return MageWorx_CustomerCredit_Model_Resource_Credit_Log_Collection
     */
    public function addCustomerFilter($id) {
        $this->addFieldToFilter('customer_id', array('in' => $id));
        return $this;
    }
    
    public function addCustomerToSelect() {
        
        $customer = Mage::getModel('customer/customer');
        $customer_firstname = $customer->getAttribute('firstname');
        $customer_firstname_table = $customer_firstname->getBackend()->getTable();
        $customer_lastname = $customer->getAttribute('lastname');
        $customer_lastname_table = $customer_lastname->getBackend()->getTable();

        $this->getSelect()
          //   ->joinLeft(array('cc'=>$this->getTable('mageworx_customercredit/credit')), 'main_table.credit_id=cc.credit_id', 'customer_id')
             ->join(array('c'=>$this->getTable('customer/entity')), 'c.entity_id=credit.customer_id', array('email','group_id','credit_balance'=>'main_table.value'))
             ->joinLeft(array('lname'=>$customer_lastname_table),'credit.customer_id = lname.entity_id AND lname.attribute_id = '.$customer_lastname->getId(),'')
             ->joinLeft(array('fname'=>$customer_lastname_table),'credit.customer_id = fname.entity_id AND fname.attribute_id = '.$customer_firstname->getId(),array('customer_name'=>"CONCAT(fname.value,' ',lname.value)"))
                ;
        //echo $this->getSelect()->__toString(); exit;
        return $this;
    }
    
    
    public function addOrderFilter($id) {
        $this->addFieldToFilter('order_id', $id);
        return $this;
    }
    
    public function addActionTypeFilter($type) {
        $this->addFieldToFilter('action_type', $type);
        return $this;
    }
    

    /**
     * Filter collection by websites
     * 
     * @param int|array $id 
     * @return MageWorx_CustomerCredit_Model_Resource_Credit_Log_Collection
     */
    public function addWebsiteFilter($id) {
        $this->addFieldToFilter('main_table.website_id', array('in' => $id));
        return $this;
    }

    protected function _initSelect() {
        parent::_initSelect();
        $this->getSelect()
                ->joinInner(array('credit' => $this->getTable('mageworx_customercredit/credit')), 'main_table.credit_id = credit.credit_id',
                    array('customer_id' => 'credit.customer_id','real_credit'=>'credit.value') //'website_id' => 'credit.website_id',
                )
                ;//->order('log_id DESC');
        ;
        return $this;
    }

}