<?php
use GeoIp2\Database\Reader as Reader;

class Idev_OneStepCheckout_Model_Observers_PresetDefaults extends Mage_Core_Model_Abstract {

    //@TODO together with refactoring system.xml: get rid of this variable and add them as config nodes
    public $defaultFields = array('country_id', 'region', 'region_id', 'city', 'postcode');

    /**
     * shipping rates array
     * @var array
     */
    protected $_rates = array();

    /**
     * ShippingMethod block class
     * @var Mage_Checkout_Block_Onepage_Payment_Methods
     */
    protected $_paymentMethodsBlock = null;

    /**
     * payment methods array
     * @var array
     */
    protected $_methods = array();

    /**
     * Call default set methods wrapper
     *
     * @param Varien_Event_Observer $observer
     * @return Idev_OneStepCheckout_Model_Observers_PresetDefaults
     */
    public function setDefaults(Varien_Event_Observer $observer) {

        $quote = $observer->getEvent()->getQuote();

        if(Mage::getStoreConfig('onestepcheckout/general/rewrite_checkout_links', $quote->getStore())) {
            $this->callDefaults($observer);

        }

        return $this;
    }

    /**
     * Call defaults method
     *
     * @param Varien_Event_Observer $observer
     */
    public function callDefaults (Varien_Event_Observer $observer) {

        $this->setAddressDefaults($observer);
        $this->setShippingDefaults($observer);
        $this->setPaymentDefaults($observer);

        return $this;
    }


    /**
     * If customer logs in and there are default data that is different from entered data we need to reset defaults
     *
     * @param Varien_Event_Observer $observer
     */
    public function setDefaultsOnLogin(Varien_Event_Observer $observer) {

        $quote = Mage::getSingleton('checkout/session')->getQuote();
        if(is_object($quote)){
            $currentBilling = $this->hasDataSet($quote->getBillingAddress());
            $currentPrimaryBilling = $this->hasDataSet($quote->getCustomer()->getPrimaryBillingAddress());
            $difference  = array_diff($currentPrimaryBilling, $currentBilling);
            if(!empty($currentBilling) && !empty($difference)){
                foreach($this->defaultFields as $field){
                    $quote->getBillingAddress()->setData($field, '');
                    $quote->getShippingAddress()->setData($field, '');
                }
            }
            $observer->getEvent()->setQuote($quote);
            if(Mage::getStoreConfig('onestepcheckout/general/rewrite_checkout_links')) {
                $this->callDefaults($observer);
            }
        }

        return $this;
    }

    /**
     * If you have aquired a quote from cart and you are having saved addresses then you can get wrong shipping methods
     *
     * @param Varien_Event_Observer $observer
     */
    public function compareDefaultsFromCart(Varien_Event_Observer $observer) {

        $quote = Mage::getSingleton('checkout/session')->getQuote();

        if(is_object($quote)){

            //extract the data from quote
            $currentBilling = $this->hasDataSet($quote->getBillingAddress());
            $currentShipping = $this->hasDataSet($quote->getShippingAddress());

            $sameAsBilling = $quote->getShippingAddress()->getSameAsBilling();
            $difference = array();

            if($sameAsBilling){
                if(Mage::getSingleton('customer/session')->isLoggedIn()){
                    $selectedAddress = $quote->getBillingAddress()->getCustomerAddressId();
                    if($selectedAddress){
                        $currentShippingOriginal = $this->hasDataSet($quote->getCustomer()->getAddressById($selectedAddress));
                        $difference = array_diff($currentShippingOriginal, $currentShipping);
                    } else {
                        $currentPrimaryBilling = $this->hasDataSet($quote->getCustomer()->getPrimaryBillingAddress());
                        $difference  = array_diff($currentPrimaryBilling, $currentBilling);
                    }
                } else {
                    $difference = array_diff($currentBilling, $currentShipping);
                }

                if(!empty($difference)){
                    $quote->getBillingAddress()->addData($difference)->implodeStreetAddress();
                    $quote->getShippingAddress()->addData($difference)->implodeStreetAddress()->setCollectShippingRates(true);
                }
            }

            $quote->getShippingAddress()->setCollectShippingRates(true);
        }

        return $this;
    }

