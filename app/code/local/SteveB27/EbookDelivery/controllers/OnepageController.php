<?php

require_once 'Mage/Checkout/controllers/OnepageController.php';

class SteveB27_EbookDelivery_OnepageController extends Mage_Checkout_OnepageController
{
    public function doSomestuffAction()
    {
		if(true) {
			$result['update_section'] = array(
            	'name' => 'payment-method',
                'html' => $this->_getPaymentMethodsHtml()
			);					
		}
    	else {
			$result['goto_section'] = 'shipping';
		}		
    }    
    
        /**
     * Save checkout billing address
     */
    public function saveBillingAction()
    {
        if ($this->_expireAjax()) {
            return;
        }
        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost('billing', array());
            $customerAddressId = $this->getRequest()->getPost('billing_address_id', false);

            if (isset($data['email'])) {
                $data['email'] = trim($data['email']);
            }
            $result = $this->getOnepage()->saveBilling($data, $customerAddressId);

            if (!isset($result['error'])) {
                if ($this->getOnepage()->getQuote()->isVirtual()) {
					if (Mage::helper('ebookdelivery')->isEnabled()) {
						$this->loadLayout('checkout_onepage_ebookdelivery');
						$result['goto_section'] = 'ebookdelivery';
					} else {
						$result['goto_section'] = 'payment';
						$result['update_section'] = array(
							'name' => 'payment-method',
							'html' => $this->_getPaymentMethodsHtml()
						);
					}
                } elseif (isset($data['use_for_shipping']) && $data['use_for_shipping'] == 1) {
					if (Mage::helper('ebookdelivery')->isEnabled()) {
						$this->loadLayout('checkout_onepage_ebookdelivery');
						$result['goto_section'] = 'ebookdelivery';
					} else {
						$result['goto_section'] = 'shipping_method';
						$result['update_section'] = array(
							'name' => 'shipping-method',
							'html' => $this->_getShippingMethodsHtml()
						);
					}

                    $result['allow_sections'] = array('shipping');
                    $result['duplicateBillingInfo'] = 'true';
                } else {
                    $result['goto_section'] = 'shipping';
                }
            }

            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
        }
    }

    /**
     * Shipping address save action
     */
    public function saveShippingAction()
    {
        if ($this->_expireAjax()) {
            return;
        }
        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost('shipping', array());
            $customerAddressId = $this->getRequest()->getPost('shipping_address_id', false);
            $result = $this->getOnepage()->saveShipping($data, $customerAddressId);

            if (!isset($result['error'])) {
				if (Mage::helper('ebookdelivery')->isEnabled()) {
					$this->loadLayout('checkout_onepage_ebookdelivery');
					$result['goto_section'] = 'ebookdelivery';
            	} else {
					$result['goto_section'] = 'shipping_method';
					$result['update_section'] = array(
						'name' => 'shipping-method',
						'html' => $this->_getShippingMethodsHtml()
					);
				}
            }
            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
        }
    }

    public function saveEbookdeliveryAction()
    {
    	$this->_expireAjax();
        if ($this->getRequest()->isPost()) {
            
        	$delivery_array = $this->getRequest()->getPost('delivery',"");
        	$devices = array();
			foreach($delivery_array as $key => $device) {
				foreach($device as $device_type => $device_fields) {
					foreach($device_fields as $field => $value) {
						$devices[$key]['device_type'] = $device_type;
						$devices[$key][$field] = $value;
					}
				}
			}
        		
        	Mage::getSingleton('core/session')->setSteveB27EbookDelivery(serialize($devices));

			$result = array();

			if (!isset($result['error'])) {
                if ($this->getOnepage()->getQuote()->isVirtual()) {
                    $result['goto_section'] = 'payment';
                    $result['update_section'] = array(
                        'name' => 'payment-method',
                        'html' => $this->_getPaymentMethodsHtml()
                    );
                } else {
                    $result['goto_section'] = 'shipping_method';
                    $result['update_section'] = array(
                        'name' => 'shipping-method',
                        'html' => $this->_getShippingMethodsHtml()
                    );
                } 
            }

            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
        }
    }    
}
