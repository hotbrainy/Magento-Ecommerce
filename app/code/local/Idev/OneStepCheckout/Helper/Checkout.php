<?php
class Idev_OneStepCheckout_Helper_Checkout extends Mage_Core_Helper_Abstract
{
    public $methods = array();

    public function savePayment($data)
    {
        if (empty($data)) {
            return array('error' => -1, 'message' => Mage::helper('checkout')->__('Invalid data'));
        }
        if ($this->getOnepage()->getQuote()->isVirtual()) {
            $this->getOnepage()->getQuote()->getBillingAddress()->setPaymentMethod(isset($data['method']) ? $data['method'] : null);
        } else {
            $this->getOnepage()->getQuote()->getShippingAddress()->setPaymentMethod(isset($data['method']) ? $data['method'] : null);
        }

        $payment = $this->getOnepage()->getQuote()->getPayment();
        $payment->importData($data);

        $this->getOnepage()->getQuote()->save();

        return array();
    }

    public function saveShippingMethod($shippingMethod)
    {
        if (empty($shippingMethod)) {
            $res = array(
                'error' => -1,
                'message' => Mage::helper('checkout')->__('Invalid shipping method.')
            );
            return $res;
        }
        $rate = $this->getOnepage()->getQuote()->getShippingAddress()->getShippingRateByCode($shippingMethod);
        if (!$rate) {
            $res = array(
                'error' => -1,
                'message' => Mage::helper('checkout')->__('Invalid shipping method.')
            );
            return $res;
        }
        $this->getOnepage()->getQuote()->getShippingAddress()->setShippingMethod($shippingMethod);

        return array();
    }

    public function saveShipping($data, $customerAddressId)
    {
        if (empty($data)) {
            $res = array(
                'error' => -1,
                'message' => Mage::helper('checkout')->__('Invalid data')
            );
            return $res;
        }
        $address = $this->getOnepage()->getQuote()->getShippingAddress();

        if (!empty($customerAddressId)) {
            $customerAddress = Mage::getModel('customer/address')->load($customerAddressId);
            if ($customerAddress->getId()) {
                if ($customerAddress->getCustomerId() != $this->getOnepage()->getQuote()->getCustomerId()) {
                    return array('error' => 1,
                        'message' => Mage::helper('checkout')->__('Customer Address is not valid.')
                    );
                }
                $address->importCustomerAddress($customerAddress);
            }
        } else {
            unset($data['address_id']);
            $address->addData($data);
        }

        $address->implodeStreetAddress();
        $address->setCollectShippingRates(true);

        if (($validateRes = $address->validate())!==true) {
            $res = array(
                'error' => 1,
                'message' => $validateRes
            );
            return $res;
        }

        $this->getOnepage()->getQuote()
        //->collectTotals()
        ->save();

        return array();
    }

    function __construct()
    {
        $this->settings = $this->loadConfig();
        $this->temp = Mage::getStoreConfig('onestepcheckout/general/' . base64_decode('c2VyaWFs'));
    }

    protected function _isLoggedIn()
    {
        $helper = Mage::helper('customer');
        if( $helper->isLoggedIn() )    {
            return true;
        }

        return false;
    }

    public function generatePassword()
    {
        $characters = 'abcdefghijklmnopqrstuvwxyz12345689';
        $length = 8;
        $buffer = '';

        for($x=1; $x < $length; $x++)   {
            $random = rand(0, strlen($characters)-1);
            $buffer .= substr($characters, $random, 1);
        }

        return $buffer;
    }

    function isValidateEmail()
    {
        $mode = $this->settings['registration_mode'];

        if($mode == 'require_registration' && !$this->_isLoggedIn())     {
            return true;
        }
        elseif($mode == 'auto_generate_account' && !$this->_isLoggedIn())   {
            return true;
        }

        return false;
    }

    function showLoginLink()
    {
        $mode = $this->settings['registration_mode'];

        if($mode == 'require_registration') {
            return true;
        }
        elseif($mode == 'disable_registration')    {
            return false;
        }
        elseif($mode == 'registration_success')    {
            return true;
        }
        elseif($mode == 'auto_generate_account')    {
            return true;
        }
        elseif($mode == 'allow_guest')    {
            return true;
        }
    }