    /**
     * Callback to see if shipping rates have changed after totals are set to quote.
     *
     * @param Varien_Event_Observer $observer
     * @return Idev_OneStepCheckout_Model_Observers_PresetDefaults
     */
    public function setShippingIfDifferent(Varien_Event_Observer $observer){

        $quote = $observer->getEvent()->getQuote();

        if(!Mage::getStoreConfig('onestepcheckout/general/rewrite_checkout_links', $quote->getStore())) {
            return $this;
        }

        $newCode = Mage::getStoreConfig('onestepcheckout/general/default_shipping_method', $quote->getStore());

        if (empty($newCode)) {
            return $this;
        }

        //request rate calculation
        $quote->getShippingAddress()->collectShippingRates();

        return $this;
    }

    /**
     * Sets the default shipping/billing data to pass validations and reveal data
     *
     * @param Varien_Event_Observer $observer
     * @return Idev_OneStepCheckout_Model_Observers_PresetDefaults
     */
    public function setAddressDefaults(Varien_Event_Observer $observer) {

        $quote = $observer->getEvent()->getQuote();

        $checkPostcode = $this->getCheckPostcode();
        $currentBilling = $this->hasDataSet($quote->getBillingAddress(), $checkPostcode);
        $currentShipping = $this->hasDataSet($quote->getShippingAddress(), $checkPostcode);

        if (!is_object($quote) || (!empty($currentBilling) || !empty($currentShipping))) {
            return $this; // data already set
        }

        $newShipping = $this->getAddressDefaults($quote);
        $newBilling = $newShipping;

        if (empty($newShipping)) {
            return $this; // no data as default means nothing is to set
        }

        //if user is logged in and no data is set else we use defaults
        if (Mage::getSingleton('customer/session')->isLoggedIn() && empty($currentBilling)) {

            //we look for default addresses and extract the data from there
            $currentPrimaryBilling = $this->hasDataSet($quote->getCustomer()->getPrimaryBillingAddress(), $checkPostcode);
            $currentPrimaryShipping = $this->hasDataSet($quote->getCustomer()->getPrimaryShippingAddress(), $checkPostcode);

            //and if we have data we set it to default
            if (empty($currentBilling) && ! empty($currentPrimaryBilling)) {
                $newBilling = $currentPrimaryBilling;
            }
            if (empty($currentShipping) && ! empty($currentPrimaryShipping)) {
                $newShipping = $currentPrimaryShipping;
            }
        }

        //if shipping should be same as billing
        if ($quote->getShippingAddress()->getSameAsBilling()) {
            $newShipping = $newBilling;
        }

        //only add if there is nothing here
        if (empty($currentBilling) && ! empty($newBilling)) {
            $quote->getBillingAddress()->addData($newBilling);
        }

        //only add if there is nothing here
        if (empty($currentShipping) && ! empty($newShipping)) {
            $quote->getShippingAddress()->addData($newShipping);
            $quote->getShippingAddress()->setSameAsBilling(Mage::getStoreConfig('onestepcheckout/general/enable_different_shipping_hide', $quote->getStore()));
        }

        return $this;
    }

    /**
     * Check if postcode is required for ajax update and should be validated
     *
     * @return boolean
     */
    public function getCheckPostcode (){
        return ((strstr(Mage::getStoreConfig('onestepcheckout/ajax_update/ajax_save_billing_fields'),'postcode')) ? true : false);
    }

