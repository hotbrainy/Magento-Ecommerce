<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Base
 */ 
class Amasty_Base_Block_Adminhtml_Debug_Rewrite extends Amasty_Base_Block_Adminhtml_Debug_Base
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('amasty/ambase/debug/rewrite.phtml');        
    }
    
    function getRewritesList(){
        return Mage::helper("ambase")->getRewritesList();
    }
}
?>