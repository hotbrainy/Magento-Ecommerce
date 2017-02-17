<?php
/**
 * Custom Publisher Models
 * 
 * Add custom model types, such as author, which can be used as a product
 * attribute while proviting additional details.
 * 
 * @license 	http://opensource.org/licenses/gpl-license.php GNU General Public License, Version 3
 * @copyright	Steven Brown March 12, 2016
 * @author		Steven Brown <steveb.27@outlook.com>
 */

class SteveB27_Publish_Controller_Router extends Mage_Core_Controller_Varien_Router_Abstract
{
    public function initControllerRouters($observer){
        $front = $observer->getEvent()->getFront();
        $front->addRouter('publish', $this);
        
        return $this;
    }
    
    public function match(Zend_Controller_Request_Http $request){
        if (!Mage::isInstalled()) {
            Mage::app()->getFrontController()->getResponse()
                ->setRedirect(Mage::getUrl('install'))
                ->sendResponse();
            exit;
        }
        $urlKey = trim($request->getPathInfo(), '/');
        $check = array();
        $check['author'] = new Varien_Object(array(
            'prefix'        => Mage::getStoreConfig('publish/author/url_prefix'),
            'suffix'        => Mage::getStoreConfig('publish/author/url_suffix'),
            'model'         =>'publish/author',
            'controller'    => 'author',
            'action'        => 'view',
            'param'         => 'id',
        ));
        foreach ($check as $key=>$settings) {
            if ($settings['prefix']){
                $parts = explode('/', $urlKey);
                if ($parts[0] != $settings['prefix'] || count($parts) != 2){
                    continue;
                }
                $urlKey = $parts[1];
            }
            if ($settings['suffix']){
                $urlKey = substr($urlKey, 0 , -strlen($settings['suffix']) - 1);
            }
            $model = Mage::getModel($settings->getModel());
            $id = $model->checkUrlKey($urlKey, Mage::app()->getStore()->getId());
            if ($id){
                if ($settings->getCheckPath() && !$model->load($id)->getStatusPath()) {
                    continue;
                }
                $request->setModuleName('publish')
                    ->setControllerName($settings->getController())
                    ->setActionName($settings->getAction())
                    ->setParam($settings->getParam(), $id);
                $request->setAlias(
                    Mage_Core_Model_Url_Rewrite::REWRITE_REQUEST_PATH_ALIAS,
                    $urlKey
                );
                
                return true;
            }
        }
        
        return false;
    }
}