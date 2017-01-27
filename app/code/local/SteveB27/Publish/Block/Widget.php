<?php
class SteveB27_Publish_Block_Widget extends SteveB27_Publish_Block_Author_View 
implements Mage_Widget_Block_Interface
{
    protected function _toHtml()
    {
		foreach($this->getData() as $_para=>$value) {
			$this->assign($_para, $value);
		}
		
		return parent::_toHtml();
    }

}