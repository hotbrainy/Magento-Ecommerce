<?php

class Magestore_Magenotification_Model_Mysql4_Feedbackmessage extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init('magenotification/feedbackmessage', 'feedbackmessage_id');
    }
}