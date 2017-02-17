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
/* $Id: IndexController.php 4 2013-11-05 07:31:07Z linhnt $ */

class Ma2_FeaturedProducts_IndexController extends Mage_Core_Controller_Front_Action {
    /*
     * Check settings set in System->Configuration and apply them for featured-products page
     * */

    public function indexAction() {

        if (!Mage::helper('featuredproducts')->getIsActive()) {
            $this->_forward('noRoute');
            return;
        }

        $template = Mage::getConfig()->getNode('global/page/layouts/' . Mage::getStoreConfig("featuredproducts/standalone/layout") . '/template');

        $this->loadLayout();

        $this->getLayout()->getBlock('root')->setTemplate($template);
        $this->getLayout()->getBlock('head')->setTitle($this->__(Mage::getStoreConfig("featuredproducts/standalone/meta_title")));
        $this->getLayout()->getBlock('head')->setDescription($this->__(Mage::getStoreConfig("featuredproducts/standalone/meta_description")));
        $this->getLayout()->getBlock('head')->setKeywords($this->__(Mage::getStoreConfig("featuredproducts/standalone/meta_keywords")));

        $breadcrumbsBlock = $this->getLayout()->getBlock('breadcrumbs');
        $breadcrumbsBlock->addCrumb('featured_products', array(
            'label' => Mage::helper('featuredproducts')->__(Mage::helper('featuredproducts')->getPageLabel()),
            'title' => Mage::helper('featuredproducts')->__(Mage::helper('featuredproducts')->getPageLabel()),
        ));

        $this->renderLayout();
    }

}
?>