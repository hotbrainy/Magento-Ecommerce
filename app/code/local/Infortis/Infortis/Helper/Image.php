<?php

class Infortis_Infortis_Helper_Image extends Mage_Core_Helper_Abstract
{
	/**
	 * Get image URL of the given product
	 *
	 * @param Mage_Catalog_Model_Product	$product		Product
	 * @param int							$w				Image width
	 * @param int							$h				Image height
	 * @param string						$imgVersion		Image version: image, small_image, thumbnail
	 * @param mixed							$file			Specific file
	 * @return string
	 */
	public function getImg($product, $w, $h, $imgVersion='image', $file=NULL)
	{
		$url = '';
		if ($h <= 0)
		{
			$url = Mage::helper('catalog/image')
				->init($product, $imgVersion, $file)
				->constrainOnly(true)
				->keepAspectRatio(true)
				->keepFrame(false)
				//->setQuality(90)
				->resize($w);
		}
		else
		{
			$url = Mage::helper('catalog/image')
				->init($product, $imgVersion, $file)
				->resize($w, $h);
		}
		return $url;
	}
}
