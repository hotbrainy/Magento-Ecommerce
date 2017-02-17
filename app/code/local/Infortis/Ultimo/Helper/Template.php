<?php

class Infortis_Ultimo_Helper_Template extends Mage_Core_Helper_Abstract
{
	/**
	 * Render "Add to" links for category view.
	 *
	 * @param product object
	 * @param URL of the "Add to compare" link
	 * @param additional CSS class name
	 * @return string
	 */
	public function getCategoryAddtoLinks($_product, $_compareUrl, $wrapperClasses = '')
	{
		$html = '';

		if (Mage::helper('wishlist')->isAllow())
		{
			$html .= '<li><a href="' . Mage::helper('wishlist')->getAddUrl($_product) . '" class="link-wishlist" title="' . $this->__('Add to Wishlist') . '">' . $this->__('Add to Wishlist') . '</a></li>';
		}
		
		if ($_compareUrl)
		{
			$html .= '<li><a href="' . $_compareUrl . '" class="link-compare" title="' . $this->__('Add to Compare') . '">' . $this->__('Add to Compare') . '</a></li>';
		}
		
		//If any link rendered
		if (!empty($html))
		{
			return '<ul class="add-to-links clearer '. $wrapperClasses .'">' . $html . '</ul>';
		}
		return $html;
	}
	
	/**
	 * Render "Add to" links for category view. Use "feature" box.
	 *
	 * @param product object
	 * @param URL of the "Add to compare" link
	 * @param additional CSS class name
	 * @return string
	 */
	public function getCategoryAddtoLinksComplex($_product, $_compareUrl, $wrapperClasses = '')
	{
		$html = '';

		if (Mage::helper('wishlist')->isAllow())
		{			
			$html .= '
			<li><a class="link-wishlist feature feature-icon-hover first v-centered-content" 
				href="' . Mage::helper('wishlist')->getAddUrl($_product) . '" 
				title="' . $this->__('Add to Wishlist') . '">
				<span class="v-center">
					<span class="icon ib ic ic-heart"></span>
				</span>
				<span class="v-center">' . $this->__('Add to Wishlist') . '</span>
			</a></li>';
		}
		
		if ($_compareUrl)
		{
			$html .= '
			<li><a class="link-compare feature feature-icon-hover first v-centered-content"
				href="' . $_compareUrl . '" 
				title="' . $this->__('Add to Compare') . '">
				<span class="v-center">
					<span class="icon ib ic ic-compare"></span>
				</span>
    	        <span class="v-center">' . $this->__('Add to Compare') . '</span>
			</a></li>';
		}
		
		//If any link rendered
		if (!empty($html))
		{
			return '<ul class="add-to-links clearer ' . $wrapperClasses .'">' . $html . '</ul>';
		}
		return $html;
	}

	/**
	 * Render "Add to" links for category view using only icons
	 *
	 * @param product object
	 * @param URL of the "Add to compare" link
	 * @param additional CSS class name
	 * @return string
	 */
	public function getCategoryAddtoLinksIcons($_product, $_compareUrl, $wrapperClasses = '')
	{
		$html = '';

		if (Mage::helper('wishlist')->isAllow())
		{			
			$html .= '
			<li><a class="link-wishlist" 
				href="' . Mage::helper('wishlist')->getAddUrl($_product) . '" 
				title="' . $this->__('Add to Wishlist') . '">
					<span class="2 icon ib ic ic-heart"></span>
			</a></li>';
		}
		
		if ($_compareUrl)
		{
			$html .= '
			<li><a class="link-compare"
				href="' . $_compareUrl . '" 
				title="' . $this->__('Add to Compare') . '">
					<span class="2 icon ib ic ic-compare"></span>
			</a></li>';
		}
		
		//If any link rendered
		if (!empty($html))
		{
			return '<ul class="add-to-links clearer ' . $wrapperClasses .'">' . $html . '</ul>';
		}
		return $html;
	}

	/**
	 * Method wrapper for bakcward compatibility
	 *
	 */
	public function getCategoryAddtoLinksComplex_2($_product, $_compareUrl, $wrapperClasses = '')
	{
		return $this->getCategoryAddtoLinksIcons($_product, $_compareUrl, $wrapperClasses);
	}

}
