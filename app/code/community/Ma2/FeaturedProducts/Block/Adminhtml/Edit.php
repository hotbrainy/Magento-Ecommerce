<?php
/**
 * MagenMarket.com
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * DISCLAIMER
 *
 * Edit or modify this file with yourown risk.
 *
 * @category    Extensions
 * @package     Ma2_FeaturedProducts
 * @copyright   Copyright (c) 2013 MagenMarket. (http://www.magenmarket.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
**/
/* $Id: Edit.php 4 2013-11-05 07:31:07Z linhnt $ */

class Ma2_FeaturedProducts_Block_Adminhtml_Edit extends Mage_Adminhtml_Block_Widget_Grid_Container {

    public function __construct() {


        $this->_blockGroup = 'featuredproducts';
        $this->_controller = 'adminhtml_edit';


        $this->_headerText = Mage::helper('adminhtml')->__('Featured Products Manager');

        parent::__construct();

        $this->_removeButton('add');

        $this->_addButton('save', array(
            'label' => Mage::helper('featuredproducts')->__('Save Selected'),
            'onclick' => 'categorySubmit(\'' . $this->getSaveUrl() . '\')',
            'class' => 'save',
        ));
    }

    public function getSaveUrl() {
        return $this->getUrl('*/*/save', array('store' => $this->getRequest()->getParam('store')));
    }

	private function _prependHtml() {
        $html = '
    	
    	<form id="featured_edit_form" action="' . $this->getSaveUrl() . '" method="post" enctype="multipart/form-data">
			<input name="form_key" type="hidden" value="' . $this->getFormKey() . '" />
    		<div class="no-display">
        		<input type="hidden" name="featured_products" id="saved_featured_products" value="" />
    		</div>
		</form>
    	';

        return $html;
    }

    public function getHeaderHtml() {
        return '<h3 style="background-image: url(' . $this->getSkinUrl('images/product_rating_full_star.gif') . ');" class="' . $this->getHeaderCssClass() . '">' . $this->getHeaderText() . '</h3>';
    }

    protected function _prepareLayout() {
        $this->setChild('store_switcher', $this->getLayout()->createBlock('adminhtml/store_switcher', 'store_switcher')->setUseConfirm(false)
        );
        return parent::_prepareLayout();
    }

    public function getGridHtml() {

        return $this->getChildHtml('store_switcher') . $this->_prependHtml() . $this->getChildHtml('grid');
    }

}
?>