    protected function _customerEmailExists($email, $websiteId = null)
    {
        $customer = Mage::getModel('customer/customer');
        if ($websiteId) {
            $customer->setWebsiteId($websiteId);
        }
        $customer->loadByEmail($email);
        if ($customer->getId()) {
            return $customer;
        }
        return false;
    }

    function showCreateAccount()
    {
        if($this->settings['registration_mode'] == 'allow_guest')    {
            return true;
        }
        else    {
            return false;
        }
    }

    function hidePasswords()
    {
        $email = $this->getOnepage()->getQuote()->getBillingAddress()->getEmail();
        if($this->settings['registration_order_without_password'] && $email && $email != '')    {
            if($this->_customerEmailExists($email, Mage::app()->getWebsite()->getId()))    {
                return true;
            }
        }
        if($this->settings['registration_mode'] == 'allow_guest')    {

            if(isset($_POST['create_account']) && $_POST['create_account'] == '1')    {
                return false;
            }

            return true;
        }

        return false;
    }

    function showPasswords()
    {
        switch($this->settings['registration_mode'])    {
            case 'require_registration':
                return true;
                break;
            case 'disable_registration':
                return false;
                break;
            case 'registration_success':
                return false;
                break;
            case 'auto_generate_account':
                return false;
                break;
            case 'allow_guest':
                return true;
                break;
        }
    }

    function checkEmailExists()
    {
        switch($this->settings['registration_mode'])    {
            case 'require_registration':
                return true;
                break;

            case 'auto_generate_account':
                return true;
                break;
        }

        return false;
    }

    public function loadConfig()
    {
        $settings = array();
        $items = array(
            'general/serial', 'general/default_country', 'general/default_shipping_method', 'general/default_payment_method',
            'general/enable_geoip', 'general/skin', 'exclude_fields/exclude_city', 'exclude_fields/exclude_telephone', 'general/show_custom_options',
            'exclude_fields/exclude_company', 'exclude_fields/exclude_fax', 'exclude_fields/exclude_region', 'exclude_fields/exclude_zip', 'exclude_fields/enable_comments', 'exclude_fields/enable_discount', 'exclude_fields/exclude_address', 'exclude_fields/exclude_country_id', 'exclude_fields/enable_giftcard',
            'general/geoip_database', 'exclude_fields/enable_newsletter', 'terms/enable_terms',
            'general/checkout_title', 'general/checkout_description','sortordering_fields',
            'terms/terms_title', 'terms/terms_contents', 'general/enable_different_shipping', 'ajax_update/enable_ajax_save_billing',
            'ajax_update/ajax_save_billing_fields', 'ajax_update/enable_update_payment_on_shipping', 'general/enable_gift_messages',
            'registration/registration_mode', 'registration/registration_order_without_password', 'general/hide_nonfree_payment_methods',
            'general/display_tax_included', 'exclude_fields/newsletter_default_checked', 'feedback','addressreview',
            'general/display_full_tax','exclude_fields/enable_comments_default','terms/enable_default_terms','terms/enable_default_terms', 'terms/enable_textarea'
        );

        foreach($items as $config)    {

            $temp = explode('/', $config);
            $name = (!empty($temp[1])) ? $temp[1] : $temp[0];

            $settings[$name] = Mage::getStoreConfig('onestepcheckout/' . $config);
        }

        $isPersistent = false;

        $persistentHelper  = Mage::helper('onestepcheckout')->getPersistentHelper();
        if(is_object($persistentHelper)){
            $isPersistent = $persistentHelper->isPersistent();
        }

        if(!$isPersistent && !$this->getOnePage()->getQuote()->isAllowedGuestCheckout()){
            $settings['registration_mode'] = 'require_registration';
        }

        return $settings;
    }

