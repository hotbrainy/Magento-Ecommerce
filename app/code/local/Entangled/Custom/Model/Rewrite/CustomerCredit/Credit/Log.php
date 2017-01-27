<?php

class Entangled_Custom_Model_Rewrite_CustomerCredit_Credit_Log extends MageWorx_CustomerCredit_Model_Credit_Log {

    const ACTION_TYPE_FIRST_ORDER = 19;

    public function getActionDate(){
        return substr($this->getData("action_date"),0,10);
    }

    public function getActionTypesOptions()
    {
        return array(
            self::ACTION_TYPE_UPDATED => Mage::helper('mageworx_customercredit')->__('Earned'),
            self::ACTION_TYPE_USED => Mage::helper('mageworx_customercredit')->__('Applied'),
            self::ACTION_TYPE_REFUNDED => Mage::helper('mageworx_customercredit')->__('Refunded'),
            self::ACTION_TYPE_CREDITRULE => Mage::helper('mageworx_customercredit')->__('Modified'),
            self::ACTION_TYPE_CANCELED => Mage::helper('mageworx_customercredit')->__('Canceled'),
            self::ACTION_TYPE_CREDIT_PRODUCT => Mage::helper('mageworx_customercredit')->__('Modified by Credit Product'),
            self::ACTION_TYPE_CREDIT_ACTION => Mage::helper('mageworx_customercredit')->__('Added'),
            self::ACTION_TYPE_CODE_CREATED => Mage::helper('mageworx_customercredit')->__('Decreased'),
            self::ACTION_TYPE_IMPORT => Mage::helper('mageworx_customercredit')->__('Imported'),
            self::ACTION_TYPE_EXPIRED => Mage::helper('mageworx_customercredit')->__('Expired'),
            self::ACTION_TYPE_API => Mage::helper('mageworx_customercredit')->__('API'),
            self::ACTION_TYPE_ORDER_EDIT => Mage::helper('mageworx_customercredit')->__('Edit Order'),
            self::ACTION_TYPE_ORDER_CANCEL_AFTER_EDIT => Mage::helper('mageworx_customercredit')->__('Edit Order by Extended Orders'),
            self::ACTION_TYPE_SYNC => Mage::helper('mageworx_customercredit')->__('Sync Balance'),
            self::ACTION_TYPE_CUSTOMER_BIRTHDAY => Mage::helper('mageworx_customercredit')->__('Birthday'),
            self::ACTION_TYPE_PRODUCT_REVIEW => Mage::helper('mageworx_customercredit')->__('Product Review'),
            self::ACTION_TYPE_PRODUCT_TAG => Mage::helper('mageworx_customercredit')->__('Product Tag'),
            self::ACTION_TYPE_NEWSLETTER_SUBSCRIPTION => Mage::helper('mageworx_customercredit')->__('Newsletter Subscription'),
            self::ACTION_TYPE_FIRST_ORDER => Mage::helper('mageworx_customercredit')->__('First Order'),
        );
    }

