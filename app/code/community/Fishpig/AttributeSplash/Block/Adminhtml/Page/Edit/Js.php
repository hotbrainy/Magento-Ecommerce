<?php
/**
 * @category    Fishpig
 * @package     Fishpig_AttributeSplash
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_AttributeSplash_Block_Adminhtml_Page_Edit_Js extends Mage_Core_Block_Text
{
	/**
	 * Add the JS
	 *
	 * @return $this
	 */
	protected function _prepareLayout()
	{
		$this->setText(sprintf("
			<script type=\"text/javascript\">
				//<![CDATA[				
					(function() {
						var create = {
							go: function() {
								this.attr = $('splash_attribute_id');
								this.opt = $('splash_option_id');
								
								if (!this.attr || !this.opt) {
									throw 'Missing required input fields';
								}
								
								this.name = $('splash_display_name');

								this.options = new Array();
								this.onAttributeChange();
								
								this.attr.observe('change', this.onAttributeChange.bindAsEventListener(this));
								
								this.opt.observe('change', this.onOptionChange.bindAsEventListener(this));
							},
							hasOptions: function() {
								return typeof this.options[this.attr.getValue()] !== 'undefined';
							},
							updateOptions: function() {
								this.opt.options = null;
								
								this.opt.options[0] = new Option('%s', '');

								this.options[this.attr.getValue()].each(function(elem, ind) {
									this.opt.options[ind+1] = new Option(elem.label, elem.value);
								}.bind(this));
							},
							getOptions: function() {
								new Ajax.Request('%s?attribute=' + this.attr.getValue(), {
									onSuccess: function(transport) {
										var json = transport.responseText.evalJSON();
										
										if (json.error) {
											throw json.error;
										}
										
										this.options[this.attr.getValue()] = new Array();

										json.options.each(function(elem, ind) {
											this.options[this.attr.getValue()].push({'value': elem.value, 'label': elem.label});
										}.bind(this));
										
										this.updateOptions();
									}.bind(this)
								});
							},
							onAttributeChange: function() {
								if (this.hasOptions()) {
									this.updateOptions();
								}
								else {
									this.getOptions();
								}
							},
							onOptionChange: function() {
								if (this.opt.getValue()) {
									if (this.name.getValue() === '') {
										this.name.setValue(this.opt.options[this.opt.selectedIndex].label);
									}
								}
							}
						};
						
						try {
							return create.go();
						}
						catch (e) {
							alert(e);
						}
					})();
				//]]>
			</script>
		", Mage::helper('adminhtml')->__('-- Please Select --'), Mage::getModel('adminhtml/url')->getUrl('*/*/options')));
		
		return parent::_prepareLayout();
	}
}
