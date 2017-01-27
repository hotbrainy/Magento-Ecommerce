<?php

/**
 * Iksanika llc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.iksanika.com/products/IKS-LICENSE.txt
 *
 * @category   Iksanika
 * @package    Iksanika_Productrelater
 * @copyright  Copyright (c) 2013 Iksanika llc. (http://www.iksanika.com)
 * @license    http://www.iksanika.com/products/IKS-LICENSE.txt
 */

class Iksanika_Productrelater_Block_Widget_Grid_Massaction extends Mage_Adminhtml_Block_Widget_Grid_Massaction
{
    public function getJsObjectName()
    {
        return $this->getHtmlId() . 'JsObjectIKSProductrelater';
    }

    public function getJavaScript() {
        return " var {$this->getJsObjectName()} = new varienGridMassactionIksanikaProductrelater('{$this->getHtmlId()}', "
                . "{$this->getGridJsObjectName()}, '{$this->getSelectedJson()}'"
                . ", '{$this->getFormFieldNameInternal()}', '{$this->getFormFieldName()}');"
                . "{$this->getJsObjectName()}.setItems({$this->getItemsJson()}); "
                . "{$this->getJsObjectName()}.setGridIds('{$this->getGridIdsJson()}');"
                . ($this->getUseAjax() ? "{$this->getJsObjectName()}.setUseAjax(true);" : '')
                . ($this->getUseSelectAll() ? "{$this->getJsObjectName()}.setUseSelectAll(true);" : '')
                . "{$this->getJsObjectName()}.errorText = '{$this->getErrorText()}';";
    }

}