    public function saveInitialData($billing_data)
    {
        $billing_address = $this->getOnepage()->getQuote()->getBillingAddress();

        $billing_address->setFirstname($billing_data['firstname']);
        $billing_address->setLastname($billing_data['lastname']);
        $billing_address->setCountryId($billing_data['country_id']);
        $billing_address->setCity($billing_data['city']);
        $billing_address->setPostcode($billing_data['postcode']);
        $billing_address->setRegionId($billing_data['region_id']);
        $billing_address->setRegion($billing_data['region']);
        $billing_address->setCompany($billing_data['company']);
        $billing_address->setFax($billing_data['fax']);
        $billing_address->setTelephone($billing_data['telephone']);
        $billing_address->save();


    }

    public function load_exclude_data($data)
    {
        if( $this->settings['exclude_city']  || empty($data['city']))    {
            $data['city'] = '-';
        }

        if( $this->settings['exclude_country_id']  || empty($data['country_id']))    {
            $data['country_id'] = $this->settings['default_country'];
        }

        if( $this->settings['exclude_telephone'] || empty($data['telephone']))    {
            $data['telephone'] = '-';
        }

        if( $this->settings['exclude_region'] || (empty($data['region']) && empty($data['region_id'])))    {
            $data['region'] = '-';
            $data['region_id'] = '999';
        }

        if( $this->settings['exclude_zip'] || empty($data['postcode']))    {
            $data['postcode'] = '-';

        }

        if(!empty($data['country_id']) && $data['postcode'] == '-' && Mage::helper('directory')->isZipCodeOptional($data['country_id'])){
            $data['postcode'] = '';
        }

        if( $this->settings['exclude_company'] || empty($data['company']) )    {
            $data['company'] = '';
        }

        if( $this->settings['exclude_fax'] || empty($data['fax']) )    {
            $data['fax'] = '';
        }

        if( $this->settings['exclude_address'] || empty($data['street']) )    {
            $data['street'][] = '*address missing*';
        }

        $data = $this->cleanValues($data);
        return $data;
    }

    /**
     * Escape form values from html
     * @param  $data
     * @return cleaned value
     */
    public function cleanValues($data) {
        $helper = Mage::helper('core');

        if (is_array($data)) {
            foreach ($data as $value) {
                $value = $this->cleanValues($value);
            }
        } else {
            $data = $helper->escapeHtml($data);
        }

        return $data;
    }

    public function getOnepage()
    {
        return Mage::getSingleton('checkout/type_onepage');
    }

    public function differentShippingAvailable()
    {
        if($this->isVirtual())    {
            return false;
        }

        if($this->settings['enable_different_shipping'])    {
            return true;
        }

        return false;
    }

    public function isVirtual()
    {
        return $this->getOnepage()->getQuote()->isVirtual();
    }

    public function _getAddressError($result, $billing_data, $type = 'billing')
    {
        $errors = array();

        if(is_array($result['message']) && in_array('Please enter city.', $result['message']))    {
            $errors[] = 'city';
        }

        if(is_array($result['message']) && in_array('Please enter first name.', $result['message']))    {
            $errors[] = 'firstname';
        }

        if(is_array($result['message']) && in_array('Please enter last name.', $result['message']))    {
            $errors[] = 'lastname';
        }

        if(is_array($result['message']) && in_array('Please enter street.', $result['message']))    {
            $errors[] = 'address';
        }

        if(is_array($result['message']) && in_array('Please enter zip/postal code.', $result['message']))    {
            $errors[] = 'postcode';
        }

        if(is_array($result['message']) && in_array('Please enter state/province.', $result['message']))    {
            $errors[] = 'region';
        }

        if(is_array($result['message']) && in_array('Please enter telephone.', $result['message']))    {
            $errors[] = 'telephone';
        }
        else    {
            if(!isset($billing_data['telephone']) || trim($billing_data['telephone']) == '') {
                if(!$this->settings['exclude_telephone'])    {
                    $errors[] = 'telephone';
                }
            }
        }



        if( $type == 'billing' )    {

            if(!is_array($result['message']) && substr($result['message'], 0, 21) == 'Invalid email address')    {
                $errors[] = 'email';
            }
            else    {
                if(!isset($billing_data['email']) || trim($billing_data['email']) == '') {
                    $errors[] = 'email';
                }
                else    {
                }
            }


            /*
             if(!isset($billing_data['customer_password']) || $billing_data['customer_password'] == '')    {
             $errors[] = 'password';
             }

             if(!isset($billing_data['confirm_password']) || $billing_data['confirm_password'] == '')    {
             $errors[] = 'confirm_password';
             }
             else    {
             if($billing_data['confirm_password'] !== $billing_data['customer_password']) {
             $errors[] = 'password';
             $errors[] = 'confirm_password';
             }
             }
             */

        }

        return $errors;
    }

