<?php

/**
 * aheadWorks Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://ecommerce.aheadworks.com/LICENSE-M1.txt
 *
 * @category   AW
 * @package    AW_All
 * @copyright  Copyright (c) 2009-2010 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/LICENSE-M1.txt
 */
class AW_All_Block_Notification_Window extends Mage_Adminhtml_Block_Notification_Window
{
    protected function _construct()
    {
        parent::_construct();

        if (!Mage::getStoreConfig('awall/install/run')) {
            $c = Mage::getModel('core/config_data');
            $c
                ->setScope('default')
                ->setPath('awall/install/run')
                ->setValue(time())
                ->save();
            $this->setHeaderText($this->__("aheadWorks Notifications Setup"));
            $this->setIsFirstRun(1);
            $this->setIsHtml(1);
        }
    }

    protected function _toHtml()
    {
        if ($this->getIsHtml()) {
            $this->setTemplate('aw_all/notification/window.phtml');
        } else {
            $this->setTemplate('aw_all/notification/window/standard.phtml');
        }
        return parent::_toHtml();
    }

    public function presetFirstSetup()
    {

    }

    public function getNoticeMessageText()
    {
        if ($this->getIsFirstRun()) {
            $child = $this->getLayout()->createBlock('core/template')->setTemplate('aw_all/notification/window/first-run.phtml')->toHtml();
            return $child;
        } else {
            return $this->getData('notice_message_text');
        }
    }
}
