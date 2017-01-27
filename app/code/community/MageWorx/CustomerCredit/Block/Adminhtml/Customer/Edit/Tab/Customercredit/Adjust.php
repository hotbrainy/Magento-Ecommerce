<?php

/**
 * MageWorx
 * Loyalty Booster Extension
 *
 * @category   MageWorx
 * @package    MageWorx_CustomerCredit
 * @copyright  Copyright (c) 2016 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_CustomerCredit_Block_Adminhtml_Customer_Edit_Tab_CustomerCredit_Adjust extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm() {
        $customer = Mage::registry('current_customer');
        $data = $customer->getData();
        
        
        $helper = Mage::helper('mageworx_customercredit');
        $creditValue    = (float)$helper->getCreditValue($customer);
        $expirationTime = $helper->getExpirationTime($customer);
        $enableExpiration = $helper->getEnableExpiration($customer);
        $data['credit_value']    = $creditValue;        
        $data['expiration_time'] = $expirationTime;
        if (($enableExpiration > 1) || $expirationTime == NULL) {
            $data['enable_expiration'] = '2';
        } else {
            $data['enable_expiration'] = $enableExpiration;
        }
        $isExpirationEnabled = $helper->isExpirationEnabled($enableExpiration);
        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('customercredit_');
        $form->setFieldNameSuffix('customercredit');
              
        $fieldset = $form->addFieldset('adjust_fieldset', array('legend'=>$helper->__('Adjust Credit')));        
        $expired = '';
        
//echo "<pre>"; print_r($model->getData()); exit;

        if ($isExpirationEnabled && $creditValue) {
            $daysLeft = Mage::helper('mageworx_customercredit')->getCreditExpired($customer);
            if($daysLeft) {
                $expired = " (".$this->__('Expire in %s day(s)',"<b>".$daysLeft."</b>").")";
            }
        }
        $fieldset->addField('credit_value', 'hidden', array(
            'name'     => 'credit_value',            
            'after_element_html' => '</td></tr><tr><td class="label">'.$helper->__('Current Balance').'</td>
                                     <td id="customercredit_credit_website_value" class="value">
                                     '.$creditValue.$expired,
        ));
        $fieldset->addField('enable_expiration', 'select', array(
            'label'     => $helper->__('Enable Expiration Date'),
            'title'     => $helper->__('Enable Expiration Date'),
            'name'      => 'enable_expiration',
            'options'    => array(
                '2' => $helper->__('Use Config'),
                '1' => $helper->__('Yes'),
                '0' => $helper->__('No'),
            ),
        ));


        if ($isExpirationEnabled && $creditValue) {
            $outputFormat = Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);
            $fieldset->addField('expiration_time', 'date', array(
                'name'     => 'expiration_time',
                'time'     =>    false,
                'format'   =>    $outputFormat,
                'image'  => $this->getSkinUrl('images/grid-cal.gif'),
                'input_format' => Varien_Date::DATE_INTERNAL_FORMAT,
                'label'    => $helper->__('Expiration Date'),
                'title'    => $helper->__('Expiration Date'),
                'note'     => $helper->__('Change date to this customer'),
            ));
        }
        
        $fieldset->addField('value_change', 'text', array(
            'name'     => 'value_change',
            'label'    => $helper->__('Credit Value'),
            'title'    => $helper->__('Credit Value'),
            'note'     => $helper->__('A negative value subtracts from the credit balance'),
        ));

        
        if ($helper->isScopePerWebsite()) {
            $script = '';
            foreach (Mage::app()->getWebsites() as $website) {
                $value  =(float)$helper->getCreditValue($customer);
                if($value) {
                    $value .= $expired;
                }
                $script .= 'vs['.$website->getId().']=\''.$value.'\';';
            }
            $fieldset->addField('website_id', 'select', array(
                'name'     => 'website_id',
                'label'    => Mage::helper('mageworx_customercredit')->__('Website'),
                'title'    => Mage::helper('mageworx_customercredit')->__('Website'),
                'values'   => Mage::getModel('adminhtml/system_store')->getWebsiteValuesForForm(),
                'onchange' =>  "var vs = []; ".$script." if (vs[this.value]) $('customercredit_credit_website_value').innerHTML = vs[this.value];"
            ));
        }
        
        $fieldset->addField('comment', 'textarea', array(
            'name'     => 'comment',
            'label'    => $helper->__('Comment'),
            'title'    => $helper->__('Comment'),
            'class'    => 'mageworx_customercredit_comment',
        ));
        
        $form->setValues($data);
        $this->setForm($form);
        
        return parent::_prepareForm();
    }
    
    public function getWebsiteHtmlId() {
        return 'customercredit_website_id';
    }

}