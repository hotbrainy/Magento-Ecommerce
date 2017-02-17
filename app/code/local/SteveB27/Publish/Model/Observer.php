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
 
class SteveB27_Publish_Model_Observer
{
    public function addItemsToTopmenuItems($observer) {
        $menu = $observer->getMenu();
        $tree = $menu->getTree();
        $action = Mage::app()->getFrontController()->getAction()->getFullActionName();

        $articleNodeId = 'author';
        $data = array(
            'name' => Mage::helper('publish')->__('Authors'),
            'id' => $articleNodeId,
            'url' => Mage::helper('publish/author')->getAuthorsUrl(),
            'is_active' => ($action == 'publish_author_index' || $action == 'publish_author_view')
        );
        $articleNode = new Varien_Data_Tree_Node($data, 'id', $tree, $menu);
        $menu->addChild($articleNode);
        return $this;
    }
}