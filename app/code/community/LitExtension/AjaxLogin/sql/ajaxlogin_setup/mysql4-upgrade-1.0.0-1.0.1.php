<?php
/**
 * @project     AjaxLogin
 * @package LitExtension_AjaxLogin
 * @author      LitExtension
 * @email       litextension@gmail.com
 */
$installer = $this;
/* @var $installer Mage_Customer_Model_Entity_Setup */

$installer->startSetup();

$installer->addAttribute('customer', 'lit_ajaxlogin_aid', array(
    'type' => 'text',
    'visible' => false,
    'required' => false,
    'user_defined' => false
));

$installer->addAttribute('customer', 'lit_ajaxlogin_atoken', array(
    'type' => 'text',
    'visible' => false,
    'required' => false,
    'user_defined' => false
));


$installer->endSetup();
