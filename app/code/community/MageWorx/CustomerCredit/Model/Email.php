<?php

/**
 * MageWorx
 * Loyalty Booster Extension
 *
 * @category   MageWorx
 * @package    MageWorx_CustomerCredit
 * @copyright  Copyright (c) 2016 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_CustomerCredit_Model_Email
{
    const ACTION_BALANCE_CHANGED = 'balance_changed';
    const ACTION_EXPIRATION_NOTICE = 'expiration_notice';
    const ACTION_CREDIT_RULES = 'credit_rules';
    const ACTION_CUSTOMER_BIRTHDAY = 'customer_birthday';
    const ACTION_PRODUCT_REVIEW = 'product_review';
    const ACTION_NEWSLETTER_SUBSCRIPTION = 'newsletter_subscription';
    const ACTION_PRODUCT_TAG = 'product_tag';

    /**
     * @param $action
     * @param $customer
     * @return $this
     */
    public function send($action, $customer)
    {
        $helper = Mage::helper('mageworx_customercredit');
        if (!$helper->isSendEmailTemplates() && !$helper->isTemplateActionEnabled($action) && !$action) {
            return;
        }
        $storeId = $customer->getStoreId();
        $templateId = 'mageworx_customercredit_email_' . $action;
        if ($helper->getTemplate($action, $storeId)) {
            $templateId = $helper->getTemplate($action, $storeId);
        }

        $creditData = $customer->getCustomerCreditData();

        if (!$creditData) {
            return;
        }

        $customerName = $customer->getFirstname() . ' ' . $customer->getLastname();
        $creditChange = floatval($creditData['value_change']);
        $creditBalance = floatval($creditData['credit_value']);
        $comment = trim($creditData['comment']);

        $templateParams = array();
        switch ($action) {
            case self::ACTION_BALANCE_CHANGED:
                if ($creditChange == 0) return;
                $templateParams = array(
                    'creditChange' => $creditChange,
                    'balance' => $creditBalance,
                    'customerName' => $customerName,
                    'comment' => $comment
                );
                break;
            case self::ACTION_EXPIRATION_NOTICE:
                $daysLeft = $creditData['days_left'];
                if (!$daysLeft) return;
                $templateParams = array(
                    'daysLeft' => $daysLeft,
                    'customerName' => $customerName,
                );
                break;
            case self::ACTION_CREDIT_RULES:
                if ($creditData['rule_id']) {
                    $ruleModel = Mage::getModel('mageworx_customercredit/rules')->load($creditData['rule_id']);
                    $ruleEmailTemplate = $ruleModel->getEmailTemplate();
                    if ($ruleEmailTemplate != 0) {
                        $templateId = $ruleEmailTemplate;
                    }
                    $orderIncId = $creditData['order_inc_id'];
                    $ruleName = $ruleModel->getName();
                }
                $templateParams = array(
                    'creditChange' => $creditChange,
                    'balance' => $creditBalance,
                    'customerName' => $customerName,
                    'comment' => $comment,
                    'ruleName' => $ruleName,
                    'orderIncId' => $orderIncId
                );
                break;
            case self::ACTION_CUSTOMER_BIRTHDAY:
                $birthday = $creditData['birthday'];
                $templateParams = array(
                    'birthday' => date('Y-m-d', $birthday),
                    'creditChange' => $creditChange,
                    'balance' => $creditBalance,
                    'comment' => $comment,
                    'customerName' => $customerName,
                );
                break;
            case self::ACTION_PRODUCT_REVIEW:
                $reviewTitle = $creditData['review_title'];
                $reviewDetail = $creditData['review_detail'];
                $productName = $creditData['product_name'];
                $productUrl = $creditData['product_url'];
                $templateParams = array(
                    'creditChange' => $creditChange,
                    'balance' => $creditBalance,
                    'comment' => $comment,
                    'customerName' => $customerName,
                    'reviewTitle' => $reviewTitle,
                    'reviewDetail' => $reviewDetail,
                    'productName' => $productName,
                    'productUrl' => $productUrl
                );
                break;
            case self::ACTION_NEWSLETTER_SUBSCRIPTION:
                $templateParams = array(
                    'creditChange' => $creditChange,
                    'balance' => $creditBalance,
                    'customerName' => $customerName,
                    'comment' => $comment
                );
                break;
            case self::ACTION_PRODUCT_TAG:
                $tagName = $creditData['tag_name'];
                $productName = $creditData['product_name'];
                $productUrl = $creditData['product_url'];
                $templateParams = array(
                    'creditChange' => $creditChange,
                    'balance' => $creditBalance,
                    'comment' => $comment,
                    'customerName' => $customerName,
                    'tagName' => $tagName,
                    'productName' => $productName,
                    'productUrl' => $productUrl
                );
                break;
        }

        $translate = Mage::getSingleton('core/translate');
        $translate->setTranslateInline(false);

        $emailInfo = Mage::getModel('core/email_info');
        $emailInfo->addTo($customer->getEmail(), $customerName);
        $bccEmails = $helper->getBccEmails();
        if ($bccEmails) {
            foreach (explode(',', $bccEmails) as $bcc) {
                $emailInfo->addBcc($bcc, 'Magento Recipient');
            }
        }
        $mailer = Mage::getModel('core/email_template_mailer');
        $mailer->addEmailInfo($emailInfo);
        $mailer->setSender(Mage::getStoreConfig('sales_email/order_comment/identity', $storeId));
        $mailer->setStoreId($storeId);
        $mailer->setTemplateId($templateId);
        $mailer->setTemplateParams($templateParams);
        $translate->setTranslateInline(true);
        $mailer->send();

        return $this;
    }
}