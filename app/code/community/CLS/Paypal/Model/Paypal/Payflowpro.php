<?php
/**
 * Classy Llama
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email to us at
 * support+paypal@classyllama.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module
 * to newer versions in the future. If you require customizations of this
 * module for your needs, please write us at sales@classyllama.com.
 *
 * To report bugs or issues with this module, please email support+paypal@classyllama.com.
 * 
 * @category   CLS
 * @package    Paypal
 * @copyright  Copyright (c) 2014 Classy Llama Studios, LLC (http://www.classyllama.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class CLS_Paypal_Model_Paypal_Payflowpro extends Mage_Paypal_Model_Payflowpro
{
    protected $_formBlockType = 'cls_paypal/payment_form_cc';

    /**
     *
     */
    public function __construct()
    {
        parent::__construct();
        
        $this->_debugReplacePrivateDataKeys[] = 'buttonsource';
    }
    
     /**
      * Return request object with basic information for gateway request
      *
      * @param Mage_Sales_Model_Order_Payment $payment
      * @return Varien_Object
      */
    protected function _buildBasicRequest(Varien_Object $payment)
    {
        $request = new Varien_Object();
        $request
            ->setUser($this->getConfigData('user'))
            ->setVendor($this->getConfigData('vendor'))
            ->setPartner($this->getConfigData('partner'))
            ->setPwd($this->getConfigData('pwd'))
            ->setVerbosity($this->getConfigData('verbosity'))
            ->setTender(self::TENDER_CC)
            ->setRequestId($this->_generateRequestId())
            ->setButtonsource(Mage::helper('cls_paypal')->getPaypalInfoCode());
        
        if($payment->getOrder()) {            
            $request->setInvnum($payment->getOrder()->getIncrementId());
        }
       
        return $request;
    }

}
