<?php
/** @var Mage_Eav_Model_Entity_Setup $installer */
$installer = $this;
$installer->startSetup();

$footerCms = Mage::getModel('cms/block')->load('footer-ads');

$footerCms->setContent(
    '<div class="col-xs-12 col-sm-3 left-a">
        <div class="col-xs-12">
            <img src="{{skin url='."'images/AD_250_80.png'".'}}" alt=""/>
        </div>
        <div class="col-xs-12">
            <img src="{{skin url='."'images/AD_250_80.png'".'}}" alt=""/>
        </div>
    </div>
    <div class="hidden-xs col-sm-9 right-a">
        <img src="{{skin url='."'images/AD_790_180.png'".'}}" alt=""/>
    </div>'
    )->save();

$installer->endSetup();