<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Base
 */
class Amasty_Base_Model_Resource_Event_Collection extends Varien_Data_Collection
{
    public function _prepareData($scope) {
        $config = Mage::getConfig()->getNode($scope . '/events')->children();
        $data = array();

        foreach ($config as $node) {
            $eventName = $node->getName();

            foreach ($node->observers->children() as $observer) {
                $data[$eventName][] = array(
                    'class' => Mage::getConfig()->getModelClassName((string) $observer->class),
                    'method' => (string) $observer->method,
                    'scope' => $scope
                );
            }
        }
        return $data;
    }
}