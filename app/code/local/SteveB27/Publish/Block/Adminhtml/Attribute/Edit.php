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

class SteveB27_Publish_Block_Adminhtml_Attribute_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct() {
        $this->_objectId = 'attribute_id';
        $this->_controller = 'adminhtml_attribute';
        $this->_blockGroup = 'publish';

        parent::__construct();
        $this->_addButton(
            'save_and_edit_button',
            array(
                'label'     => Mage::helper('publish')->__('Save and Continue Edit'),
                'onclick'   => 'saveAndContinueEdit()',
                'class'     => 'save'
            ),
            100
        );
        $this->_updateButton('save', 'label', Mage::helper('publish')->__('Save Attribute'));
        $this->_updateButton('save', 'onclick', 'saveAttribute()');

        if (!Mage::registry('entity_attribute')->getIsUserDefined()) {
            $this->_removeButton('delete');
        } else {
            $this->_updateButton('delete', 'label', Mage::helper('publish')->__('Delete Attribute'));
        }
    }
    
    public function getHeaderText(){
        if (Mage::registry('entity_attribute')->getId()) {
            $frontendLabel = Mage::registry('entity_attribute')->getFrontendLabel();
            if (is_array($frontendLabel)) {
                $frontendLabel = $frontendLabel[0];
            }
            return Mage::helper('publish')->__('Edit Attribute "%s"', $this->htmlEscape($frontendLabel));
        }
        else {
            return Mage::helper('publish')->__('New Attribute');
        }
    }
    
    public function getValidationUrl(){
        return $this->getUrl('*/*/validate', array('_current'=>true));
    }
    
    public function getSaveUrl(){
        return $this->getUrl('*/'.$this->_controller.'/save', array('_current'=>true, 'back'=>null));
    }
}