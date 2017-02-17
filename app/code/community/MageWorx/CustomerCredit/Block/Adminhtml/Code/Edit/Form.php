<?php

/**
 * MageWorx
 * Loyalty Booster Extension
 *
 * @category   MageWorx
 * @package    MageWorx_CustomerCredit
 * @copyright  Copyright (c) 2016 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_CustomerCredit_Block_Adminhtml_Code_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
	public function __construct()
	{
	    parent::__construct();
		$this->setTitle(Mage::helper('mageworx_customercredit')->__('Recharge Code'));
	}
	
	protected function _prepareForm()
    {
        $form = new Varien_Data_Form(array(
            'id'     => 'edit_form',
            'action' => $this->getUrl('*/*/save', array('_current' => true)),
            'method' => 'post'
            )
        );
        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }
}