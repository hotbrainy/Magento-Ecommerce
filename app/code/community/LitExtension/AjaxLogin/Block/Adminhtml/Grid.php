<?php
/**
 * @project     AjaxLogin
 * @package LitExtension_AjaxLogin
 * @author      LitExtension
 * @email       litextension@gmail.com
 */

class LitExtension_AjaxLogin_Block_Adminhtml_Grid  extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();

        $this->setId('attributemanagergrid');
        $this->setDefaultSort('attribute_code');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
        $this->setTemplate('le_ajaxlogin/grid.phtml');
    }

    protected function _prepareCollection()
    {
        $model = 'catalog/category_attribute_collection';
        $sUrl=$this->getUrl('*/*/*', array('_current'=>true));

        $type='customer';
        $model = 'customer/attribute_collection';

        $this->type=$type;
        $collection = Mage::getResourceModel($model)
            ->setEntityTypeFilter( Mage::getModel('eav/entity')->setType($type)->getTypeId() )
            ->addFieldToFilter('is_system','0')
            ->addFieldToFilter('is_user_defined','1')
            ->addVisibleFilter();

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('attribute_code', array(
            'header'=>Mage::helper('catalog')->__('Attribute Code'),
            'sortable'=>true,
            'index'=>'attribute_code'
        ));

        $this->addColumn('frontend_label', array(
            'header'=>Mage::helper('catalog')->__('Attribute Label'),
            'sortable'=>true,
            'index'=>'frontend_label'
        ));

        $this->addColumn('is_required', array(
            'header'=>Mage::helper('catalog')->__('Required'),
            'sortable'=>true,
            'index'=>'is_required',
            'type' => 'options',
            'options' => array(
                '1' => Mage::helper('catalog')->__('Yes'),
                '0' => Mage::helper('catalog')->__('No'),
            ),
            'align' => 'center',
        ));

        return parent::_prepareColumns();
    }

    public function addNewButton(){
        return $this->getButtonHtml(
            Mage::helper('ajaxlogin')->__('New Attribute'),
            "setLocation('".$this->getUrl('*/*/new', array('type' => $this->type,'attribute_id'=>0))."')",
            "scalable add"
        );
    }
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('type' => $this->type,'attribute_id' => $row->getAttributeId()));
    }

}
