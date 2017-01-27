<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */

/**
 * @author Amasty
 */ 
class Amasty_Shopby_Block_Adminhtml_Range_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
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
    
    $fldInfo = $form->addFieldset('amshopby_info', array('legend'=> $hlp->__('Range')));
    
    $fldInfo->addField('price_frm', 'text', array(
      'label'     => $hlp->__('From'),
      'class'     => 'required-entry',
      'required'  => true,
      'name'      => 'price_frm',
    ));    
    
    $fldInfo->addField('price_to', 'text', array(
      'label'     => $hlp->__('To'),
      'class'     => 'required-entry',
      'required'  => true,
      'name'      => 'price_to',
      'note'      =>  $hlp->__('Please set 999999 to show `and above`')
    ));
      
    //set form values
    $data = Mage::getSingleton('adminhtml/session')->getFormData();
    $model = Mage::registry('amshopby_range');
    if ($data) {
        $form->setValues($data);
        Mage::getSingleton('adminhtml/session')->setFormData(null);
    }
    elseif ($model) {
        $form->setValues($model->getData());
    } 
    
    return parent::_prepareForm();
  }
}