<?php
require_once "Idev/OneStepCheckout/controllers/AjaxController.php";

class Entangled_RewardPoints_Rewrite_AjaxController extends Idev_OneStepCheckout_AjaxController {

    public function updatecartAction(){

        $this->_checkSession();

        $response = array(
            'success' => false,
            'error'=> false,
            'message' => false
        );

        try {
            $cartData = $this->getRequest()->getParam('cart');

            if (!empty($cartData) && is_array($cartData)) {
                $filter = new Zend_Filter_LocalizedToNormalized(
                    array('locale' => Mage::app()->getLocale()->getLocaleCode())
                );
                foreach ($cartData as $index => $data) {
                    if (isset($data['qty'])) {
                        $cartData[$index]['qty'] = $filter->filter(trim($data['qty']));
                    }
                }
                $cart = Mage::getSingleton('checkout/cart');
                if (! $cart->getCustomerSession()->getCustomer()->getId() && $cart->getQuote()->getCustomerId()) {
                    $cart->getQuote()->setCustomerId(null);
                }

                $cartData = $cart->suggestItemsQty($cartData);
                $cart->updateItems($cartData)
                    ->save();
                Mage::getSingleton('checkout/session')->setCartWasUpdated(true);

            } else {

                Mage::getSingleton('checkout/session')->addException($e, $this->__('Cannot update shopping cart.'));
                $response = array(
                    'success' => false,
                    'error'=> true,
                    'message' => 'No cart data here'
//                    'redirect' => Mage::getUrl('checkout/cart')
                );
            }

        } catch (Mage_Core_Exception $e) {
            Mage::getSingleton('checkout/session')->addError(Mage::helper('core')->escapeHtml($e->getMessage()));
            $response = array(
                'success' => false,
                'error'=> true,
                'message' => Mage::helper('core')->escapeHtml($e->getMessage())
//                'redirect' => Mage::getUrl('checkout/cart')
            );
        } catch (Exception $e) {
            Mage::getSingleton('checkout/session')->addException($e, $this->__('Cannot update shopping cart.'));
            $response = array(
                'success' => false,
                'error'=> true,
                'message' => $this->__('Cannot update shopping cart.')
//                'redirect' => Mage::getUrl('checkout/cart')
            );
            Mage::logException($e);
        }



        $response = array(
            'success' => true,
            'error'=> false,
            'message' => 'Items upated'
//            'redirect' => ''
        );

        $response["items_count"] = Mage::helper('checkout/cart')->getItemsCount();
        $response["base_url"] = Mage::getBaseUrl()."?empty_cart=1";

        if(!$cart->getQuote()->hasItems()){
//            $response['redirect'] = Mage::getUrl();
        }

        $html = $this->getLayout()
            ->createBlock('checkout/onepage_shipping_method_available')
            ->setTemplate('onestepcheckout/shipping_method.phtml')
            ->toHtml();

        $response['shipping_method'] = $html;


        $html = $this->getLayout()
            ->createBlock('checkout/onepage_payment_methods','choose-payment-method')
            ->setTemplate('onestepcheckout/payment_method.phtml');

        if(Mage::helper('onestepcheckout')->isEnterprise() && Mage::helper('customer')->isLoggedIn()){

            $customerBalanceBlock = $this->getLayout()->createBlock('enterprise_customerbalance/checkout_onepage_payment_additional', 'customerbalance', array('template'=>'onestepcheckout/customerbalance/payment/additional.phtml'));
            $customerBalanceBlockScripts = $this->getLayout()->createBlock('enterprise_customerbalance/checkout_onepage_payment_additional', 'customerbalance_scripts', array('template'=>'onestepcheckout/customerbalance/payment/scripts.phtml'));

            $rewardPointsBlock = $this->getLayout()->createBlock('enterprise_reward/checkout_payment_additional', 'reward.points', array('template'=>'onestepcheckout/reward/payment/additional.phtml', 'before' => '-'));
            $rewardPointsBlockScripts = $this->getLayout()->createBlock('enterprise_reward/checkout_payment_additional', 'reward.scripts', array('template'=>'onestepcheckout/reward/payment/scripts.phtml', 'after' => '-'));

            $this->getLayout()->getBlock('choose-payment-method')
                ->append($customerBalanceBlock)
                ->append($customerBalanceBlockScripts)
                ->append($rewardPointsBlock)
                ->append($rewardPointsBlockScripts)
            ;
        }

        if(Mage::helper('onestepcheckout')->isEnterprise()){
            $giftcardScripts = $this->getLayout()->createBlock('enterprise_giftcardaccount/checkout_onepage_payment_additional', 'giftcardaccount_scripts', array('template'=>'onestepcheckout/giftcardaccount/onepage/payment/scripts.phtml'));
            $html->append($giftcardScripts);
        }

        $response['payment_method'] = $html->toHtml();

        // Add updated totals HTML to the output
        $html = $this->getLayout()
            ->createBlock('onestepcheckout/summary')
            ->setTemplate('onestepcheckout/summary.phtml')
            ->toHtml();

        $response['summary'] = $html;

        $this->getResponse()->setBody(Zend_Json::encode($response));
    }

}