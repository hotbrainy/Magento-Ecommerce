<?php

/**
 * MageWorx
 * Loyalty Booster Extension
 *
 * @category   MageWorx
 * @package    MageWorx_CustomerCredit
 * @copyright  Copyright (c) 2016 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_CustomerCredit_Model_Customer_Convert_Parser_Customer extends Mage_Customer_Model_Convert_Parser_Customer
{
    public function getExternalAttributes()
    {
        $attributes = parent::getExternalAttributes();
        $attributes['credit_balance'] = 'credit_balance';

        return $attributes;
    }

    public function unparse()
    {
        $systemFields = array();
        foreach ($this->getFields() as $code => $node) {
            if ($node->is('system')) {
                $systemFields[] = $code;
            }
        }

        $entityIds = $this->getData();

        foreach ($entityIds as $i => $entityId) {
            $customer = $this->getCustomerModel()
                    ->setData(array())
                    ->load($entityId);
            /* @var $customer Mage_Customer_Model_Customer */

            $position = Mage::helper('mageworx_customercredit')->__('Line %d, Email: %s', ($i + 1), $customer->getEmail());
            $this->setPosition($position);

            $row = array();

            foreach ($customer->getData() as $field => $value) {
                if ($field == 'website_id') {
                    $website = $this->getWebsiteById($value);
                    if ($website === false) {
                        $website = $this->getWebsiteById(0);
                    }
                    $row['website'] = $website->getCode();
                    continue;
                }

                if (in_array($field, $systemFields) || is_object($value)) {
                    continue;
                }

                $attribute = $this->getAttribute($field);
                if (!$attribute) {
                    continue;
                }

                if ($attribute->usesSource()) {

                    $option = $attribute->getSource()->getOptionText($value);
                    if ($value && empty($option)) {
                        $message = Mage::helper('mageworx_customercredit')->__("An invalid option ID is specified for %s (%s), skipping the record.", $field, $value);
                        $this->addException($message, Mage_Dataflow_Model_Convert_Exception::ERROR);
                        continue;
                    }
                    if (is_array($option)) {
                        $value = join(self::MULTI_DELIMITER, $option);
                    } else {
                        $value = $option;
                    }
                    unset($option);
                }
                elseif (is_array($value)) {
                    continue;
                }
                $row[$field] = $value;
            }

            $defaultBillingId = $customer->getDefaultBilling();
            $defaultShippingId = $customer->getDefaultShipping();

            $customerAddress = $this->getCustomerAddressModel();

            if (!$defaultBillingId) {
                foreach ($this->getFields() as $code => $node) {
                    if ($node->is('billing')) {
                        $row['billing_' . $code] = null;
                    }
                }
            }
            else {
                $customerAddress->load($defaultBillingId);

                foreach ($this->getFields() as $code => $node) {
                    if ($node->is('billing')) {
                        $row['billing_' . $code] = $customerAddress->getDataUsingMethod($code);
                    }
                }
            }

            if (!$defaultShippingId) {
                foreach ($this->getFields() as $code => $node) {
                    if ($node->is('shipping')) {
                        $row['shipping_' . $code] = null;
                    }
                }
            }
            else {
                if ($defaultShippingId != $defaultBillingId) {
                    $customerAddress->load($defaultShippingId);
                }
                foreach ($this->getFields() as $code => $node) {
                    if ($node->is('shipping')) {
                        $row['shipping_' . $code] = $customerAddress->getDataUsingMethod($code);
                    }
                }
            }

            $store = $this->getStoreById($customer->getStoreId());
            if ($store === false) {
                $store = $this->getStoreById(0);
            }
            $row['created_in'] = $store->getCode();

            $newsletter = $this->getNewsletterModel()
                    ->setData(array())
                    ->loadByCustomer($customer);
            $row['is_subscribed'] = ($newsletter->getId()
                                     && $newsletter->getSubscriberStatus() == Mage_Newsletter_Model_Subscriber::STATUS_SUBSCRIBED)
                    ? 1 : 0;

            if ($customer->getGroupId()) {
                $groupCode = $this->_getCustomerGroupCode($customer);
                if (is_null($groupCode)) {
                    $this->addException(
                        Mage::helper('mageworx_customercredit')->__("An invalid group ID is specified, skipping the record."),
                        Mage_Dataflow_Model_Convert_Exception::ERROR
                    );
                    continue;
                } else {
                    $row['group'] = $groupCode;
                }
            }
            
            $credit = Mage::helper('mageworx_customercredit')->getCreditValue($customer->getEntityId(), $customer->getWebsiteId());
            

            if ($credit && $credit > 0) {
                $row['credit_balance'] = $credit;
            }

            $batchExport = $this->getBatchExportModel()
                    ->setId(null)
                    ->setBatchId($this->getBatchModel()->getId())
                    ->setBatchData($row)
                    ->setStatus(1)
                    ->save();
        }

        return $this;
    }

    protected function _getCustomerGroupCode($customer) {
        if (is_null($this->_customerGroups)) {
            $groups = Mage::getResourceModel('customer/group_collection')
                    ->load();

            foreach ($groups as $group) {
                $this->_customerGroups[$group->getId()] = $group->getData('customer_group_code');
            }
        }

        if (isset($this->_customerGroups[$customer->getGroupId()])) {
            return $this->_customerGroups[$customer->getGroupId()];
        } else {
            return null;
        }
    }
    
    
}