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

class AW_All_Model_Feed_Updates extends AW_All_Model_Feed_Abstract
{

    /**
     * Retrieve feed url
     *
     * @return string
     */
    public function getFeedUrl()
    {
        return AW_All_Helper_Config::UPDATES_FEED_URL;
    }

    /**
     * Checks feed
     * @return
     */
    public function check()
    {
        if ((time() - Mage::app()->loadCache('aw_all_updates_feed_lastcheck')) > Mage::getStoreConfig('awall/feed/check_frequency')) {
            $this->refresh();
        }
    }

    public function refresh()
    {
        $feedData = array();

        try {

            $Node = $this->getFeedData();
            if (!$Node) return false;
            foreach ($Node->children() as $item) {

                if ($this->isInteresting($item)) {
                    $date = strtotime((string)$item->date);
                    if (!Mage::getStoreConfig('awall/install/run') || (Mage::getStoreConfig('awall/install/run') < $date)) {
                        $feedData[] = array(
                            'severity' => 3,
                            'date_added' => $this->getDate((string)$item->date),
                            'title' => (string)$item->title,
                            'description' => (string)$item->content,
                            'url' => (string)$item->url,
                        );
                    }
                }
            }

            $adminnotificationModel = Mage::getModel('adminnotification/inbox');
            if ($feedData && is_object($adminnotificationModel)) {
                $adminnotificationModel->parse(($feedData));
            }

            Mage::app()->saveCache(time(), 'aw_all_updates_feed_lastcheck');
            return true;
        } catch (Exception $E) {
            return false;
        }
    }


    public function getInterests()
    {
        if (!$this->getData('interests')) {
            $types = @explode(',', Mage::getStoreConfig('awall/feed/interests'));
            $this->setData('interests', $types);
        }
        return $this->getData('interests');
    }

    /**
     *
     * @return
     */
    public function isInteresting($item)
    {
        $interests = $this->getInterests();

        $types = @explode(",", (string)$item->type);
        $exts = @explode(",", (string)$item->extensions);

        $isInterestedInSelfUpgrades = array_search(AW_All_Model_Source_Updates_Type::TYPE_INSTALLED_UPDATE, $types);

        foreach ($types as $type) {

            if (array_search($type, $interests) !== false) {
                return true;
            }
            if (($type == AW_All_Model_Source_Updates_Type::TYPE_UPDATE_RELEASE) && $isInterestedInSelfUpgrades) {
                foreach ($exts as $ext) {
                    if ($this->isExtensionInstalled($ext)) {
                        return true;
                    }
                }
            }
        }
        return false;
    }

    public function isExtensionInstalled($code)
    {
        $modules = array_keys((array)Mage::getConfig()->getNode('modules')->children());

        foreach ($modules as $moduleName) {
            if ($moduleName == $code) {
                return true;
            }
        }
        return false;
    }

}