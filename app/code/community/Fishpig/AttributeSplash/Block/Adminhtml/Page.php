<?php
/**
 * @category    Fishpig
 * @package     Fishpig_AttributeSplash
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_AttributeSplash_Block_Adminhtml_Page extends Mage_Adminhtml_Block_Widget_Grid_Container
{
	public function __construct()
	{	
		parent::__construct();
		
		$this->_controller = 'adminhtml_page';
		$this->_blockGroup = 'attributeSplash';
		$this->_headerText = 'Splash: ' . $this->__('Pages');

		$this->_removeButton('add');
	}
}