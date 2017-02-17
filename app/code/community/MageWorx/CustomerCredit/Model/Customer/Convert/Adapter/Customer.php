<?php

/**
 * MageWorx
 * Loyalty Booster Extension
 *
 * @category   MageWorx
 * @package    MageWorx_CustomerCredit
 * @copyright  Copyright (c) 2016 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_CustomerCredit_Model_Customer_Convert_Adapter_Customer extends Mage_Customer_Model_Convert_Adapter_Customer
{
    public function saveRow($importData)
    {
        parent::saveRow($importData);

        if (isset($importData['credit_balance'])) {

            $customer = $this->getCustomerModel();
            $website = $this->getWebsiteByCode($importData['website']);

            $customer->setWebsiteId($website->getId())
                    ->loadByEmail($importData['email']);

            $creditModel = Mage::getModel('mageworx_customercredit/credit', $customer);
            if(empty($importData['credit_balance'])){
                return $this;
            }

            $operator = substr($importData['credit_balance'], 0, 1);
            if($operator != '-' || $operator += '+'){
                $operator = substr($importData['credit_balance'], -1);
            }
            
            if ($operator == '-' || $operator == '+') {
                $valueChange = (float)$importData['credit_balance'];
            } else {
                $valueChange = (float)$importData['credit_balance'] - $creditModel->getValue();
            }

            if ($valueChange != 0) {
                $creditModel->setValueChange($valueChange)->save();
            }

        }

        return $this;
    }
}