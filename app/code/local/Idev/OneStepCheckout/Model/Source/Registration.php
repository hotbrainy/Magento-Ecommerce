<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Paygate
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Idev_OneStepCheckout_Model_Source_Registration
{
    public function toOptionArray()
    {
        $options = array(
        array('label'=>'Require registration/login', 'value'=>'require_registration'),
        array('label'=>'Disable registration/login', 'value'=>'disable_registration'),
        array('label'=>'Allow guests and logged in users', 'value'=>'allow_guest'),
        array('label'=>'Enable registration on success page', 'value'=>'registration_success'),
        array('label'=>'Auto-generate account for new emails', 'value'=>'auto_generate_account'),
        );

        return $options;
    }
}