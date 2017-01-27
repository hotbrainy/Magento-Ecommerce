<?php
/**
 * @project     AjaxLogin
 * @package LitExtension_AjaxLogin
 * @author      LitExtension
 * @email       litextension@gmail.com
 */

class LitExtension_AjaxLogin_Model_Attributemanager extends Mage_Eav_Model_Entity_Attribute
{
    public function _construct()
    {
        parent::_construct();
    }

    protected function _beforeSave()
    {
        if ( $this->getFrontendInput()=="image"){
            $this->setBackendModel('catalog/category_attribute_backend_image');
            $this->setBackendType('varchar');
        }

        if ( $this->getFrontendInput()=="date"){
            $this->setBackendModel('eav/entity_attribute_backend_datetime');
            $this->setBackendType('datetime');
        }

        if ( $this->getFrontendInput()=="textarea" ){

            $this->setBackendType('text');
        }

        if ( $this->getFrontendInput()=="text" ){

            $this->setBackendType('varchar');
        }

        if ( ($this->getFrontendInput()=="multiselect" || $this->getFrontendInput()=="select") ){
            $this->setData('source_model', 'eav/entity_attribute_source_table');
            $this->setBackendType('varchar');
        }

        if ($this->getFrontendInput()=="boolean"){
            $this->setData('source_model', 'eav/entity_attribute_source_boolean');
            $this->setBackendType('int');
            $this->setFrontendInput("select");
        }

        return parent::_beforeSave();
    }
}