    /**
     * Get default config values for address
     *
     * @param Mage_Sales_Model_Quote $quote
     * @return array
     */
    public function getAddressDefaults(Mage_Sales_Model_Quote $quote){

        $data = $this->getAddressGeoIP($quote);

        if(!empty($data)){
            return $data;
        }

        if($countryId = Mage::getStoreConfig('onestepcheckout/general/default_country',$quote->getStore())){
            $data['country_id'] = $countryId;
        }
        if($regionId = Mage::getStoreConfig('onestepcheckout/general/default_region_id',$quote->getStore())){
            $data['region_id'] = $regionId;
        }
        if($city = Mage::getStoreConfig('onestepcheckout/general/default_city',$quote->getStore())){
            $data['city'] = $city;
        }
        if($postcode = Mage::getStoreConfig('onestepcheckout/general/default_postcode',$quote->getStore())){
            $data['postcode'] = $postcode;
        }

        return $data;
    }

    /**
     * Get GeoIp data by user ip address
     * @param  Mage_Sales_Model_Quote $quote
     * @return array
     */
    public function getAddressGeoIP(Mage_Sales_Model_Quote $quote)
    {
        if (!$this->addressGeoIp) {

            $data = array();

            $enabled = Mage::getStoreConfig('onestepcheckout/geoip/enable',$quote->getStore());
            $ip = Mage::app()->getRequest()->getClientIp(Mage::getStoreConfig('onestepcheckout/geoip/trustproxy',$quote->getStore()));

            if (!$enabled || !$ip) {
                return $data;
            }

            switch ($enabled) {
                case 'pear_geoip':
                    $data = $this->_getPearGeoIp($ip,$quote);
                    break;
                case 'pecl_geoip':
                    $data = $this->_getPeclGeoIp($ip,$quote);
                    break;
                case 'mod_geoip':
                    $data = $this->_getModGeoIp($ip,$quote);
                    break;
                case 'geoip2_db':
                    $data = $this->_getGeoIp2($ip,$quote);
                    break;
                case 'geoip2_online':
                    $data = $this->_getGeoIp2Online($ip,$quote);
                    break;
                default:
                    ;
                    break;
            }

            $this->addressGeoIp = $data;
        }

        return $this->addressGeoIp;
    }

    /**
     * Get GeoIp data from mod_geoip apache module
     *
     * @param ip
     * @param quote
     */
    protected function _getModGeoIp($ip, $quote = null)
    {
        $data = array();

        try {
            if (function_exists('apache_get_modules')) {

                $mods = apache_get_modules();
                if (array_search('mod_geoip', $mods)) {
                    if (! empty($_SERVER['GEOIP_COUNTRY_CODE'])) {
                        $data['country_id'] = $_SERVER['GEOIP_COUNTRY_CODE'];
                    }
                    if (! empty($_SERVER['GEOIP_REGION']) && ! empty($data['country_id'])) {
                        $data['region_id'] = Mage::getModel('directory/region')->loadByCode($_SERVER['GEOIP_REGION'], $data['country_id'])->getRegionId();
                    }
                    if (! empty($_SERVER['GEOIP_CITY'])) {
                        $data['city'] = utf8_encode($_SERVER['GEOIP_CITY']);
                    }
                    if (! empty($_SERVER['GEOIP_POSTAL_CODE'])) {
                        $data['postcode'] = $_SERVER['GEOIP_POSTAL_CODE'];
                    }
                    // if no information
                    if (empty($data)) {
                        Mage::log(Mage::helper('onestepcheckout')->__('Unable to get GeoIP information, mod_geoip databases are not configured properly or region information for ip: %s not found', $ip), Zend_Log::WARN);
                    }
                } else {
                    // not enabled
                    Mage::throwException(Mage::helper('onestepcheckout')->__('Apache module mod_geoip is not loaded'));
                }
            } else {
                Mage::throwException(Mage::helper('onestepcheckout')->__('method apache_get_modules is not available you are probably using php in cgi mode and if you do then this method is not available'));
            }
        } catch (Exception $e) {
            Mage::logException($e);
        }

        return $data;
    }

