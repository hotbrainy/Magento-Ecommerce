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
/* $Id: Thumbnail.php 4 2013-11-05 07:31:07Z linhnt $ */

class Ma2_FeaturedProducts_Block_Adminhtml_Edit_Renderer_Thumbnail extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    protected $_values;

    /**
     * Renders grid column
     *
     * @param   Varien_Object $row
     * @return  string
     */
    public function render(Varien_Object $row)
    {
        $html = 'No image';
        if ($row->getThumbnail() && $row->getThumbnail() != 'noselection'){
          $html = '<a href="'.Mage::helper('catalog/image')->init($row, 'thumbnail').'" target="_blank"><img src="'. Mage::helper('catalog/image')->init($row, 'thumbnail')->resize(50) . '" alt=""/></a>';
        }
        else if ($row->getSmallImage() && $row->getSmallImage() != 'noselection'){
          $html = '<a href="'.Mage::helper('catalog/image')->init($row, 'small_image').'" target="_blank"><img src="'. Mage::helper('catalog/image')->init($row, 'small_image')->resize(50) . '" alt=""/></a>';
        }
        else if ($row->getImage() && $row->getImage() != 'noselection'){
          $html = '<a href="'.Mage::helper('catalog/image')->init($row, 'image').'" target="_blank"><img src="'. Mage::helper('catalog/image')->init($row, 'image')->resize(50) . '" alt=""/></a>';
        }
        
        return $html;
    }
}
?>