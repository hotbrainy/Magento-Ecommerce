<?php

class Entangled_Custom_Helper_Rewrite_OneStepCheckout_Checkout extends Idev_OneStepCheckout_Helper_Checkout {


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
            $zipResult = json_decode(file_get_contents("http://maps.googleapis.com/maps/api/geocode/json?address=".$data["postcode"]),true);
            if(
                $zipResult["status"] == "OK"
                && isset($zipResult["results"][0])
                && $zipResult["results"][0]["address_components"][0]["types"][0] == "postal_code"
            ){
              foreach($zipResult["results"][0]["address_components"] as $component){
                  if($component["types"][0] == "administrative_area_level_1"){
                      $regionCode = $component["short_name"];
                      $regionModel = Mage::getModel('directory/region')->loadByCode($regionCode, $data["country_id"]);
                      $data['region'] = $regionModel->getName();
                      $data['region_id'] = $regionModel->getId();
                  }
              }
            }
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
}