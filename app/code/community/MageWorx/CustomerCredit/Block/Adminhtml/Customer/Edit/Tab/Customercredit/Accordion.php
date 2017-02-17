<?php

/**
 * MageWorx
 * Loyalty Booster Extension
 *
 * @category   MageWorx
 * @package    MageWorx_CustomerCredit
 * @copyright  Copyright (c) 2016 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_CustomerCredit_Block_Adminhtml_Customer_Edit_Tab_CustomerCredit_Accordion extends Mage_Adminhtml_Block_Widget_Accordion
{
    protected function _prepareLayout()
    {
        $this->setId('customercreditAccordion');
        
        $this->addItem('log', array(
            'title'       => Mage::helper('mageworx_customercredit')->__('Activity Log'),
            'ajax'        => true,
            'content_url' => $this->getUrl('adminhtml/mageworx_customercredit_credit/logGrid', array('_current' => true)),
        ));
    }
}