    /**
     *
     * Get GeoIp data from Pecl GeoIp php module
     *
     * @param ip
     * @param quote
     */
    protected function _getPeclGeoIp($ip, $quote = null)
    {
        $data = array();

        try {
            // see if extension is loaded
            if (extension_loaded('geoip')) {
                //try city database first
                $record = geoip_record_by_name($ip);
                if ($record) {
                    if (! empty($record['country_code'])) {
                        $data['country_id'] = $record['country_code'];
                    }
                    if (! empty($record['region'])) {
                        $data['region_id'] = Mage::getModel('directory/region')->loadByCode($record['region'], $record['country_code'])->getRegionId();
                    }
                    if (! empty($record['city'])) {
                        $data['city'] = utf8_encode($record['city']);
                    }
                    if (! empty($record['postal_code'])) {
                        $data['postcode'] = $record['postal_code'];
                    }
                } else {
                    //try country database second
                    $record = geoip_country_code_by_name($ip);
                    if ($record) {
                        $data['country_id'] = $record;
                    }
                }
                //if no information
                if (empty($data)) {
                    Mage::log(
                        Mage::helper('onestepcheckout')
                        ->__('Unable to get GeoIP information and seems like Pecl GeoIP databases are not configured properly or region information for ip: %s not found', $ip),
                        Zend_Log::WARN
                    );
                }
            } else {
                //not enabled
                Mage::throwException(Mage::helper('onestepcheckout')->__('Php extension for Pecl GeoIP is not loaded'));
            }
        } catch (Exception $e) {
            Mage::logException($e);
        }

        return $data;
    }

    /**
     *
     * @param ip
     * @param quote
     */
    protected function _getPearGeoIp($ip, $quote = null)
    {
        $database = Mage::getBaseDir('base') . DS . Mage::getStoreConfig('onestepcheckout/geoip/geoip_database',$quote->getStore());

        try {
            if (!@include_once('Net/GeoIP.php')) {
                Mage::throwException(Mage::helper('onestepcheckout')->__('Net/GeoIP pear package is not installed or inaccessible'));
            } else {
                require_once 'Net/GeoIP.php';
                $geoip = Net_GeoIP::getInstance($database);
            }
        } catch (Exception $e) {
            Mage::logException($e);
        }

        try {
            // city database
            if (is_object($geoip) && method_exists($geoip,'lookupLocation')) {
                $location = $geoip->lookupLocation($ip);
                if (! empty($location->countryCode)) {
                    $data['country_id'] = $location->countryCode;
                }
                if (! empty($location->region)) {
                    $data['region_id'] = Mage::getModel('directory/region')->loadByCode($location->region,$location->countryCode)->getRegionId();
                }
                if (! empty($location->city)) {
                    $data['city'] = utf8_encode($location->city);
                }
                if (! empty($location->postalCode)) {
                    $data['postcode'] = $location->postalCode;
                }
                // country database
            } elseif (is_object($geoip) && method_exists($geoip,'lookupCountryCode')) {
                $data['country_id'] = $geoip->lookupCountryCode($ip);
                //no database
            } else {
                Mage::log(
                    Mage::helper('onestepcheckout')->__('Net/GeoIP database %s is not installed properly or is inaccessible or region information for ip: %s not found', $database, $ip),
                    Zend_Log::WARN
                );
            }
        } catch (Exception $e) {
            Mage::logException($e);
        }

        return $data;
    }

