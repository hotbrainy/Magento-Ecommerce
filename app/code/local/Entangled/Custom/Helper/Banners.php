<?php

class Entangled_Custom_Helper_Banners extends Mage_Core_Helper_Abstract {

    const IMPRINTS_ATTRIBUTE = "book_imprint";

    public function getCurrentSlides(){
        $imprintIds = Mage::app()->getRequest()->getParam(self::IMPRINTS_ATTRIBUTE);
        if($imprintIds){
            $ids = explode(",",$imprintIds);

            $slides = array();
            $indexedValues = array();
            $attributeValues = $this->getImprintValues();
            foreach($attributeValues as $attributeValue){
                $indexedValues[$attributeValue["value"]] = $this->slugify($attributeValue["label"]);
            }

            foreach($ids as $id){
                if(isset($indexedValues[$id])){
                    $code = $indexedValues[$id];

                    $slides = array_merge($slides,$this->_getImprintSlides($code));
                }
            }
            return count($slides) ? $slides : false;
        }

        return false;
    }

    public function getImprintValues(){
        $attributeInfo = Mage::getResourceModel('eav/entity_attribute_collection')
            ->setCodeFilter(self::IMPRINTS_ATTRIBUTE)->getFirstItem();
        $attributeId = $attributeInfo->getAttributeId();
        $attributeModel = Mage::getModel('catalog/resource_eav_attribute')->load($attributeId);

        return $attributeModel ->getSource()->getAllOptions(false);
    }

    protected function _getImprintSlides($code,$limit = 3){
        $mediaFolder = $this->_getSlidesBasePath();
        $files = array();
        foreach(glob($mediaFolder.$code.".*") as $file){
            if(count($files) >= 3){
                break;
            }
            $filename = str_replace($mediaFolder,"",$file);
            $url = $this->_getSlidesBaseUrl() . $filename;
            $files[] = $this->_getSlideData($url);
        }
        return $files;
    }

    protected function _getSlideData($url){
        return array (
            'banner_id' => '6',
            'name' => 'Ad Banner 1',
            'order_banner' => '1',
            'bannerslider_id' => '3',
            'status' => '0',
            'click_url' => NULL,
            'imptotal' => '0',
            'clicktotal' => '0',
            'tartget' => '0',
            'image' => $url,
            'image_alt' => NULL,
            'width' => NULL,
            'height' => NULL,
            'start_time' => '2016-06-30 10:10:00',
            'end_time' => '2017-07-30 10:10:00',
        );
    }

    protected function _getSlidesBasePath(){
        return Mage::getBaseDir("media") . DS . "wysiwyg" . DS . "banners" . DS;
    }

    protected function _getSlidesBaseUrl(){
        return Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB, true )."media/wysiwyg/banners/";
    }

    public function slugify($text){
        // replace non letter or digits by -
        $text = preg_replace('~[^\pL\d]+~u', '-', $text);

        // transliterate
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

        // remove unwanted characters
        $text = preg_replace('~[^-\w]+~', '', $text);

        // trim
        $text = trim($text, '-');

        // remove duplicate -
        $text = preg_replace('~-+~', '-', $text);

        // lowercase
        $text = strtolower($text);

        if (empty($text)) {
            return 'n-a';
        }

        return $text;
    }

}