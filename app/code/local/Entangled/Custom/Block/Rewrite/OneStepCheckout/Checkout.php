<?php

class Entangled_Custom_Block_Rewrite_OneStepCheckout_Checkout extends Idev_OneStepCheckout_Block_Checkout {

    public function _construct(){
        $this->setSubTemplate(true);
        parent::_construct();
    }

    public function getBillingFieldsOrder($fields = array()){

        $fieldsAvailable = array(
            'name' => array('fields' => array('firstname','lastname')),
            'email-phone' => array('fields' =>array('email','telephone')),
            'street' => array(),
            'country-postcode' => array('fields' => array('country_id','postcode')),
            'region_id' => array(),
            'city' => array(),
            'company-fax' => array('fields' => array('company','fax')),
            'taxvat' => array(),
            'dob' => array(),
            'gender' => array(),
            'create_account' => array(),
            'password' => array('has_li' => 1, 'fields' => array('password','confirm_password')),
            'save_in_address_book' => array('has_li' => 1)
        );
        $settings = $this->settings['sortordering_fields'];
        $tmp = array();
        foreach ($fieldsAvailable as $key => $value){
            if(empty($value['fields'])){
                if(!empty($settings[$key]) && !empty($fields[$key]) ){
                    $tmp[$settings[$key]]['fields'][] = $fields[$key];
                    if(!empty($value['has_li'])){
                        $tmp[$settings[$key]]['has_li']=1;
                    }
                }
            } else {
                foreach($value['fields'] as $subfield){
                    if(!empty($settings[$subfield]) && !empty($fields[$subfield]) ){
                        if(empty($placeholder)){
                            $placeholder = $settings[$subfield];
                        }
                        $tmp[$placeholder]['fields'][$settings[$subfield]] = $fields[$subfield];
                    }
                }
                if(!empty($value['has_li']) && !empty($placeholder)){
                    $tmp[$placeholder]['has_li']=1;
                }
                if(!empty($placeholder)){
                    ksort($tmp[$placeholder]['fields']);
                    unset($placeholder);
                }

            }
        }
        ksort($tmp);
        $fields = $tmp ;

        return $fields;
    }

}