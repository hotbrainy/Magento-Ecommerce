<?php
/**
 * MageWorx
 * All Extension
 *
 * @category   MageWorx
 * @package    MageWorx_All
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_All_Model_Observer
{
    /**
     * Remove not permitted groups from System Configuration Section
     *
     * @param  Varien_Event_Observer $observer
     * @return MageWorx_All_Model_Observer
     */
    public function restrictGroupsAcl($observer)
    {
        $editBlock = $observer->getEvent()->getBlock();

        if (!($editBlock instanceof Mage_Adminhtml_Block_System_Config_Edit)) {
            return $this;
        }

        $sectionCode = Mage::app()->getRequest()->getParam('section');
        if (false === strpos($sectionCode, 'mageworx')) {
            return $this;
        }

        $session = Mage::getSingleton('admin/session');
        $currentSection = Mage::getSingleton('adminhtml/config')->getSections()->$sectionCode;
        $groups = $currentSection->groups[0];
        foreach ($groups as $group => $object){
            if (!$session->isAllowed("system/config/$sectionCode/$group")){
                $currentSection->groups->$group = null;
            }
        }
        return $this;
    }
}