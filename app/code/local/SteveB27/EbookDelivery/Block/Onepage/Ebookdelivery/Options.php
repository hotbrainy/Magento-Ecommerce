<?php

class SteveB27_EbookDelivery_Block_Onepage_Ebookdelivery_Options extends SteveB27_EbookDelivery_Block_Onepage_Ebookdelivery
{
	public $_options = array();
	
	protected function _prepareLayout()
    {
		$config = Mage::getSingleton('adminhtml/config')->getSection('ebookdelivery')->groups->options->fields;
		
		if($config->hasChildren()) {
			$options = $config->children();
			foreach($options as $key => $node) {
				$configKey = 'ebookdelivery/options/' . $key;
				$enabled = Mage::getStoreConfig($configKey);
				$this->_options[$key] = $enabled;
			}
		}
		
		foreach($this->_options as $option => $enabled) {
			if($enabled) {
				$blockModel = get_class($this) . '_' . ucfirst($option);
				$block = $this->getLayout()->createBlock(
					$blockModel,
					'checkout.onepage.ebookdelivery.options'.'.'.$option,
					array('template' => 'ebookdelivery/options/'.$option.'.phtml')
				);
				$this->append($block);
			}
		}
	}
	
	public function _toHtml()
	{
		$html = parent::_toHtml();
		
		$html .= $this->getChildHtml();
		return $html;
	}
}