<?php

class Entangled_Custom_Block_Rewrite_CustomerCredit_Customer_Log extends MageWorx_CustomerCredit_Block_Customer_Log {

    protected function _prepareLayout() {
        call_user_func(array(get_parent_class(get_parent_class($this)), '_prepareLayout'));

        $this->getLogItems()->load();
        return $this;
    }

}