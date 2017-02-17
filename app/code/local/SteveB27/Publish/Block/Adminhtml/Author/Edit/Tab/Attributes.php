<?php
/**
 * Custom Publisher Models
 * 
 * Add custom model types, such as author, which can be used as a product
 * attribute while proviting additional details.
 * 
 * @license 	http://opensource.org/licenses/gpl-license.php GNU General Public License, Version 3
 * @copyright	Steven Brown March 12, 2016
 * @author		Steven Brown <steveb.27@outlook.com>
 */

class SteveB27_Publish_Block_Adminhtml_Author_Edit_Tab_Attributes extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm() {
        $form = new Varien_Data_Form();
        $form->setDataObject(Mage::registry('current_author'));
        $fieldset = $form->addFieldset('info',
            array(
                'legend'=>Mage::helper('publish')->__('Author Information'),
                 'class'=>'fieldset-wide',
            )
        );
        $attributes = $this->getAttributes();
        foreach ($attributes as $attribute){
            $attribute->setEntity(Mage::getResourceModel('publish/author'));
        }
        $this->_setFieldset($attributes, $fieldset, array());
        
        if(Mage::registry('current_author')) {
			$formValues = Mage::registry('current_author')->getData();
			$form->addValues($formValues);
		}
        $form->setFieldNameSuffix('author');
        $this->setForm($form);
    }
    
    protected function _prepareLayout() {
        Varien_Data_Form::setElementRenderer(
            $this->getLayout()->createBlock('adminhtml/widget_form_renderer_element')
        );
        Varien_Data_Form::setFieldsetRenderer(
            $this->getLayout()->createBlock('adminhtml/widget_form_renderer_fieldset')
        );
        Varien_Data_Form::setFieldsetElementRenderer(
            $this->getLayout()->createBlock('publish/adminhtml_author_renderer_fieldset_element')
        );
    }
    
    protected function _getAdditionalElementTypes(){
        return array(
            'file'    => Mage::getConfig()->getBlockClassName('publish/adminhtml_author_helper_file'),
            'image' => Mage::getConfig()->getBlockClassName('publish/adminhtml_author_helper_image'),
            'textarea' => Mage::getConfig()->getBlockClassName('adminhtml/catalog_helper_form_wysiwyg')
        );
    }
    
    public function getAuthor() {
        return Mage::registry('current_author');
    }
}