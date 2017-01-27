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
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Shell
 * @copyright  Copyright (c) 2006-2016 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

require_once 'abstract.php';

class Mage_Shell_SendAmazonLink extends Mage_Shell_Abstract
{

    /**
     * Validate arguments
     *
     */
    protected function _validate()
    {
        if (isset($_SERVER['REQUEST_METHOD']) && !$_GET["code"]=="UAfjaxFuBrrQfj6Q") {
            die('This script cannot be run from Browser. This is the shell script.');
        }
    }

    /**
     * Run script
     *
     */
    public function run()
    {
        if($_GET["file"]){
            $this->_args["file"] = $_GET["file"];
        }
        if($_GET["mail"]){
            $this->_args["mail"] = $_GET["mail"];
        }
        if (isset($this->_args['file']) && isset($this->_args['mail'])) {
            $amazonemail = Mage::helper("ebookdelivery/amazonemail");
            $file = Mage::getBaseDir("media").DS."downloadable".DS."files".DS."links".DS."*".DS."*".DS.$this->_args['file'];
            foreach(glob($file) as $filename){
                echo "\nFilename: ".$filename."\n";
                try{
                    $amazonemail->sendAmazonemail($this->_args['mail'],$filename,$this->_args['file']);
                    echo "Sent\n";

                }catch(Exception $e){
                    echo "Failed";
                }
            }
        }
    }

    /**
     * Retrieve Usage Help Message
     *
     */
    public function usageHelp()
    {
        return "";
    }
}

$shell = new Mage_Shell_SendAmazonLink();
$shell->run();
