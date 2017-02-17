<?php
class Dextrous_Membership_Model_Observer
{
	public function placeOrderAfter(Varien_Event_Observer $observer)
	{
		$order = $observer->getEvent()->getOrder();
		if($order && $order->getCustomerId())
		{
			$orderItems = 	$order->getAllItems();
			$skusArr	=	array();
			$memberSku	=	Entangled_Purchasediscount_Helper_Data::DISCOUNT_SKU;
			if(count($orderItems) > 0)
			{
				foreach($orderItems as $item){
					$skusArr[]	=	$item->getData('sku');
				}
				if(count($skusArr) > 0 && in_array($memberSku, $skusArr)) {
					$customerId = 	$order->getCustomerId();
					$customer 	= 	Mage::getModel('customer/customer')->load($customerId);
					$customer->setData('group_id', 4); //10% off Customers - Group
					$customer->save();
				}
			}
		}
	}
}
