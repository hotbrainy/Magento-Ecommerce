<?php

class Entangled_Custom_Model_Observer {

    public function controllerActionPredispatchCustomerAccountLogin(Varien_Event_Observer $observer){
        Mage::getSingleton("core/session")->unsSocialLoginCheckoutFlag();
    }

    public function controllerActionPredispatchOnestepcheckoutIndexIndex(Varien_Event_Observer $observer){
        Mage::getSingleton("core/session")->setSocialLoginCheckoutFlag(1);
    }

    public function controllerActionPredispatchNewsletterSubscriberNew(Varien_Event_Observer $observer){
        if($authorId = Mage::app()->getRequest()->getParam("author_ids")){
            $customerSession = Mage::getSingleton("customer/session");
            if($customerSession->isLoggedIn()){
                $customer = $customerSession->getCustomer();
                $authors = $customer->getData("author_ids");
                if(strpos($authors,"($authorId)") === false){
                    $authors .= "($authorId)";
                }
                $customer->setData("author_ids",$authors);
                $customer->save();
            }
        }
    }

    public function controllerActionPredispatchMonkeySignupSaveadditional(Varien_Event_Observer $observer){
        $lists = Mage::app()->getRequest()->getParam("list");
        $authors = isset($lists["authors"]) && is_array($lists["authors"]) ? $lists["authors"] : array();
        $customerSession = Mage::getSingleton("customer/session");
        $customer = $customerSession->getCustomer();
        $authors = $customer->getData("author_ids");
        $authorIds = explode(")(",substr($authors,1,-1));
        $hasChange = false;
        foreach($authorIds as $key => $authorId){
            if(!isset($lists["authors"][$authorId])){
                unset($authorIds[$key]);
                $hasChange = true;
            }
        }

        if($hasChange){
            $newAuthorIds = "(".implode(")(",$authorIds).")";
            $customer->setData("author_ids",$newAuthorIds);
            $customer->save();
        }
    }

    public function newsletterSubscriberSaveBefore(Varien_Event_Observer $observer){
        $entity = $observer->getDataObject();
        if($authorId = Mage::app()->getRequest()->getParam("author_ids")){
            $entity->setAuthorIds("(".$authorId.")");
        }
    }

    public function checkoutCartProductAddAfter(Varien_Event_Observer $observer){
        $quoteItem = $observer->getEvent()->getQuoteItem();
        $product = $observer->getEvent()->getProduct();
        $customerSession = Mage::getSingleton("customer/session");
        if($customerSession->isLoggedIn()){
            $error = false;
            if($product->getSku() == Entangled_Purchasediscount_Helper_Data::DISCOUNT_SKU){
                if($customerSession->getCustomer()->getGroupId() == Entangled_Purchasediscount_Helper_Data::DISCOUNT_GROUP_ID){
                    $error = true;
                    $message = "You have already purchased this membership!";
                }
            }else{
                $isRepeatedProduct = Mage::helper('entangled_custom')->isRepeatedProduct($product);

                if($isRepeatedProduct){
                    $error = true;
                    $message = "You already own this amazing book! Please go to My Library to download again or open this book.";
                }
            }
            if($error){
                $quoteItem->getQuote()->removeItem($quoteItem->getId());
                Mage::throwException($message);
            }
        }
    }
}