<?php
/**
 * @category    Bubble
 * @package     Bubble_Elasticsearch
 * @version     4.1.2
 * @copyright   Copyright (c) 2016 BubbleShop (https://www.bubbleshop.net)
 */
class Bubble_Elasticsearch_Model_Autoload
{
    /**
     * Try autoloading namespace classes before regular Magento classes
     *
     * @param string $class
     */
    public static function load($class)
    {
        $classFile = BP . DS . 'lib' . DS . str_replace('\\', DS, $class, $count) . '.php';
        if ($count > 0 && is_file($classFile)) {
            include $classFile;
        }
    }
}