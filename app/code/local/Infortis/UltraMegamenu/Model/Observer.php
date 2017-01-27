<?php

class Infortis_UltraMegamenu_Model_Observer
{
	public function hookTo_CatalogCategoryFlatLoadnodesBefore(Varien_Event_Observer $observer)
	{
		$columns = array();
		$observer->getSelect()->columns(
			array('umm_dd_type', 'umm_dd_width', 'umm_dd_proportions', 'umm_dd_columns', 'umm_dd_blocks', 'umm_cat_target', 'umm_cat_label')
		);
	}
}