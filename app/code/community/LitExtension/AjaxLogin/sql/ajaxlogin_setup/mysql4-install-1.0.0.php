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

$installer->addAttribute('customer', 'lit_ajaxlogin_gid', array(
    'type' => 'text',
    'visible' => false,
    'required' => false,
    'user_defined' => false
));

$installer->addAttribute('customer', 'lit_ajaxlogin_gtoken', array(
    'type' => 'text',
    'visible' => false,
    'required' => false,
    'user_defined' => false
));

$installer->addAttribute('customer', 'lit_ajaxlogin_fid', array(
    'type' => 'text',
    'visible' => false,
    'required' => false,
    'user_defined' => false
));


$installer->addAttribute('customer', 'lit_ajaxlogin_ftoken', array(
    'type' => 'text',
    'visible' => false,
    'required' => false,
    'user_defined' => false
));

$installer->addAttribute('customer', 'lit_ajaxlogin_tid', array(
    'type' => 'text',
    'visible' => false,
    'required' => false,
    'user_defined' => false
));

$installer->addAttribute('customer', 'lit_ajaxlogin_ttoken', array(
    'type' => 'text',
    'visible' => false,
    'required' => false,
    'user_defined' => false
));

$installer->addAttribute('customer', 'lit_ajaxlogin_lid', array(
    'type' => 'text',
    'visible' => false,
    'required' => false,
    'user_defined' => false
));

$installer->addAttribute('customer', 'lit_ajaxlogin_ltoken', array(
    'type' => 'text',
    'visible' => false,
    'required' => false,
    'user_defined' => false
));

$installer->addAttribute('customer', 'lit_ajaxlogin_yid', array(
    'type' => 'text',
    'visible' => false,
    'required' => false,
    'user_defined' => false
));

$installer->addAttribute('customer', 'lit_ajaxlogin_ytoken', array(
    'type' => 'text',
    'visible' => false,
    'required' => false,
    'user_defined' => false
));

$installer->endSetup();
