<?php
/**
 * @category    Bubble
 * @package     Bubble_Elasticsearch
 * @version     4.1.2
 * @copyright   Copyright (c) 2016 BubbleShop (https://www.bubbleshop.net)
 */
define('DS', DIRECTORY_SEPARATOR);
define('PS', PATH_SEPARATOR);

if (isset($_SERVER['SCRIPT_FILENAME']) && is_link($_SERVER['SCRIPT_FILENAME'])) {
    define('BP', dirname($_SERVER['SCRIPT_FILENAME']));
} else {
    define('BP', dirname(__FILE__));
}

// Configure include path
$paths = array();
$paths[] = BP . DS . 'app' . DS . 'code' . DS . 'local';
$paths[] = BP . DS . 'app' . DS . 'code' . DS . 'community';
$paths[] = BP . DS . 'app' . DS . 'code' . DS . 'core';
$paths[] = BP . DS . 'lib';

$appPath = implode(PS, $paths);
set_include_path($appPath . PS . get_include_path());

// Register autoload
spl_autoload_register(function($class) {
    $classFile = str_replace('\\', '/', $class, $count);
    if (!$count) {
        $classFile = str_replace(' ', DS, ucwords(str_replace('_', ' ', $class)));
    }
    $classFile .= '.php';
    include $classFile;
});

header('Content-Type: text/html; charset=UTF-8');
header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
header('Pragma: no-cache');

$html = '';
$q = isset($_GET['q']) ? $_GET['q'] : '';
$found = false;

if ('' !== $q) {
    $store = isset($_GET['store']) ? $_GET['store'] : '';
    $config = new Bubble_Elasticsearch_Config($store);

    try {
        if (!$config->getData()) {
            throw new Exception('Could not find config for autocomplete');
        }

        $client = new Bubble_Elasticsearch_Client($config->getClientConfig());
        $index = new Bubble_Elasticsearch_Index($client, $client->getIndexAlias($store));
        $index->setAnalyzers($config->getAnalyzers());

        $autocomplete = new Bubble_Elasticsearch_Autocomplete($config);
        $html = $autocomplete->search($q, $index);
        $found = true;
    } catch (Exception $e) {
        if (isset($_GET['fallback_url'])) {
            $url = $_GET['fallback_url'] . '?q=' . $q;
            $html = @file_get_contents($url);
        }
    }
}

header('Fast-Autocomplete: ' . ($found ? 'HIT' : 'MISS'));

echo $html;
exit;