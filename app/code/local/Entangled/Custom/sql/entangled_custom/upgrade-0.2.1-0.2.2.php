<?php
/** @var Mage_Eav_Model_Entity_Setup $installer */
$installer = $this;
$installer->startSetup();

$installer->updateAttribute(Mage_Catalog_Model_Product::ENTITY,"sku","is_used_for_promo_rules",1);

/** @var $model Mage_SalesRule_Model_Rule */
$model = Mage::getModel('salesrule/rule');

$data = array(
    'product_ids' => null,
    'name' => "10% off for $20 coupon",
    'description' => null,
    'is_active' => 1,
    'website_ids' => array(1),
    'customer_group_ids' => Mage::getModel("customer/group")->getCollection()->getAllIds(),
    'coupon_type' => 1,
    'coupon_code' => "",
    'uses_per_coupon' => 0,
    'uses_per_customer' => 0,
    'from_date' => null,
    'to_date' => null,
    'sort_order' => null,
    'is_rss' => 1,
    'simple_action' => 'by_percent',
    'discount_amount' => 10,
    'discount_qty' => 0,
    'discount_step' => "",
    'apply_to_shipping' => 0,
    'simple_free_shipping' => 2,
    'stop_rules_processing' => 0,
    'store_labels' => array('10% Discount'),
    'conditions' => array(
        "1" => array(
            'type' => 'salesrule/rule_condition_combine',
            'aggregator' => 'all',
            'value' => 1,
            'new_child' => ""
        ),
        "1--1" => array(
            'type' => 'salesrule/rule_condition_product_found',
            'aggregator' => 'all',
            'value' => 1,
            'new_child' => ""
        ),
        "1--1--1" => array(
            'type' => 'salesrule/rule_condition_product',
            'attribute' => 'sku',
            'operator' => '==',
            'value' => "DISCOUNT-10",
        ),
    ),
    'actions' => array(
        1 => array(
            'type' => 'salesrule/rule_condition_product_combine',
            'aggregator' => 'all',
            'value' => 1,
            'new_child' => ""
        )
    )
);

$model->loadPost($data);
$model->save();

$installer->endSetup();