    protected function _getComment()
    {
        $helper = Mage::helper('mageworx_customercredit');
        if($this->getData("action_type") == self::ACTION_TYPE_FIRST_ORDER){
            return Mage::helper('mageworx_customercredit')->__('First Order - #%s',$this->getOrder()->getIncrementId());
        }
        $comment = '';
        switch ($this->getActionType()) {
            case self::ACTION_TYPE_UPDATED :
                if ($this->hasRechargeCode()) {
                    if (Mage::app()->getRequest()->getActionName() == 'removeCode') {
                        $code = Mage::getModel("mageworx_customercredit/code")->load(Mage::app()->getRequest()->getParam('code_id'));
                        $comment = $helper->__('Credit Code %s was removed.', $code->getCode());
                    } else {
                        $comment = $helper->__('By Recharge Code %s', $this->getRechargeCode());
                    }
                } elseif ($user = Mage::getSingleton('admin/session')->getUser()) {
                    if ($this->getComment()) {
                        $comment = $this->getComment();
                    }
                }
                break;
            case self::ACTION_TYPE_ORDER_EDIT :
            case self::ACTION_TYPE_ORDER_CANCEL_AFTER_EDIT :
                $comment = $helper->__('Order #%s was edited. Credits was changed. %s', $this->getOrder()->getIncrementId(), $this->getComment());
                break;
            case self::ACTION_TYPE_USED :
                $this->_checkOrder();
                $comment = $helper->__('In Order #%s', $this->getOrder()->getIncrementId());
                break;
            case self::ACTION_TYPE_REFUNDED :
                $this->_checkCreditmemo();
                if ($this->getCreditModel()->getCreditRule()) {
                    $comment = $helper->__("Credit Rule(s) Order #%s; \nCredit Memo #%s", $this->getOrder()->getIncrementId(), $this->getCreditmemo()->getIncrementId());
                    $this->getCreditModel()->setCreditRule(null);
                } else {
                    $comment = $helper->__("Order #%s; \nCredit Memo #%s", $this->getOrder()->getIncrementId(), $this->getCreditmemo()->getIncrementId());
                }
                break;
            case self::ACTION_TYPE_CANCELED :
                if ($this->getCreditModel()->getCreditRule()) {
                    $comment = $helper->__("Credit Rule(s) In Order #%s", $this->getOrder()->getIncrementId());
                    $this->getCreditModel()->setCreditRule(null);
                } else {
                    $comment = $helper->__("Order #%s", $this->getOrder()->getIncrementId());
                }
                break;
            case self::ACTION_TYPE_CREDITRULE :
                $orderIncrementId = $this->getOrder()->getIncrementId();
                if ($orderIncrementId > 0) {
                    $comment = $helper->__('#%s', $orderIncrementId);
                } else {
                    $comment = $helper->__('Credit Rule');
                }
                break;
            case self::ACTION_TYPE_CREDIT_PRODUCT :
                $orderIncrementId = $this->getOrder()->getIncrementId();
                if ($orderIncrementId > 0) {
                    $comment = $helper->__('Purchase of Credit Units in Order #%s', $orderIncrementId);
                } else {
                    $comment = $helper->__('Purchase of Credit Units');
                }
                break;
            case self::ACTION_TYPE_CREDIT_ACTION :
                $comment = $helper->__('Customer completed rule "%s" action.', $this->getRuleName());
                break;
            case self::ACTION_TYPE_CODE_CREATED :
                $lastItem = Mage::getModel('mageworx_customercredit/code')->getCollection()->getLastItem();
                $comment = $helper->__('Credit Code %s was created.', $lastItem->getCode());
                break;
            case self::ACTION_TYPE_IMPORT :
                $comment = $helper->__('Credits was imported. %s', $this->getComment());
                break;
            case self::ACTION_TYPE_EXPIRED :
                $comment = $helper->__('Credits was expired. %s', $this->getComment());
                break;
            case self::ACTION_TYPE_API :
                $comment = $helper->__('Credits was changed. %s', $this->getComment());
                break;
            case self::ACTION_TYPE_SYNC :
                $comment = $helper->__('Sync Store Credits Balances. %s', $this->getComment());
                break;
            case self::ACTION_TYPE_CUSTOMER_BIRTHDAY :
                $comment = $helper->__('Reward for Birthday');
                break;
            case self::ACTION_TYPE_PRODUCT_REVIEW :
                $comment = $helper->__('Reward for Product Review "%s"', $this->getReview()->getTitle());
                break;
            case self::ACTION_TYPE_PRODUCT_TAG :
                $comment = $helper->__('Reward for Product Tag "%s"', $this->getTag()->getName());
                break;
            case self::ACTION_TYPE_NEWSLETTER_SUBSCRIPTION :
                $comment = $helper->__('Reward for Newsletter Subscription');
                break;
            case self::ACTION_TYPE_FIRST_ORDER :
                $comment = Mage::helper('mageworx_customercredit')->__('First Order - #%s',$this->getOrder()->getIncrementId());
                break;
            default :
                Mage::throwException($helper->__('Unknown log action type.'));
                break;
        }
        if (Mage::registry('customer_credit_order_place_amount_value') && !$this->getActionType() != self::ACTION_TYPE_CREDITRULE) {
            //$comment .= " (" . Mage::helper('core')->currency(Mage::registry('customer_credit_order_place_amount_value')) . ", " . $helper->__('Exchange rate is %s', $helper->getExchangeRate()) . ")";
        }
        return $comment;
    }

    public function getActionType(){
        return $this->getValueChange() < 0 && !Mage::getSingleton('admin/session')->isLoggedIn() ? self::ACTION_TYPE_USED : self::ACTION_TYPE_UPDATED;
    }

    public function getOrder(){
        return $this->getData('order') ? $this->getData('order') : Mage::getModel("sales/order")->load($this->getOrderId());
    }

    public function getOrderUrl(){
        return Mage::getModel("core/url")->getUrl("sales/order/view",array("order_id"=>$this->getOrderId()));
    }

    public function setActionType($value){
        return $this->setData("action_type",$value);
    }

    protected function init()
    {
        if (!$this->hasCreditModel() || !$this->getCreditModel()->getId()) {
            Mage::throwException(Mage::helper('mageworx_customercredit')->__('Customer credit hasn\'t assigned.'));
        }

        $this->setWebsiteId($this->getCreditModel()->getWebsiteId());
        if($this->getData("action_type") != self::ACTION_TYPE_FIRST_ORDER){
            if ($this->getCreditModel()->hasCreditmemo()) {
                $this->setActionType(self::ACTION_TYPE_REFUNDED);
            } elseif ($this->getCreditModel()->hasOrder()) {
                $this->setActionType(self::ACTION_TYPE_USED);
                if ($this->getCreditModel()->getOrder()->hasOriginalIncrementId() && $this->getCreditModel()->getOrder()->getIncrementId() != $this->getCreditModel()->getOrder()->getOriginalIncrementId()) {
                    $this->setActionType(self::ACTION_TYPE_ORDER_EDIT);
                    Mage::register('customercredit_order_edit', $this->getCreditModel()->getValueChange(), TRUE);
                    Mage::register('customercredit_order_real_edit', TRUE, TRUE);
                }
            }
        }

        if ($this->hasOrder()) {
            $this->setOrderId($this->getOrder()->getId());
        }

        $user = Mage::getSingleton('admin/session');
        if ($user->getUser()) {
            $staffName = $user->getUser()->getFirstname();
            $staffName .= " " . $user->getUser()->getLastname();
        } else {
            $staffName = Mage::helper('mageworx_customercredit')->__("Magento System");
        }

        if (!$this->hasActionType()) {
            $this->setActionType(self::ACTION_TYPE_UPDATED);
        }
        if ($this->getCreditModel()->getIsApi()) {
            $this->setActionType(self::ACTION_TYPE_API);
        }
        $this->setStaffName($staffName);
        if ($this->getActionType() == self::ACTION_TYPE_ORDER_CANCEL_AFTER_EDIT) {
            $this->setValueChange($this->getCreditModel()->getValueChange() + Mage::registry('customercredit_order_edit'));
        }

        $this->setCreditId($this->getCreditModel()->getId());
        $this->setValueChange($this->getCreditModel()->getValueChange());
        $this->setValue($this->getCreditModel()->getValue());
        $this->setComment($this->_getComment());
    }

}