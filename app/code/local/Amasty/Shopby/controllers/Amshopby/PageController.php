<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */  
class Amasty_Shopby_Amshopby_PageController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        $this->loadLayout(); 
        $this->_setActiveMenu('catalog/amshopby/pages');
        $this->_addContent($this->getLayout()->createBlock('amshopby/adminhtml_page'));
        $this->renderLayout();
    }

    public function newAction() 
    {
        $this->editAction(); 
    }
    
    public function editAction() 
    {
        $id     = (int) $this->getRequest()->getParam('id');
        $model  = Mage::getModel('amshopby/page')->load($id);

        $cond = $model->getCond();
        if ($cond){
            $cond = unserialize($cond);
            $i=0;
            foreach ($cond as $k=>$v){
                /*
                 * Compatibility
                 */
                if (!is_array($v)) {
                    $model->setData('attr_' . $i, $k);
                    $model->setData('option_' . $i, $v);
                } else {
                    /*
                     * New Logic
                     */
                    $model->setData('attr_' . $i, $v['attribute_code']);
                    $model->setData('option_' . $i, $v['attribute_value']);
                    
                }
                ++$i;
            }
        }

        if ($id && !$model->getId()) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('amshopby')->__('Page does not exist'));
            $this->_redirect('*/*/');
            return;
        }
        
        $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
        if (!empty($data)) {
            $model->setData($data);
        } else {
            $this->prepareForEdit($model);
        }
        
        Mage::register('amshopby_page', $model);
        
        $this->loadLayout();
        $this->_setActiveMenu('catalog/amshopby');
        $this->_title('Edit Page');
        $this->_addContent($this->getLayout()->createBlock('amshopby/adminhtml_page_edit'));
        
        $this->renderLayout();
    }

    public function saveAction() 
    {
        $id     = $this->getRequest()->getParam('id');
        $model  = Mage::getModel('amshopby/page');
        $data   = $this->getRequest()->getPost();
        if (isset($data['multistore'])){
            foreach ($data['multistore'] as $key=>$value){
                $data[$key] = serialize($value);
            }
        }
        if ($data) {
            $model->setData($data)->setId($id);
            
            try {
                $this->prepareForSave($model);

                $cond = array();
                for ($i=0; $i < $model->getNum(); ++$i){
                    $cond[] = array(
                        'attribute_code' => $model->getData('attr_' . $i),
                        'attribute_value' => $model->getData('option_' . $i)
                    );
                }
                
                $model->setCond(serialize($cond));
                
                $model->save();
                Mage::getSingleton('adminhtml/session')->setFormData(false);
                
                $msg = Mage::helper('amshopby')->__('Page has been successfully saved');
                Mage::getSingleton('adminhtml/session')->addSuccess($msg);
                if ($this->getRequest()->getParam('continue')){
                    $this->_redirect('*/*/edit', array('id' => $model->getId()));
                }
                else {
                    $this->_redirect('*/*');
                }
               
                
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array('id' => $id));
            }    
                        
            return;
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('amshopby')->__('Unable to find a page to save'));
        $this->_redirect('*/*/');
    } 
    
    public function optionsAction()
    {
        $name = 'option_' . substr($this->getRequest()->getParam('name'),-1);
        $result = '<input id="'.$name.'" name="'.$name.'" value="" class="input-text" type="text" />';
        
        $code = $this->getRequest()->getParam('code');
        if (!$code){
            $this->getResponse()->setBody($result);
            return;
        }
        
        $attribute = Mage::getModel('catalog/product')->getResource()->getAttribute($code);
        if (!$attribute){
            $this->getResponse()->setBody($result);
            return;            
        }

        if (!in_array($attribute->getFrontendInput(), array('select', 'multiselect')) ){
            $this->getResponse()->setBody($result);
            return;            
        }
        
        $options = $attribute->getFrontend()->getSelectOptions();
        
        if ('select' === $attribute->getFrontendInput()) {
            $result = '<select id="'.$name.'" name="'.$name.'" class="select">';
        } elseif ('multiselect' === $attribute->getFrontendInput()) {
            $result = '<select id="'.$name.'" name="'.$name.'[]" class="select multiselect" multiple="multiple">';
        }
        
        foreach ($options as $option){
            $result .= '<option value="'.$option['value'].'">'.$option['label'].'</option>';      
        }
        $result .= '</select>';    
        
        $this->getResponse()->setBody($result);
        
    }    
        
    public function massDeleteAction()
    {
        $ids = $this->getRequest()->getParam('pages');
        if(!is_array($ids)) {
             Mage::getSingleton('adminhtml/session')->addError(Mage::helper('amshopby')->__('Please select page(s)'));
        } 
        else {
            try {
                foreach ($ids as $id) {
                    $model = Mage::getModel('amshopby/page')->load($id);
                    $model->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d record(s) were successfully deleted', count($ids)
                    )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/');
    }
    
    public function deleteAction() 
    {
        if ($this->getRequest()->getParam('id') > 0 ) {
            try {
                $model = Mage::getModel('amshopby/page');
                 
                $model->setId($this->getRequest()->getParam('id'))
                    ->delete();
                     
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('amshopby')->__('Page has been deleted'));
                $this->_redirect('*/*/');
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            }
        }
        $this->_redirect('*/*/');
    } 
    
    protected function _title($text = null, $resetIfExists = true)
    {
        if (version_compare(Mage::getVersion(), '1.4') < 0){
            return $this;
        }
        return parent::_title($this->__($text), $resetIfExists);
    }     
    
    protected function _setActiveMenu($menuPath)
    {
        $this->getLayout()->getBlock('menu')->setActive($menuPath);
        $this
            ->_title('Catalog')
            ->_title('Improved Navigation')     
            ->_title('Pages')
        ;     
        return $this;
    }

    protected function prepareForSave(Mage_Core_Model_Abstract $model)
    {
        // convert categories from array to string
        $cats = $model->getData('cats');
        if (is_array($cats)){
            // need commas to simplify sql query
            $model->setData('cats', implode(',', $cats));
        }
        else { // need for null value
            $model->setData('cats', '');
        }

        // convert categories from array to string
        $stores = $model->getData('stores');
        if (is_array($stores)){
            // need commas to simplify sql query
            $model->setData('stores', implode(',', $stores));
        }
        else { // need for null value
            $model->setData('stores', '');
        }
    }
    
    protected function prepareForEdit(Mage_Core_Model_Abstract $model)
    {
        $cats = $model->getData('cats');
        if (!is_array($cats)){
            $model->setData('cats', explode(',', $cats));    
        }

        $stores = $model->getData('stores');
        if (!is_array($stores)){
            $model->setData('stores', explode(',', $stores));
        }
    }


    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('catalog/amshopby/pages');
    }
}