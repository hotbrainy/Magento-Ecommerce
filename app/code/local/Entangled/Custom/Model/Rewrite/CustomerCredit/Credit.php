<?php
/**
 * Created by PhpStorm.
 * User: riterrani
 * Date: 11/24/16
 * Time: 7:50 PM
 */ 
class Entangled_Custom_Model_Rewrite_CustomerCredit_Credit extends MageWorx_CustomerCredit_Model_Credit {

    const FIRST_ORDER_AMOUNT = 0;

    public function processFirstOrder($order){
        try{
            $creditAmount = self::FIRST_ORDER_AMOUNT;
            // $this->setValueChange(0);
            $this->creditLog
                ->setOrder($order)
                ->setActionType(Entangled_Custom_Model_Rewrite_CustomerCredit_Credit_Log::ACTION_TYPE_FIRST_ORDER);
            $this->save();
            $this->sendNewFanEmail($order);
        }catch (Exception $e){
            Mage::getSingleton('customer/session')->addException($e, $e->getMessage());
        }
        return $this;
    }

    /**
     * @param Mage_Sales_Model_Order $order
     * @return $this
     */
    public function processCompleteOrderStatus($order) {
        $creditProductSku = Mage::helper('mageworx_customercredit')->getCreditProductSku();

        $creditQty = 0;
        if ($creditProductSku) {
            $allItems = $order->getAllItems();
            foreach ($allItems as $item) {
                if ($item->getSku() == $creditProductSku) {
                    $creditQty += intval($item->getQtyInvoiced());
                }
            }

            if ($creditQty > 0) {
                $creditLog = $this->creditLog->loadByOrderAndAction($order->getId(), 5);
                if (!$creditLog || !$creditLog->getId()) {
                    $this->setValueChange($creditQty);
                    $this->creditLog
                        ->setOrder($order)
                        ->setActionType(MageWorx_CustomerCredit_Model_Credit_Log::ACTION_TYPE_CREDIT_PRODUCT);
                    $this->save();
                }
            }
        }
        return $this;
    }

    public function sendNewFanEmail($order){
        $recipientEmail = $order->getCustomerEmail();
        $recipientName = $order->getCustomerName();
        $sender = array(
            'name' => Mage::getStoreConfig('trans_email/ident_general/name'),
            'email' => Mage::getStoreConfig('trans_email/ident_general/email')
        );
        $vars = array(
            'customer_name' => $recipientName,
        );
        $storeId = Mage::app()->getStore()->getId();
        Mage::getModel('core/email_template')
            ->sendTransactional('entangled_rewardpoints_new_fan', $sender, $recipientEmail, $recipientName, $vars, $storeId);
    }
}