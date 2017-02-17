<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */  
class Amasty_Shopby_Amshopby_RangeController extends Mage_Adminhtml_Controller_Action
{
    // show grid
    public function indexAction()
    {
        $this->loadLayout(); 
        $this->_setActiveMenu('catalog/amshopby');
        $this->_addBreadcrumb($this->__('Ranges'), $this->__('Ranges')); 
        $this->_addContent($this->getLayout()->createBlock('amshopby/adminhtml_range'));
        $this->_title($this->__('Price Ranges'));
        $this->renderLayout();
    }

    public function newAction() 
    {
        $this->editAction(); 
    }
    
    public function editAction() 
    {
        $id     = (int) $this->getRequest()->getParam('id');
        $model  = Mage::getModel('amshopby/range')->load($id);

        if ($id && !$model->getId()) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('amshopby')->__('Range does not exist'));
            $this->_redirect('*/*/');
            return;
        }
        
        $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
        if (!empty($data)) {
            $model->setData($data);
        }
        
        Mage::register('amshopby_range', $model);

        $this->loadLayout();
        
        $this->_setActiveMenu('catalog/amshopby');
        $this->_addContent($this->getLayout()->createBlock('amshopby/adminhtml_range_edit'));
             //->_addLeft($this->getLayout()->createBlock('amshopby/adminhtml_filter_edit_tabs'));

        $this->_title($this->__('Edit Range'));

        $this->renderLayout();
    }

    public function saveAction() 
    {
        $id     = $this->getRequest()->getParam('id');
        $model  = Mage::getModel('amshopby/range');
        $data   = $this->getRequest()->getPost();
        if ($data) {
            $model->setData($data)->setId($id);
            
            try {
                $model->save();
                Mage::getSingleton('adminhtml/session')->setFormData(false);
                
                $msg = Mage::helper('amshopby')->__('Price range has been successfully saved');
                Mage::getSingleton('adminhtml/session')->addSuccess($msg);

                $this->_redirect('*/*/');
               
                
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array('id' => $id));
            }    
                        
            return;
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('amshopby')->__('Unable to find a range to save'));
        $this->_redirect('*/*/');
    } 
        
    public function massDeleteAction()
    {
        $ids = $this->getRequest()->getParam('ranges');
        if(!is_array($ids)) {
             Mage::getSingleton('adminhtml/session')->addError(Mage::helper('amshopby')->__('Please select range(s)'));
        } 
        else {
            try {
                foreach ($ids as $id) {
                    $model = Mage::getModel('amshopby/range')->load($id);
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
        if( $this->getRequest()->getParam('id') > 0 ) {
            try {
                $model = Mage::getModel('amshopby/range');
                 
                $model->setId($this->getRequest()->getParam('id'))
                    ->delete();
                     
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('amshopby')->__('Range has been deleted'));
                $this->_redirect('*/*/');
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            }
        }
        $this->_redirect('*/*/');
    }


    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('catalog/amshopby/ranges');
    }
}