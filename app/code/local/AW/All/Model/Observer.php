<?php
class AW_All_Model_Observer
{
    public function prepareAWTabs()
    {
        $layout = Mage::app()->getLayout();
        $leftBlock = $layout->getBlock('left');
        $tabsBlock = null;
        foreach ($leftBlock->getChild() as $child) {
            if ($child instanceof Mage_Adminhtml_Block_System_Config_Tabs) {
                $tabsBlock = $child;
                break;
            }
        }
        if (null === $tabsBlock) {
            return $this;
        }
        foreach ($tabsBlock->getTabs() as $tab) {
            if ($tab->getId() != 'awall' || null === $tab->getSections()) {
                continue;
            }
            $_sections = $tab->getSections()->getItems();
            $tab->getSections()->clear();

            $_sectionLabelList = array();
            $_sectionList = array();
            foreach ($_sections as $key => $_section) {
                if (!in_array($key, array('awall'))) {
                    $_sectionLabelList[] = strtolower(str_replace(' ', '_', $_section->getLabel()));
                    $_sectionList[] = $_section;
                }
            }
            array_multisort($_sectionLabelList, SORT_ASC, SORT_STRING, $_sectionList);

            foreach ($_sectionList as $_section) {
                $tab->getSections()->addItem($_section);
            }


            if (array_key_exists('awall', $_sections)) {
                $tab->getSections()->addItem($_sections['awall']);
            }
        }
        return $this;
    }
}