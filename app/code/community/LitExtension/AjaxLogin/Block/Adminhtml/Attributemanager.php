<?php
/**
 * @project     AjaxLogin
 * @package LitExtension_AjaxLogin
 * @author      LitExtension
 * @email       litextension@gmail.com
 */

class LitExtension_AjaxLogin_Block_Adminhtml_Attributemanager  extends Mage_Adminhtml_Block_Template
{
    public function _prepareLayout()
    {
        return parent::_prepareLayout();
    }

    public function _construct()
    {
        parent::_construct();
        $this->setTemplate('le_ajaxlogin/index.phtml');
        $this->getAttributemanager();
    }

    public function getAttributemanager()
    {
        if (!$this->hasData('attributemanager/index')) {
            $this->setData('attributemanager/index', Mage::registry('attributemanager/index'));
        }

        return $this->getData('attributemanager/index');
    }
}
