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
 
class SteveB27_Publish_Helper_Author extends Mage_Core_Helper_Abstract
{
    public function getAuthorsUrl(){
        return Mage::getUrl('publish/author/index');
    }
    
    public function getUseBreadcrumbs(){
        return Mage::getStoreConfigFlag('publish/author/breadcrumbs');
    }
    
    public function isRssEnabled(){
        return  Mage::getStoreConfigFlag('rss/config/active') && Mage::getStoreConfigFlag('publish/author/rss');
    }
    
    public function getRssUrl(){
        return Mage::getUrl('publish/author/rss');
    }
    
    public function getFileBaseDir(){
        return Mage::getBaseDir('media').DS.'author'.DS.'file';
    }
    
    public function getFileBaseUrl(){
        return Mage::getBaseUrl('media').'author'.'/'.'file';
    }
    
    public function getAttributeSourceModelByInputType($inputType){
        $inputTypes = $this->getAttributeInputTypes();
        if (!empty($inputTypes[$inputType]['source_model'])) {
            return $inputTypes[$inputType]['source_model'];
        }
        return null;
    }
    
    public function getAttributeInputTypes($inputType = null){
        $inputTypes = array(
            'multiselect'   => array(
                'backend_model'     => 'eav/entity_attribute_backend_array'
            ),
            'boolean'       => array(
                'source_model'      => 'eav/entity_attribute_source_boolean'
            ),
            'file'          => array(
                'backend_model'     => 'publish/attribute_backend_file'
            ),
            'image'         => array(
                'backend_model'     => 'publish/attribute_backend_image'
            ),
        );

        if (is_null($inputType)) {
            return $inputTypes;
        } else if (isset($inputTypes[$inputType])) {
            return $inputTypes[$inputType];
        }
        return array();
    }
    public function getAttributeBackendModelByInputType($inputType){
        $inputTypes = $this->getAttributeInputTypes();
        if (!empty($inputTypes[$inputType]['backend_model'])) {
            return $inputTypes[$inputType]['backend_model'];
        }
        return null;
    }
    public function authorAttribute($article, $attributeHtml, $attributeName){
        $attribute = Mage::getSingleton('eav/config')->getAttribute(SteveB27_Publish_Model_Author::ENTITY, $attributeName);
        if ($attribute && $attribute->getId() && !$attribute->getIsWysiwygEnabled()) {
            if ($attribute->getFrontendInput() == 'textarea') {
                $attributeHtml = nl2br($attributeHtml);
            }
        }
        if ($attribute->getIsWysiwygEnabled()) {
            $attributeHtml = $this->_getTemplateProcessor()->filter($attributeHtml);
        }
        return $attributeHtml;
    }
    protected function _getTemplateProcessor(){
        if (null === $this->_templateProcessor) {
            $this->_templateProcessor = Mage::helper('catalog')->getPageTemplateProcessor();
        }
        return $this->_templateProcessor;
    }

    public function getProductAuthorsCollection($product){
        $_authors = explode(',',$product->getResource()->getAttributeRawValue($product->getId(),'publish_author', Mage::app()->getStore()));
        $authorsCollection = Mage::getModel('publish/author')->getCollection()
            ->addFieldToFilter("entity_id",array("in"=>$_authors))
            ->addAttributeToSelect("name");

        return $authorsCollection;
    }

    public function getProductAuthorsNames($product){
        $collection = $this->getProductAuthorsCollection($product);
        $authors = array();
        foreach($collection as $author){
            $authors[] = $author->getName();
        }

        return $authors;
    }

    public function getAuthorsList($product,$formatted = true){
        if(is_numeric($product)){
            $product = Mage::getModel("catalog/product")->load($product);
        }
        $_authors = explode(',',$product->getPublishAuthor());
        $authorCount = count($_authors);
        $authorHtml = array();
        if($authorCount > 0) {
            $author_names = array();
            foreach ($_authors as $_author) {
                $author = Mage::getModel('publish/author')->load($_author);
                if($formatted){
                    $authorHtml[] = '<a href="' . $author->getAuthorUrl() . '">' . $author->getName() . '</a>';
                }else{
                    $authorHtml[] = $author->getName();
                }
            }
        }else{
            return "";
        }
        return $authorHtml;
    }

    public function getAuthorUrl($product){
        if(is_numeric($product)){
            $product = Mage::getModel("catalog/product")->load($product);
        }
        $_authors = explode(',',$product->getPublishAuthor());
        $authorCount = count($_authors);
        if($authorCount > 0) {
            $author_names = array();
            foreach ($_authors as $_author) {
                $author = Mage::getModel('publish/author')->load($_author);
                return $author->getAuthorUrl();
            }
        }

        return "";
    }
}