    /**
     *
     * @param ip
     * @param quote
     */
    protected function _getGeoIp2($ip, $quote = null)
    {
        $data = array();

        try {
            if (!@include_once('vendor/autoload.php')) {
                Mage::throwException(Mage::helper('onestepcheckout')->__('GeoIp2 or composer package is not installed or inaccessible, OneStepCheckout can\'t use geoIP'));
            } else {
                // Should load all dependencies for Maxmind API
                require_once 'vendor/autoload.php';
            }
        } catch (Exception $e) {
            Mage::logException($e);
            return $data;
        }
        $database = Mage::getBaseDir('base') . DS . Mage::getStoreConfig('onestepcheckout/geoip/geoip_database',$quote->getStore());

        try {
            // Init and query database
            $reader = new Reader($database);
            if (is_object($reader) && method_exists($reader,'city')) {
                $record = $reader->city($ip);
                if (! empty($record->country->isoCode)) {
                    $data['country_id'] = $record->country->isoCode;
                }
                if (! empty($record->mostSpecificSubdivision->isoCode)) {
                    $data['region_id'] = Mage::getModel('directory/region')->loadByCode($record->mostSpecificSubdivision->isoCode,$record->country->isoCode)->getRegionId();
                }
                if (! empty($record->city)) {
                    $data['city'] = utf8_encode($record->city->name);
                }
                if (! empty($record->postal->code)) {
                    $data['postcode'] = $record->postal->code;
                }
                // country database
            } else {
                Mage::log(
                    Mage::helper('onestepcheckout')->__('GeoIp2 database %s is not installed properly or is inaccessible or region information for ip: %s not found', $database, $ip),
                    Zend_Log::WARN
                    );
            }
        } catch (Exception $e) {
            Mage::logException($e);
            return $data;
        }

        return $data;
    }

    /**
     *
     * @param ip
     * @param quote
     */
    protected function _getGeoIp2Online($ip, $quote = null)
    {
        $data = array();

        $client = new Varien_Http_Client('http://freegeoip.net/json/'.$ip);
        $client->setMethod(Varien_Http_Client::GET);

        //more parameters
        try{
            $response = $client->request();

            if ($response->isSuccessful()) {
                $record = Zend_Json::decode($response->getBody());
                if (! empty($record['country_code'])) {
                    $data['country_id'] = $record['country_code'];
                }
                if (! empty($record['region_code']) && ! empty($record['country_code'])) {
                    $data['region_id'] = Mage::getModel('directory/region')->loadByCode($record['region_code'],$record['country_code'])->getRegionId();
                }
                if (! empty($record['city'])) {
                    $data['city'] = utf8_encode($record['city']);
                }
                if (! empty($record['zip_code'])) {
                    $data['postcode'] = $record['zip_code'];
                }
                // country database
            } else {
                Mage::log(
                    Mage::helper('onestepcheckout')->__('GeoIp2 online call failed'),
                    Zend_Log::WARN
                    );
            }
        } catch (Exception $e) {
            Mage::logException($e);
            return $data;
        }
        return $data;
    }

    /**
     * Select and set default shipping method from available methods
     *
     * @param Varien_Event_Observer $observer
     * @return Idev_OneStepCheckout_Model_Observers_PresetDefaults
     */
    public function setShippingDefaults(Varien_Event_Observer $observer) {

        $quote = $observer->getEvent()->getQuote();
        $newCode = Mage::getStoreConfig('onestepcheckout/general/default_shipping_method', $quote->getStore());
        $oldCode = $quote->getShippingAddress()->getShippingMethod();
        $codes = array();

        if (empty($newCode)) {
            return $this;
        }

        foreach ($this->getEstimateRates($quote) as $rates) {
            foreach ($rates as $rate) {
                $codes[] = $rate->getCode();
            }
        }

        if (empty($codes)) {
            return $this;
        }

        $codeCount = (int)count($codes);

        //if we have only one rate available select it no matter what the default is
        if ($codeCount === 1) {
            if(Mage::getStoreConfig('onestepcheckout/general/default_shipping_if_one', $quote->getStore())){
                $newCode = current($codes);
            }
        }

        if (! empty($codes) && (empty($oldCode) || ! in_array($oldCode, $codes))) {
            if (in_array($newCode, $codes)) {
                $quote->getShippingAddress()->setShippingMethod($newCode);
            }
        }

        return $this;
    }

