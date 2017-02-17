<?php

class Infortis_Ultimo_Helper_Grid extends Mage_Core_Helper_Abstract
{
	/**
	 * Values: number of columns / grid item width
	 *
	 * @var array
	 */
	protected $_itemWidth = array(
		"1" => 98,
		"2" => 48,
		"3" => 31.3333,
		"4" => 23,
		"5" => 18,
		"6" => 14.6666,
		"7" => 12.2857,
		"8" => 10.5,
	);

	/**
	 * Get CSS for grid item based on number of columns
	 *
	 * @param int $columnCount
	 * @return string
	 */
	public function getCssGridItem($columnCount)
	{
		$out = "\n";
		$out .= '.itemgrid.itemgrid-adaptive .item { width:' . $this->_itemWidth[$columnCount] . '%; clear:none !important; }' . "\n";
		
		if ($columnCount > 1)
		{
			$out .= '.itemgrid.itemgrid-adaptive > li:nth-of-type(' . $columnCount . 'n+1) { clear:left !important; }' . "\n";
		}

		return $out;
	}

	/**
	 * Get CSS to disable hover effect
	 *
	 * @return string
	 */
	public function getCssDisableHoverEffect()
	{
		return '
	/* Disable hover effect
	-------------------------------------------------------------- */
		/* Cancel "hover effect" styles: apply the same styles which item has without "hover effect" */
		.category-products-grid.hover-effect .item { border-top: none; }
		.category-products-grid.hover-effect .item:hover {
			margin-left:0;
			margin-right:0;
			padding-left:1%;
			padding-right:1%;
			box-shadow: none !important;
		}

		/* Show elements normally displayed only on hover */
		.category-products-grid.hover-effect .item .display-onhover { display:block !important; }
		
		/* Show full name even if enabled: display name in single line */
		.products-grid.single-line-name .item .product-name { overflow: visible; white-space: normal; }

		/* Spaces between items */
		.category-products-grid.hover-effect .item { margin-bottom: 20px; }
		';
	}

	/**
	 * Get CSS to disable hover effect
	 *
	 * @return string
	 */
	public function getCssHideAddtoLinks()
	{
		return'
	/* Products grid
	-------------------------------------------------------------- */
		.products-grid.category-products-grid.hover-effect .item .add-to-links, /* To override "display-onhover" */
		.products-grid .item .add-to-links { display: none !important; }
		';
	}
}
