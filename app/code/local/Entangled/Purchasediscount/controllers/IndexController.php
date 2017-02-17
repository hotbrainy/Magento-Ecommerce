<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Entangled_Purchasediscount_IndexController extends Mage_Core_Controller_Front_Action
{	
    public function indexAction()
    {
        echo "Entangled Purchasediscount Index";


        $adapter = Mage::getSingleton('core/resource');

        /* @var $conn Varien_Db_Adapter_Interface */
        $conn = $adapter->getConnection('core_write');

        $sql = "SELECT * FROM " . Mage::getSingleton('core/resource')->getTableName('purchasediscount/purchasedate') . " WHERE timestamp < DATE_SUB(NOW(), INTERVAL 1 MINUTE)";
        $results = $conn->fetchAll($sql);
          
        foreach ($results AS $result)
        {
  echo "id: " . $result['customer_id'] . '<br>';          
            $customer = Mage::getModel('customer/customer')->load($result['customer_id']);

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
    
}

