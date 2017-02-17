<?php

class Magestore_Magenotification_Model_Mysql4_Feedback extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init('magenotification/feedback', 'feedback_id');
    }
}