    public function getActiveStoreMethods($store = null, $quote = null){

        $store = $quote ? $quote->getStoreId() : null;
        $methods = array();
        $methods = Mage::helper('payment')->getStoreMethods($store, $quote);

        foreach ($methods as $key => $method) {
                if ($this->_canUseMethod($method, $quote)) {
                    unset($methods[$key]);
                    $methods[$key] = $method->getCode();
                } else {
                    unset($methods[$key]);
                }
            }
        $this->methods = $methods;
        return $methods;

    }

    protected function _canUseMethod($method, $quote)
    {
        if (!$method->canUseForCountry($quote->getBillingAddress()->getCountry())) {
            return false;
        }

        if(method_exists($method, 'canUseForCurrency')){
            if (!$method->canUseForCurrency(Mage::app()->getStore()->getBaseCurrencyCode())) {
                return false;
            }
        }

        /**
         * Checking for min/max order total for assigned payment method
         */
        $total = $quote->getBaseGrandTotal();
        $minTotal = $method->getConfigData('min_order_total');
        $maxTotal = $method->getConfigData('max_order_total');

        if((!empty($minTotal) && ($total < $minTotal)) || (!empty($maxTotal) && ($total > $maxTotal))) {
            return false;
        }
        return true;
    }

    public function getDomain()
    {
        $domain = $_SERVER['SERVER_NAME'];
        $temp = explode('.', $domain);
        $exceptions = array(
            'co.uk',
            'com.au',
            'com.br',
            'com.sg'
        );

        $count = count($temp);
        $last = $temp[($count-2)] . '.' . $temp[($count-1)];

        if(in_array($last, $exceptions))    {
            $new_domain = $temp[($count-3)] . '.' . $temp[($count-2)] . '.' . $temp[($count-1)];
        }
        else    {
            $new_domain = $temp[($count-2)] . '.' . $temp[($count-1)];
        }

        return $new_domain;
    }

    public function checkEntry($domain, $serial)
    {
        $key = sha1(base64_decode('b25lc3RlcGNoZWNrb3V0'));
        if(sha1($key.$domain) == $serial)   {
            return true;
        }

        return false;
    }

    public function checkEntryDev($domain, $serial)
    {
        $key = sha1(base64_decode('b25lc3RlcGNoZWNrb3V0X2Rldg=='));
        if(sha1($key.$domain) == $serial)   {
            return true;
        }

        return false;

    }

    public function canRun($dev=false)
    {
        $temp = rtrim(ltrim($this->temp));

        if(!$dev) {
            $original = $this->checkEntry($_SERVER['SERVER_NAME'], $temp);
            $wildcard = $this->checkEntry($this->getDomain(), $temp);
        } else {
            $original = $this->checkEntryDev($_SERVER['SERVER_NAME'], $temp);
            $wildcard = $this->checkEntryDev($this->getDomain(), $temp);
        }

        if(!$original && !$wildcard) {
            return false;
        }

        return true;
    }
    
    public function getFirstName($quote = false)
    {
        $firstName = false;
        if($quote){
            $firstName = $quote->getBillingAddress()->getFirstname();
        }
        if(!$firstName){
            $firstName = Mage::helper('customer')->getCustomer()->getFirstname();
        }
        return $firstName;
    }

    public function getLastName($quote = false)
    {
        $lastName = false;
        if($quote){
            $lastName = $quote->getBillingAddress()->getFirstname();
        }
        if(!$lastName){
            $lastName = Mage::helper('customer')->getCustomer()->getLastname();
        }
        return $lastName;
    }

    public function getEmail($quote = false)
    {
        $email = false;
        if($quote){
            $email = $quote->getBillingAddress()->getEmail();
        }
        if(!$email){
            $email = Mage::helper('customer')->getCustomer()->getEmail();
        }
        return $email;
    }
}