    /**
     * get all shipping rates
     *
     * @param Mage_Sales_Model_Quote $quote
     * @return array
     */
    public function getEstimateRates(Mage_Sales_Model_Quote $quote) {
        if (empty($this->_rates)) {
            $groups = $quote->getShippingAddress()->getGroupedAllShippingRates();
            $this->_rates = $groups;
        }
        return $this->_rates;
    }

    /**
     * Set default payment method for the user
     *
     * @param Varien_Event_Observer $observer
     * @return Idev_OneStepCheckout_Model_Observers_PresetDefaults
     */
    public function setPaymentDefaults(Varien_Event_Observer $observer) {
        $quote = $observer->getEvent()->getQuote();
        $newCode = Mage::getStoreConfig('onestepcheckout/general/default_payment_method', $quote->getStore());

        if (empty($newCode)) {
            return;
        }

        $codes = $this->getPaymentMethods($quote);

        if (empty($codes) || !is_object($quote)|| !$quote->getGrandTotal()) {
            return;
        }

        $codeCount = (int)count($codes);

        //if we have only one rate available select it no matter what the default is
        if ($codeCount === 1 && current($codes) !='free') {
            $newCode = current($codes);
        }

        $oldCode = $quote->getPayment()->getMethod();

        if (!empty($codes) && (empty($oldCode) || !in_array($oldCode, $codes))) {
            if (in_array($newCode, $codes)) {
                //only if method is actually active we can set this as default
                if(Mage::getStoreConfig('payment/'.$newCode.'/active', $quote->getStore())){
                    if ($quote->isVirtual()) {
                        $quote->getBillingAddress()->setPaymentMethod($newCode);
                    } else {
                        $quote->getShippingAddress()->setPaymentMethod($newCode);
                    }
                    try {
                        $quote->getPayment()->setMethod($newCode)->getMethodInstance();
                    } catch ( Exception $e ) {
                        Mage::logException($e);
                    }
                }
            }
        }
    }

    /**
     * Retrieve availale payment methods
     *
     * @param Mage_Sales_Model_Quote $quote
     * @return array
     */
    public function getPaymentMethods(Mage_Sales_Model_Quote $quote) {

        $methods = $this->_methods;
        if (empty($methods)) {
            $store = $quote ? $quote->getStoreId() : null;
            $methodInstances = Mage::helper('payment')->getStoreMethods($store, $quote);
            $total = $quote->getGrandTotal();
            foreach ($methodInstances as $key => $method) {
                if ($this->_canUseMethod($method, $quote)
                        && ($total != 0
                                || $method->getCode() == 'free'
                                || ($quote->hasRecurringItems() && $method->canManageRecurringProfiles()))) {
                    $methods[] = $method->getCode();
                } else {
                    unset($methods[$key]);
                }
            }

            $this->_methods = $methods;
        }
        return $this->_methods;
    }

    /**
     * Check if method can be used
     *
     * @param string $method
     * @param object $quote
     * @return boolean
     */
    protected function _canUseMethod($method, $quote)
    {
        if (!$method->canUseForCountry($quote->getBillingAddress()->getCountry())) {
            return false;
        }

        if (method_exists($method,'canUseForCurrency') && !$method->canUseForCurrency(Mage::app()->getStore()->getBaseCurrencyCode())) {
            return false;
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

    /**
     * Check if object has values or default values set
     *
     * @param Mage_Sales_Model_Quote_Address $addressObject
     * @return array();
     */
    public function hasDataSet($address, $checkPostcode = false){

        $data = array();

        if(is_object($address)){
            foreach($address->getData() as $key => $value){
                if(in_array($key, $this->defaultFields) && !empty($value)){
                    $data[$key] = $value;
                }
            }
        }

        if($checkPostcode && empty($data['postcode'])){
            $data = array();
        }
        return $data;
    }

}
