<?php

class Infortis_Infortis_Helper_Responsive extends Mage_Core_Helper_Abstract
{	
	/**
	 * Map maximum page width to maximum responsive layout breakpoint
	 *
	 * @param int		Maximum page width
	 * @param string	Store code
	 * @return int
	 */
	public function mapWidthToBreakpoint($width, $storeCode = NULL)
	{
		if ($width < 1280)
			$maxBreak = 960;
		elseif ($width < 1360)
			$maxBreak = 1280;
		elseif ($width < 1440)
			$maxBreak = 1360;
		elseif ($width < 1680)
			$maxBreak = 1440;
		else
			$maxBreak = 1680;
		
		return $maxBreak;
	}
	
	/**
	 * Get array: map responsive layout breakpoint to actual page width
	 *
	 * @return array
	 */
	public function getArrayMapBreakpointToActualWidth()
	{
		return array(
			"1680" => 1520,
			"1440" => 1380,
			"1360" => 1300,
			"1280" => 1200,
			"960" => 960,
		);
	}
}
