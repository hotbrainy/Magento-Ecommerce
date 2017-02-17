<?php 
class Magestore_Magenotification_Block_Adminhtml_Feedback_Renderer_Product
	extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
	/* Render Grid Column*/
	public function render(Varien_Object $row) 
	{
		return $row->getExtension().' - '.$this->__('version').' '.$row->getExtensionVersion();
	}
}