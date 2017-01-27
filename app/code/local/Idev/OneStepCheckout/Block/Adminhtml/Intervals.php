<?php
class Idev_OneStepCheckout_Block_Adminhtml_Intervals extends Mage_Adminhtml_Block_System_Config_Form_Field_Array_Abstract
{
    public function __construct()
    {

        $this->addColumn('start', array(
                'label' => Mage::helper('onestepcheckout')->__('Start'),
                'style' => 'width:45px'
        ));
        $this->addColumn('end', array(
                'label' => Mage::helper('onestepcheckout')->__('End'),
                'style' => 'width:45px'
        ));
        $this->_addAfter = false;
        $this->_addButtonLabel = Mage::helper('onestepcheckout')->__('Add Interval');

        parent::__construct();

    }

}
