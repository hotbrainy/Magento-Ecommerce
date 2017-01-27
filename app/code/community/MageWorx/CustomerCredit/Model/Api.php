<?php

/**
 * MageWorx
 * Loyalty Booster Extension
 *
 * @category   MageWorx
 * @package    MageWorx_CustomerCredit
 * @copyright  Copyright (c) 2016 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_CustomerCredit_Model_Api extends Mage_Customer_Model_Api_Resource
{
    private $_customer = null;

    /**
     * Get Customer Object
     * @param int $id
     * @return Mage_Customer_Model_Customer
     */
    private function _getCustomer($id) {
        if (!$id) {
            $this->_fault('data_invalid', $e->getMessage());
        }
        if(!$this->_customer) {
            try {
                $customer = Mage::getModel('customer/customer')->load($id);
            } catch (Mage_Core_Exception $e) {
                $this->_fault('not_exists', $e->getMessage());
            }
            $this->_customer = $customer;
        }
        return $this->_customer;
    }
    
    /**
     * Get Cedit list
     * @return array
     */
    public function listCredit() {
        $model = Mage::getModel('mageworx_customercredit/credit')->setIsApi(true);
        $collection = $model->getCollection();
        $list = array();
        foreach($collection as $item) {
            $list[$item->getId()] = $item->getData();
        }
        return $list;
    }

    /**
     * Get credit by customer id
     * @param int $customerId
     * @return int|boolean
     */
    public function getCredit($customerId) {
        if($customer = $this->_getCustomer($customerId)) {
            try {
                $customerCredit = Mage::getModel('mageworx_customercredit/credit', $customer)->setIsApi(true);
                return $customerCredit->getValue();
            } catch (Mage_Core_Exception $e) {
                $this->_fault('not_exists', $e->getMessage());
            }
        }
        return false;
    }
    
    /**
     * Set credit
     * @param int $customerId
     * @param float $value
     * @return boolean
     */
    public function setCredit($customerId,$value)
    {
        if($customer = $this->_getCustomer($customerId))
        {
            try {
                $customerCredit = Mage::getModel('mageworx_customercredit/credit', $customer)->setIsApi(true);
                $credit = $this->getCredit($customerId);
                $credit = 0-$credit;
                $customerCredit->setValueChange($credit)->save();
                return TRUE;
            } catch (Mage_Core_Exception $e) {
                $this->_fault('not_updated', $e->getMessage());
            }
            
        }
        return FALSE;
    }
    
    /**
     * Increase Credit Value
     * @param int $customerId
     * @param float $value
     * @return boolean
     */
    public function increaseCredit($customerId,$value) {
        if($customer = $this->_getCustomer($customerId)) {
            try {
                $customerCredit = Mage::getModel('mageworx_customercredit/credit', $customer)->setIsApi(true);
                $customerCredit->setValueChange($value)->save();
                return TRUE;
            } catch (Mage_Core_Exception $e) {
                $this->_fault('not_updated', $e->getMessage());
            }
            
        }
        return false;
    }
    
    /**
     * Decrease Credit value
     * @param int $customerId
     * @param float $value
     * @return boolean
     */
    public function decreaseCredit($customerId,$value) {
        if($customer = $this->_getCustomer($customerId)) {
            try {
                $value = 0 - $value;
                $customerCredit = Mage::getModel('mageworx_customercredit/credit', $customer)->setIsApi(true);
                $customerCredit->setValueChange($value)->save();
                return TRUE;
            } catch (Mage_Core_Exception $e) {
                $this->_fault('not_updated', $e->getMessage());
            }
            
        }
        return false;
    }
    
    /**
     * Generate new credit codes
     * @param float $credit_value
     * @param int $website_id
     * @param int $qty
     * @param date $from_date
     * @param date $to_date
     * @param boolean $is_active
     * @param int $code_length
     * @param int $group_length
     * @param string $group_separator
     * @param string $code_format
     * @return boolean
     */
    public function generateNewCodes($credit_value=1,$website_id=1,$qty=1,$from_date,$to_date,$is_active=true,$code_length=null,$group_length=null,$group_separator=null,$code_format=null) {
        $helper = Mage::helper('mageworx_customercredit');
        if(!$code_length) $code_length = $helper->getCodeLength();
        if(!$group_length) $group_length = $helper->getGroupLength();
        if(!$group_separator) $group_separator = $helper->getGroupSeparator();
        if(!$code_format) $code_format = $helper->getCodeFormat();
        
        $codeModel = Mage::getModel('mageworx_customercredit/code');
        $data = array('settings'=>array(),'details'=>array());
        
        $data['settings'] = array('code_length'=>$code_length,
                                  'group_length'=>$group_length,
                                  'group_separator'=>$group_separator,
                                  'code_format'=>$code_format,
                                  'qty'=>$qty);
        $data['details'] = array( 'credit' => $credit_value,
                                  'website_id' => $website_id,
                                  'is_active' => $is_active);
        $dataDetails = array();
        $dataDetails = $this->_filterDates($dataDetails, array($from_date, $to_date));
        $data['details'] = $dataDetails; 
        
        $codeModel->loadPost($data);
        $codeModel->generate();
        
        return true;
    }

}