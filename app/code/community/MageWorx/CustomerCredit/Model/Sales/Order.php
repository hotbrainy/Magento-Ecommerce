<?php

/**
 * MageWorx
 * Loyalty Booster Extension
 *
 * @category   MageWorx
 * @package    MageWorx_CustomerCredit
 * @copyright  Copyright (c) 2016 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_CustomerCredit_Model_Sales_Order extends Mage_Sales_Model_Order {
    /**
     * Rewrite function
     * Check order state before saving
     * @return MageWorx_CustomerCredit_Model_Sales_Order
     */
    protected function _checkState() {
        if (!$this->getId()) {
            return $this;
        }

        if (!$this->getCutomerCreditAmount()) {
            return parent::_checkState();
        }

        $userNotification = $this->hasCustomerNoteNotify() ? $this->getCustomerNoteNotify() : null;

        if (!$this->isCanceled()
            && !$this->canUnhold()
            && !$this->canInvoice()
            && !$this->canShip()) {
            if (0 == $this->getBaseGrandTotal() || $this->canCreditmemo()) {
                if ($this->getState() !== self::STATE_COMPLETE) {
                    $this->_setState(self::STATE_COMPLETE, true, '', $userNotification);
                }
            }
            /**
             * Order can be closed just in case when we have refunded amount.
             * In case of "0" grand total order checking ForcedCanCreditmemo flag
             */
            elseif (floatval($this->getTotalRefunded()) || (!$this->getTotalRefunded()
                    && $this->hasForcedCanCreditmemo())
            ) {
                if ($this->getState() !== self::STATE_CLOSED && $this->hasCreditmemos()) {
                    $this->_setState(self::STATE_CLOSED, true, '', $userNotification);
                    $this->unsForcedCanCreditmemo();
                }
            }
            if($this->getState() !== self::STATE_CLOSED && abs($this->getTotalOfflineRefunded()-($this->getGrandTotal()+$this->getCutomerCreditAmount())) < 0.0001) {
                $this->_setState(self::STATE_CLOSED, true, '', $userNotification);
                $this->unsForcedCanCreditmemo();
            }
        }
        if(Mage::registry('can_closed_order')) {
            if ($this->getState() !== self::STATE_CLOSED && $this->hasCreditmemos()) {
                $this->_setState(self::STATE_CLOSED, true, '', $userNotification);
                $this->unsForcedCanCreditmemo();
            }
        }

        if ($this->getState() == self::STATE_NEW && $this->getIsInProcess()) {
            $this->setState(self::STATE_PROCESSING, true, '', $userNotification);
        }
        return $this;
    }


    /*
     * Rewrite function
     * Add a comment to order
     * Different or default status may be specified
     *
     * @param string $comment
     * @param string $status
     * @return Mage_Sales_Model_Order_Status_History
     */
    public function addStatusHistoryComment($comment, $status = false)
    {
        if(Mage::registry('change_order_status_once')) return $this;
        Mage::register("change_order_status_once",true,true);
        return parent::addStatusHistoryComment($comment, $status);
    }
}