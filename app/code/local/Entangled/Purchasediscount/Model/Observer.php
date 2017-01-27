<?php
//ini_set('display_errors', 'On');
//error_reporting(E_ALL);
class Entangled_Purchasediscount_Model_Observer
{
    /**
     * Check the customer's order line items to see if they purchased the discount membership
     * @param obj $observer
     */
    
    public function checkOrder($observer)
    {

        
        $order = Mage::getModel('sales/order')->load($observer->order_ids[0]);
        $items = $order->getAllItems();
        foreach ($items AS $item)
        {
  
            if ($item->getSku() == Entangled_Purchasediscount_Helper_Data::DISCOUNT_SKU)
            {
                $this->changeCustomerGroup($observer);
                $this->sendNewFanaticEmail();
            }
        }



    }
    
    /**
     * Change customer to discount group
     */
    private function changeCustomerGroup($observer)
    {
        $customerId = Mage::getSingleton('customer/session')->getCustomer()->getId();
   
        $order = Mage::getModel('sales/order')->load($observer->order_ids[0]);
        $customer = Mage::getModel('customer/customer')->load($customerId);

        $customer->setData('group_id', Entangled_Purchasediscount_Helper_Data::DISCOUNT_GROUP_ID);
        try {            
            $customer->save();
        } catch ( Exception $e ) {
            echo "error: " . $e->getMessage() . '<br><br>';
        }     
       
        
        // Set expiration date
        $adapter = Mage::getSingleton('core/resource');

        /* @var $conn Varien_Db_Adapter_Interface */
        $conn = $adapter->getConnection('core_write');
        $update = "INSERT INTO " . Mage::getSingleton('core/resource')->getTableName('purchasediscount/purchasedate') . " ( customer_id ) VALUES('" . $customer->getId() . "')";

        $conn->query($update);
        
        
    }
    
    /**
     * Check for expired memberships and set the customer back to regular customer group
     */
    public function checkDiscountExpiration()
    {
        $adapter = Mage::getSingleton('core/resource');

        /* @var $conn Varien_Db_Adapter_Interface */
        $conn = $adapter->getConnection('core_write');

        $sql = "SELECT * FROM " . Mage::getSingleton('core/resource')->getTableName('purchasediscount/purchasedate') . " WHERE timestamp < DATE_SUB(NOW(), INTERVAL 1 YEAR)";
        $results = $conn->fetchAll($sql);
          
        foreach ($results AS $result)
        {
            $customer = Mage::getModel('customer/customer')->load($customerId);

            $customer->setData( 'group_id', 1);
            try {            
                $customer->save();
            } catch ( Exception $e ) {
                echo "error: " . $e->getMessage() . '<br><br>';
            }  
            
            $sql = "DELETE FROM " . Mage::getSingleton('core/resource')->getTableName('purchasediscount/purchasedate') . " WHERE purchase_id = " . $result['purchase_id'];
            $conn->query($sql);
       
        }
        
    }

    public function sendNewFanaticEmail(){
        $customer = Mage::getSingleton('customer/session')->getCustomer();
        $sender = array(
            'name' => Mage::getStoreConfig('trans_email/ident_general/name'),
            'email' => Mage::getStoreConfig('trans_email/ident_general/email')
        );
        $recepientEmail = $customer->getEmail();
        $vars = array(
            'customer' => $customer,
            'expiration_date' => date('Y-m-d',strtotime(date("Y-m-d", mktime()) . " + 365 day")),
        );
        $storeId = Mage::app()->getStore()->getId();
        Mage::getModel('core/email_template')
            ->sendTransactional('entangled_rewardpoints_new_fanatic', $sender, $recepientEmail, $customer->getName(), $vars, $storeId);
    }
    
}

