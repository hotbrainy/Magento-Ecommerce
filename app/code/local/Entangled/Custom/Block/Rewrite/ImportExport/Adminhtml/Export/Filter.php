<?php

class Entangled_Custom_Block_Rewrite_ImportExport_Adminhtml_Export_Filter extends Mage_ImportExport_Block_Adminhtml_Export_Filter {



    /**
     * Prepares page sizes for dashboard grid with las 5 orders
     *
     * @return void
     */
    protected function _preparePage()
    {
        $this->getCollection()->setPageSize(200);
        // Remove count of total orders $this->getCollection()->setCurPage($this->getParam($this->getVarNamePage(), $this->_defaultPage));
    }
}