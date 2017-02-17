<?php

class Magestore_Magenotification_Block_Adminhtml_Feedback_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('feedback_form', array('legend'=>Mage::helper('magenotification')->__('Feedback detail')));
     
	$data = array();
    if ( Mage::getSingleton('adminhtml/session')->getFeedbackData() )
    {
		$data = Mage::getSingleton('adminhtml/session')->getFeedbackData();
        Mage::getSingleton('adminhtml/session')->setFeedbackData(null);
    } elseif ( Mage::registry('feedback_data') ) {
		$data = Mage::registry('feedback_data')->getData();
    }	
	$dataObject = new Varien_Object($data);
	
	if($dataObject->getFeedbackId()){
		$fieldset->addField('is_sent', 'note', array(
		  'label'     => '',
		  'name'      => 'is_sent',
		  'text'    => $dataObject->getIsSent() == '1' ? '<span style="width:250px;" class="grid-severity-notice"><span>'.$this->__('Sent to Magestore.com').'</span></span>' : '<span style="width:250px;" class="grid-severity-critical"><span>'.$this->__('Not sent to Magestore.com').'</span></span>',
		));			 
	}
	
	if($dataObject->getFeedbackId()){
	  $fieldset->addField('code', 'note', array(
          'label'     => Mage::helper('magenotification')->__('Feedback Code'),
          'name'      => 'code',
		  'text'      => $dataObject->getCode(),
      )); 
	  
      $fieldset->addField('extension', 'note', array(
          'label'     => Mage::helper('magenotification')->__('Extension'),
          'name'      => 'extension',
		  'text'      => $dataObject->getExtension().' - version '. $dataObject->getExtensionVersion(),
      )); 
	} else {
      $fieldset->addField('extension', 'select', array(
          'label'     => Mage::helper('magenotification')->__('Extension'),
          'name'      => 'extension',
		  'values'      => Mage::helper('magenotification')->getExtensionOption(),
          'class'     => 'required-entry',
          'required'  => true,				  
      )); 
	}			  
	  
    if($dataObject->getFeedbackId()){	  
		$fieldset->addField('created_time', 'note', array(
          'label'     => Mage::helper('magenotification')->__('Posted'),
          'name'      => 'created_time',
		  'text'      => $this->formatDate($dataObject->getCreatedTime(),'medium',true),
		));		 	 

		if($dataObject->getCouponCode()){
			$fieldset->addField('coupon_code', 'note', array(
			  'label'     => Mage::helper('magenotification')->__('Coupon'),
			  'name'      => 'coupon_code',
			  'text'     => '<b>'.$dataObject->getCouponCode().'</b> ('.
					              Mage::helper('magenotification')->__('for discount').' '.$dataObject->getCouponValue().' '. 
								  Mage::helper('magenotification')->__('to').' '.	
								  Mage::helper('core')->formatDate($dataObject->getExpiredCoupon(),'medium',false).')',
			));		
		}
		
		$fieldset->addField('status', 'select', array(
          'label'     => Mage::helper('magenotification')->__('Status'),
          'name'      => 'status',
          'values'    => Mage::helper('magenotification')->getFeedbackStatusOption(),
		  'disabled'   => 'disabled',
		  'style'     => 'width:600px;',
		));
 
		$fieldset->addField('content', 'note', array(
          'name'      => 'content',
          'label'     => Mage::helper('magenotification')->__('Feedback'),
		  'text'      => $dataObject->getData('content'),			  
		));
		
		$fieldset->addField('attached_file', 'note', array(
          'name'      => 'attached_file',
          'label'     => Mage::helper('magenotification')->__('Attached Files'),
          'text'      => $this->getLayout()->createBlock('magenotification/adminhtml_feedback_renderer_file')
											->setFeedback($dataObject)->getAttachedFilesHtml(),			  
		));	
		
	} else {
 
		$fieldset->addField('content', 'editor', array(
          'name'      => 'content',
          'label'     => Mage::helper('magenotification')->__('Feedback'),
		  'style'     => 'width:600px;height:300px',
          'class'     => 'required-entry',
          'required'  => true,				  
		));	
		
		$fieldset->addField('attached_file', 'note', array(
          'name'      => 'attached_file',
          'label'     => Mage::helper('magenotification')->__('Attached Files'),
          'text'      => $this->getLayout()->createBlock('magenotification/adminhtml_feedback_renderer_file')->toHtml(),			  
		));		
	}
	  
	  $form->setValues($data);
      return parent::_prepareForm();
  }
}