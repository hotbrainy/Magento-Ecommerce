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

class SteveB27_Publish_Block_Adminhtml_Author_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct(){
        parent::__construct();
        $this->_blockGroup = 'publish';
        $this->_controller = 'adminhtml_author';
        $this->_updateButton('save', 'label', Mage::helper('publish')->__('Save Author'));
        $this->_updateButton('delete', 'label', Mage::helper('publish')->__('Delete Author'));
        $this->_addButton('saveandcontinue', array(
            'label'        => Mage::helper('publish')->__('Save And Continue Edit'),
            'onclick'    => 'saveAndContinueEdit()',
            'class'        => 'save',
        ), -100);
        $this->_formScripts[] = "
            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }
    
    public function getHeaderText(){
        if( Mage::registry('current_author') && Mage::registry('current_author')->getId() ) {
            return Mage::helper('publish')->__("Edit Author '%s'", $this->escapeHtml(Mage::registry('current_author')->getName()));
        }
        else {
            return Mage::helper('publish')->__('Add Author');
        }
    }
}