<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Base
 */

$this->startSetup();

Amasty_Base_Helper_Module::baseModuleInstalled();

$feedData = array();
$feedData[] = array(
    'severity'      => 4,
    'date_added'    => gmdate('Y-m-d H:i:s', time()),
    'title'         => 'Amasty`s extension has been installed. Remember to flush all cache, recompile, log-out and log back in.',
    'description'   => 'You can see versions of the installed extensions right in the admin, as well as configure notifications about major updates.',
    'url'           => 'http://amasty.com/news/updates-and-notifications-configuration-9.html'
//    'url'           => Mage::getModel('adminhtml/url')->getUrl('adminhtml/system_config/edit', array('section'=>'ambase')),
);

Mage::getModel('adminnotification/inbox')->parse($feedData);

$this->endSetup();