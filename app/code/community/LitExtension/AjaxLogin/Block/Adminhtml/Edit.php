<?php
/**
 * @project     AjaxLogin
 * @package LitExtension_AjaxLogin
 * @author      LitExtension
 * @email       litextension@gmail.com
 */

class LitExtension_AjaxLogin_Block_Adminhtml_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    protected function _prepareLayout()
    {
        $this->setChild('form', $this->getLayout()->createBlock($this->_blockGroup . '/' . 'adminhtml_' . $this->_mode . '_form'));
        return parent::_prepareLayout();
    }

    public function __construct()
    {
        $this->_objectId = 'attribute_id';
        $this->_controller = 'index';
        $this->_blockGroup = 'ajaxlogin';

        parent::__construct();

        $this->_updateButton('back', 'onclick', 'setLocation(\'' . $this->getUrl('*/index/customer') . '\')');

        if ($this->getRequest()->getParam('popup')) {
            $this->_removeButton('back');
            $this->_addButton(
                'close',
                array(
                    'label' => Mage::helper('catalog')->__('Close Window'),
                    'class' => 'cancel',
                    'onclick' => 'window.close()',
                    'level' => -1
                )
            );
        }

        $this->_updateButton('save', 'label', Mage::helper('catalog')->__('Save Attribute'));

        if (!Mage::registry('attributemanager_data')->getIsUserDefined()) {
            $this->_removeButton('delete');
        } else {
            $this->_updateButton('delete', 'label', Mage::helper('catalog')->__('Delete Attribute'));
            $this->_updateButton('delete', 'onclick', "deleteConfirm(
            		'" . Mage::helper('adminhtml')->__('Are you sure you want to do this?') . "',
            		'" . $this->getUrl('*/*/delete/type/' . $this->getRequest()->getParam('type') . '/attribute_id/' . $this->getRequest()->getParam('attribute_id')
                ) . "')");
        }
    }

    public function getHeaderText()
    {

        $types = array(
            'catalog_category' => 'Category',
            'customer' => 'Customer',
            'customer_address' => 'Customer address',
            'order' => 'Order',
            'order_address' => 'Order address',
            'order_item' => 'Order item',
            'order_payment' => 'Order payment',
            'order_status_history' => 'Order status history',
            'invoice' => 'Invoice',
            'invoice_item' => 'Invoice item',
            'invoice_comment' => 'Invoice comment',
        );
        $t = $this->getRequest()->getParam('type');
        if (!isset($types[$t])) {
            $type = 'Category';
        } else {
            $type = $types[$t];
        }
        if (Mage::registry('attributemanager_data')->getId()) {
            return Mage::helper('ajaxlogin')->__('Edit %1$s Attribute "%2$s"', $type, $this->htmlEscape(Mage::registry('attributemanager_data')->getFrontendLabel()));
        } else {
            return Mage::helper('ajaxlogin')->__('New %s Attribute', $type);
        }

    }

    public function getValidationUrl()
    {
        return $this->getUrl('*/*/validate', array('_current' => true));
    }

    public function getSaveUrl()
    {
        return $this->getUrl('*/' . $this->_controller . '/save', array('_current' => true, 'back' => '*/' . $this->_controller . '/customer'));
    }
}
