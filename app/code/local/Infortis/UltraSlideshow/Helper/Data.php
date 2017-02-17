<?php

class Infortis_UltraSlideshow_Helper_Data extends Mage_Core_Helper_Abstract
{
	/**
	 * Get settings
	 *
	 * @return string
	 */
	public function getCfg($optionString)
    {
        return Mage::getStoreConfig('ultraslideshow/' . $optionString);
    }

	/**
	 * Get slideshow position
	 *
	 * @return string
	 */
	/*
	public function getPosition()
    {
    	return $this->getCfg('general/position');
    }
    */
}
