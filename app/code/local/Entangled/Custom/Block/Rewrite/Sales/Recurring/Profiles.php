<?php

class Entangled_Custom_Block_Rewrite_Sales_Recurring_Profiles extends Mage_Sales_Block_Recurring_Profiles {

    /**
     * Prepare profiles collection and render it as grid information
     */
    public function prepareProfilesGrid()
    {
        /* @var $profile Mage_Sales_Model_Recurring_Profile */
        $profile = Mage::getModel('sales/recurring_profile');

        $this->setGridColumns(array(
            new Varien_Object(array(
                'index' => 'name',
                'title' => "Membership",
                'is_nobr' => true,
                'width' => 1,
            )),
            new Varien_Object(array(
                'index' => 'created_at',
                'title' => $this->__("Started At"),
                'is_nobr' => true,
                'width' => 1,
                'is_amount' => true,
            )),
            new Varien_Object(array(
                'index' => 'finish_at',
                'title' => $this->__("Finish At"),
                'is_nobr' => true,
                'width' => 1,
            )),
        ));

        $profiles = array();
        $customer = Mage::getSingleton("customer/session")->getCustomer();
        if($customer->getGroupId() == Entangled_Purchasediscount_Helper_Data::DISCOUNT_GROUP_ID){
            $membership = Mage::getModel('purchasediscount/purchasedate')->load($customer->getId(),"customer_id");

            $currentTimestamp = Mage::getModel('core/date')->timestamp($membership->getData("timestamp")); //Magento's timestamp function makes a usage of timezone and converts it to timestamp
            $date = date('Y-m-d', $currentTimestamp);
            $finishAt = date('Y-m-d', strtotime("+1 year",$currentTimestamp));
            $profiles[] = new Varien_Object(array(
                'name' => "Yearly Discount Membership 10% off All Orders",
                'created_at'  => $date,
                'finish_at'  => $finishAt,
            ));
        }

        if ($profiles) {
            $this->setGridElements($profiles);
        }
        $orders = array();
    }

}