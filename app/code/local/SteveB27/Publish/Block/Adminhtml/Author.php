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

class SteveB27_Publish_Block_Adminhtml_Author extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct(){
        $this->_controller         = 'adminhtml_author';
        $this->_blockGroup         = 'publish';
        parent::__construct();
        $this->_headerText         = Mage::helper('publish')->__('Author');
        $this->_updateButton('add', 'label', Mage::helper('publish')->__('Add Author'));

        $this->setTemplate('publish/grid.phtml');
    }
}