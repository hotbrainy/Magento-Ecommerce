<?php

/**
 * Import rule for Magento Community
 * @param $fromUrl
 * @param $toUrl
 */
function createRuleCommunity($fromUrl, $toUrl)
{
    // Create rewrite:
    /** @var Mage_Core_Model_Url_Rewrite $rewrite */
    $rewrite = Mage::getModel('core/url_rewrite');

    // Check for existing rewrites:
        // Attempt loading it first, to prevent duplicates:
        $rewrite->loadByIdPath($fromUrl);

        $rewrite->setStoreId(Mage::app()->getDefaultStoreView());
        $rewrite->setOptions('RP');
        $rewrite->setIdPath($fromUrl);
        $rewrite->setRequestPath($fromUrl);
        $rewrite->setIsSystem(0);
        $rewrite->setTargetPath($toUrl);

        $rewrite->save();
}

set_time_limit(0);
ini_set('display_errors', 'On');
error_reporting(E_ALL);
require '../app/Mage.php';
Mage::app('default');

$file = file_get_contents("existing_url.csv");
$found = $notFound = array();
foreach(explode("\n",$file) as $website){
    $slug = str_replace("http://entangledpublishing.com/","",$website);
    $slug = substr($slug,0,-1);
    $product = Mage::getModel("catalog/product")->loadByAttribute("url_key",$slug);
    if($product && $product->getId()){
        $found[] = $slug;
        createRuleCommunity($slug,$product->getUrlPath());

    }else{
        $notFound[] = $slug;
    }
}

echo "<br><br>Products Found: ".count($found)."<br>";
echo implode("<br>",$found);
echo "<br><br>Products not found ".count($notFound)."<br>";
echo implode("<br>",$notFound);