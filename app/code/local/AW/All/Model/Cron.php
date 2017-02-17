<?php

class AW_All_Model_Cron
{
    public function run()
    {
        Mage::getModel('awall/feed_extensions')->checkExtensions();
    }
}