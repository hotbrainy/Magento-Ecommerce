<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */
class Amasty_Shopby_Block_Adminhtml_Page_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form(array(
            'id' => 'edit_form',
            'action' => $this->getUrl('*/*/save', array('id' => $this->getRequest()->getParam('id'))),
            'method' => 'post'));
        
        $form->setUseContainer(true);
        $this->setForm($form);
        $hlp = Mage::helper('amshopby');
        
        $model = Mage::registry('amshopby_page');
        
        if (!$model->getId()){
            $fldInfo = $form->addFieldset('setup', array('legend'=> $hlp->__('Page Setup')));

            $fldInfo->addField('num', 'text', array(
              'label'     => $hlp->__('Number of Attribute Selections'),
              'class'     => 'validate-greater-than-zero',
              'required'  => true,
              'name'      => 'num',
            )); 
        }
        else {
            $fldMeta = $form->addFieldset('tags', array('legend'=> $hlp->__('Meta Tags')));
            $fldMeta->addField('num', 'hidden', array(
              'name'      => 'num',
            ));
            $fldMeta->addField('use_cat', 'select', array(
              'label'     => $hlp->__('Add to Category Metas'),
              'name'      => 'use_cat',
              'values'    => array(Mage::helper('catalog')->__('No'), Mage::helper('catalog')->__('Yes')),
            )); 
            $fldMeta->addField('meta_title', 'text', array(
              'label'     => $hlp->__('Page Title'),
              'name'      => 'meta_title',
            )); 
            $fldMeta->addField('meta_descr', 'text', array(
              'label'     => $hlp->__('Meta Description'),
              'name'      => 'meta_descr',
            )); 
            $fldMeta->addField('meta_kw', 'text', array(
              'label' => $hlp->__('Meta Keywords'),
              'name' => 'meta_kw',
            ));

            $fldMeta->addField('url', 'text', array(
              'label'     => $hlp->__('Canonical Url'),
              'name'      => 'url',
              'note'      => $hlp->__("It's not the page URL. It's HTML tag as per https://support.google.com/webmasters/answer/139394") 
            ));

            $cmsBlocks = Mage::getResourceModel('cms/block_collection')->load()->toOptionArray();
            array_unshift($cmsBlocks, array('value' => null, 'label' => $this->__('Please select a static block ...')));
            $fldInfo = $form->addFieldset('info', array('legend'=> $hlp->__('Page Text')));
            $fldInfo->addField('title', 'text', array(
                'label'     => $hlp->__('Title'),
                'name'      => 'title',
            ));
            $fldInfo->addField('description', 'textarea', array(
                'label'     => $hlp->__('Description'),
                'name'      => 'description',
            ));
            $fldInfo->addField('cms_block_id', 'select', array(
                'label'     => $hlp->__('Top CMS block'),
                'name'      => 'cms_block_id',
                'values'    => $cmsBlocks,
            ));
            $fldInfo->addField('bottom_cms_block_id', 'select', array(
                'label'     => $hlp->__('Bottom CMS block'),
                'name'      => 'bottom_cms_block_id',
                'values'    => $cmsBlocks,
            ));

            $fldCats = $form->addFieldset('categories', array('legend' => $hlp->__('Page Categories')));

            $fldCats->addField('stores', 'multiselect', array(
                'label'     => $hlp->__('Store Views'),
                'name'      => 'stores',
                'values'    => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(true),
            ));

            $fldCats->addField('cats', 'multiselect', array(
                'label'     => $hlp->__('Categories'),
                'name'      => 'cats',
                'values'    => $this->getTree(),
            ));

            $filters = $model->getAllFilters(true);

            for ($i = 0; $i < $model->getNum(); ++$i){
                $fldCond = $form->addFieldset('cond'. $i, array('legend'=> $hlp->__('Selection #' . ($i+1))));
                $fldCond->addField('attr_' . $i, 'select', array(
                    'label'     => $hlp->__('Filter'),
                    'name'      => 'attr_'.$i,
                    'values'    => $filters,
                    'class'     => 'required-entry',
                    'required'  => true,
                    'onchange'  => 'showOptions(this)',
                ));
                $attributeCode = $model->getData('attr_'.$i);
                $frontendInput = $model->getFrontendInput($attributeCode)->getFrontendInput();

                if ('select' === $frontendInput) {
                    $options = $model->getOptionsForFilter($attributeCode, 'select');
                    $fldCond->addField('option_' . $i, 'select', array(
                        'label'     => $hlp->__('Value'),
                        'class'     => 'required-entry',
                        'required'  => true,
                        'name'      => 'option_'.$i,
                        'values'    => $options,
                    ));
                } elseif ('multiselect' === $frontendInput) {
                    $options = $model->getOptionsForFilter($attributeCode, 'multiselect');
                    $fldCond->addField('option_' . $i, 'multiselect', array(
                        'label'     => $hlp->__('Value'),
                        'class'     => 'required-entry',
                        'required'  => true,
                        'name'      => 'option_'.$i,
                        'values'    => $options,
                    ));
                } else {
                    $fldCond->addField('option_' . $i, 'text', array(
                        'label'     => $hlp->__('Value'),
                        'class'     => 'required-entry',
                        'required'  => true,
                        'name'      => 'option_'.$i,
                    ));
                }
            }
        }

        //set form values
        $data = Mage::getSingleton('adminhtml/session')->getFormData();
        if ($data) {
            $form->setValues($data);
            Mage::getSingleton('adminhtml/session')->setFormData(null);
        }
        elseif ($model) {
            $form->setValues($model->getData());
        } 

        return parent::_prepareForm();
    }
  
    /**
     * Genarates tree of all categories
     *
     * @return array sorted list category_id=>title
     */
    protected function getTree()
    {
        $rootId = Mage::app()->getStore(0)->getRootCategoryId();         
        $tree = array();
        
        $collection = Mage::getModel('catalog/category')
            ->getCollection()->addNameToResult();
        
        $pos = array();
        foreach ($collection as $cat){
            $path = explode('/', $cat->getPath());
            if ((!$rootId || in_array($rootId, $path)) && $cat->getLevel()){
                $tree[$cat->getId()] = array(
                    'label' => str_repeat('--', $cat->getLevel()) . $cat->getName(), 
                    'value' => $cat->getId(),
                    'path'  => $path,
                );
            }
            $pos[$cat->getId()] = $cat->getPosition();
        }
        
        foreach ($tree as $catId => $cat){
            $order = array();
            foreach ($cat['path'] as $id){
                $order[] = isset($pos[$id]) ? $pos[$id] : 0;
            }
            $tree[$catId]['order'] = $order;
        }
        
        usort($tree, array($this, 'compare'));
        array_unshift($tree, array('value'=>'', 'label'=>''));
        
        return $tree;
    }
    
    /**
     * Compares category data. Must be public as used as a callback value
     *
     * @param array $a
     * @param array $b
     * @return int 0, 1 , or -1
     */
    public function compare($a, $b)
    {
        foreach ($a['path'] as $i => $id){
            if (!isset($b['path'][$i])){ 
                // B path is shorther then A, and values before were equal
                return 1;
            }
            if ($id != $b['path'][$i]){
                // compare category positions at the same level
                $p = isset($a['order'][$i]) ? $a['order'][$i] : 0;
                $p2 = isset($b['order'][$i]) ? $b['order'][$i] : 0;
                return ($p < $p2) ? -1 : 1;
            }
        }
        // B path is longer or equal then A, and values before were equal
        return ($a['value'] == $b['value']) ? 0 : -1;
    }
}