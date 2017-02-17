<?php
/**
 * @category    Fishpig
 * @package     Fishpig_AttributeSplash
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_AttributeSplash_Block_Adminhtml_Page_Edit_Tab_Customfields extends Fishpig_AttributeSplash_Block_Adminhtml_Page_Edit_Tab_Abstract
{
	/**
	 * Add the design elements to the form
	 *
	 * @return $this
	 */
	protected function _prepareForm()
	{
		parent::_prepareForm();

		$fieldset = $this->getForm()->addFieldset('splash_customfields', array(
			'legend'=> $this->helper('adminhtml')->__('Custom Fields'),
			'class' => 'fieldset-wide',
		));
		
		if ($page = Mage::registry('splash_page')) {
			foreach($page->getAllAvailableCustomFields() as $alias => $title) {
				$fieldset->addField($alias, 'editor', array(
					'name' => 'custom_fields[' . $alias . ']',
					'label' => $this->__($title),
					'title' => $this->__($title),
					'style' => 'width:600px;',
					'note' => '<?php echo $page->get' . str_replace(' ', '', ucwords(str_replace('_', ' ', $alias))) . '(); ?>',
				));
			}
		}
		
		$this->getForm()->setValues($this->_getFormData());
		
		return $this;
	}
}
