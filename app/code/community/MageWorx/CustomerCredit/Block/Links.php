<?php

/**
 * MageWorx
 * Loyalty Booster Extension
 *
 * @category   MageWorx
 * @package    MageWorx_CustomerCredit
 * @copyright  Copyright (c) 2016 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_CustomerCredit_Block_Links extends Mage_Page_Block_Template_Links_Block
{
    /**
     * Position in link list
     * @var int
     */
    protected $_position = 40;

    /**
     * Set link title, label and url
     */
    public function __construct() {
        parent::__construct();
        if ($this->helper('mageworx_customercredit')->isShowCustomerCredit()) {
            $text = $this->__('My Credit');
            $this->_label = $text;
            $this->_title = $text;
            $this->_url = $this->getUrl('customercredit');
        }
    }
}