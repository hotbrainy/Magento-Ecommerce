<?php
class Dextrous_Membership_IndexController extends Mage_Core_Controller_Front_Action
{	
	public function indexAction()
	{
		$this->loadLayout();
		$this->renderLayout();
	}
	
	public function membershipAction()
	{
		Mage::getSingleton('core/session', array('name' => 'frontend'));
		try {
			$sku	 	=	Entangled_Purchasediscount_Helper_Data::DISCOUNT_SKU;
			$proId	 	= 	Mage::getModel('catalog/product')->getResource()->getIdBySku($sku);
			$qty 	 	= 	'1';
			$product 	= 	Mage::getModel('catalog/product')->load($proId);
			$cart 		= 	Mage::getModel('checkout/cart');
			$cart->init();
			$cart->addProduct($product, array('qty' => $qty));
			$cart->save();
			Mage::getSingleton('checkout/session')->setCartWasUpdated(true);
			Mage::getSingleton('core/session')->addSuccess('Product added successfully');
			$checkoutUrl	=	Mage::helper('checkout/url')->getCheckoutUrl();
			header('Location: ' . $checkoutUrl);
			exit;
		} catch (Exception $e) {
			$homeUrl	=	Mage::getUrl('');
			Mage::getSingleton('core/session')->addError($e->getMessage());
			header('Location: ' . $homeUrl);
			exit;
		}
	}
}
?>