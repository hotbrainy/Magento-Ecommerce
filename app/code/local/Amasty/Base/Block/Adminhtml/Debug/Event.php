<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Base
 */
class Amasty_Base_Block_Adminhtml_Debug_Event extends Mage_Adminhtml_Block_Widget_Form
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('amasty/ambase/debug/event.phtml');
    }

    function getEventsList()
    {
        return Mage::helper('ambase')->getEventsList();
    }
}
