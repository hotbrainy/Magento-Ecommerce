<?php
/**
 * Classy Llama
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email to us at
 * support+paypal@classyllama.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module
 * to newer versions in the future. If you require customizations of this
 * module for your needs, please write us at sales@classyllama.com.
 *
 * To report bugs or issues with this module, please email support+paypal@classyllama.com.
 * 
 * @category   CLS
 * @package    Paypal
 * @copyright  Copyright (c) 2014 Classy Llama Studios, LLC (http://www.classyllama.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class CLS_Paypal_Block_Adminhtml_Sales_Order_Create_Customer extends Mage_Adminhtml_Block_Sales_Order_Create_Customer
{

    /**
     * Additionally render "Create Guest Order" button
     *
     * @return string
     */
    public function getButtonsHtml()
    {
        $html = parent::getButtonsHtml();

        // "Create Guest Order" button
        $addButtonData = array(
            'label'     => Mage::helper('cls_paypal')->__('Create Guest Order'),
            'onclick'   => "order.setLoadBaseUrl('{$this->getGuestLoadBlockUrl()}'); order.setCustomerId(false);",
            'style'     => 'margin-left: 6px'
        );
        $html .= $this->getLayout()->createBlock('adminhtml/widget_button')->setData($addButtonData)->toHtml();

        return $html;
    }

    /**
     * Retrieve url for loading blocks (guest order)
     * @return string
     */
    public function getGuestLoadBlockUrl()
    {
        return $this->getUrl('*/*/loadBlock', array('is_guest_order' => true));
    }

}
