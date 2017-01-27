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
/* $Id: Name.php 4 2013-11-05 07:31:07Z linhnt $ */

class Ma2_FeaturedProducts_Block_Adminhtml_Edit_Renderer_Name extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    protected $_values;

    /**
     * Renders grid column
     *
     * @param   Varien_Object $row
     * @return  string
     */
    public function render(Varien_Object $row) {

        $action_name = $this->getRequest()->getActionName();

        if ($action_name == 'exportCsv' || $action_name == 'exportXml') {
            return $row->getName();
        }

        $href = $this->getUrl('*/catalog_product/edit', array(
            'store' => $this->getRequest()->getParam('store'),
            'id' => $row->getId()));

        $html = '<a href="' . $href . '">' . $row->getName() . '</a>';

        return $html;
    }

}
?>
