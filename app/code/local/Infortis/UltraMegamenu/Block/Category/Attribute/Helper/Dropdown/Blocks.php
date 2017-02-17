<?php

/**
 * Form textarea element
 */
class Infortis_UltraMegamenu_Block_Category_Attribute_Helper_Dropdown_Blocks
	extends Mage_Adminhtml_Block_Catalog_Helper_Form_Wysiwyg
{
	const DELIMITER = '#@#';
	const MAX_BLOCKS = 4;

	protected $_secondaryAttributes = array('title', 'rows', 'cols', 'readonly', 'disabled', 'tabindex');

	protected $_labels = array('Top Block', 'Left Block', 'Right Block', 'Bottom Block');

	public function getElementHtml()
	{
		$html = '';
		$id = $this->getHtmlId();
		$wrapperId = $id . '_units';
		$attributeValue = $this->getEscapedValue();

		//Prepare unit values
		$exploded = explode(self::DELIMITER, $attributeValue);
		$units = array();
		for ($i = 0; $i < self::MAX_BLOCKS; $i++)
		{
			if (isset($exploded[$i]))
			{
				$units[] = $exploded[$i];
			}
			else
			{
				$units[] = '';
			}
		}

		//Main field
		$this->addClass('textarea');
		$html .= '<textarea id="' . $id . '" name="'.$this->getName().'" '.$this->serialize($this->getHtmlAttributes()).' ';
		$html .= 'style="display:none;" ';
		$html .= '>';
		$html .= $attributeValue;
		$html .= "</textarea>";

		//Unit fields
		$html .= '<div id="' . $wrapperId . '" class="">';

			for ($i = 0; $i < self::MAX_BLOCKS; $i++)
			{
				$curFieldId = $id . '_' . ($i+1);
				$html .= '<label for="' . $curFieldId . '">' . $this->_labels[$i] . '</label>';
				$html .= '<textarea id="' . $curFieldId . '" '.$this->serialize($this->_secondaryAttributes).' ';
				$html .= 'class="textarea" ';
				$html .= 'style="height:8em;" ';
				$html .= '>';
				$html .= $units[$i];
				$html .= '</textarea>';
				$html .= $this->getAfterElementHtml($curFieldId);
				$html .= '<br/><br/>';
			}

		$html .= '</div>';

		//Scripts
		if (!Mage::registry('infortis_admin_jquery'))
		{
			$jqueryUrl = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_JS) . 'infortis/jquery/jquery-for-admin.min.js';
			$html .= '<script type="text/javascript" src="' . $jqueryUrl . '"></script>';
			$html .= '<script type="text/javascript">jQuery.noConflict();</script>';
			Mage::register('infortis_admin_jquery', 1);
		}
		$html .= '
		<script type="text/javascript">
		//<![CDATA[
			jQuery(function($) {

				var mainId				= \'#' . $id . '\';
				var fieldsWrapperId		= \'#' . $wrapperId . '\';
				var delimiter			= \'' . self::DELIMITER . '\';

				var onChange = function(e) {
					var target = $(e.target);
					var output = "";

					target.addClass("modified");

					//Compile
					$(fieldsWrapperId + " textarea").each(function() {
						output += $.trim($(this).val()) + delimiter;
					});
					$(mainId).val(output);
				}

				$(fieldsWrapperId).on("focus change", "textarea", function(e) {
					onChange(e);
				});
				
			}); //end: on document ready
		//]]>
		</script>
		';

		return $html;
	}

	/**
	 * Additional html: put it at the end of each element html
	 *
	 * @return string
	 */
	public function getAfterElementHtml($fieldId=null)
	{
		//$html = parent::getAfterElementHtml();
		$html = '';
		if ($this->getIsWysiwygEnabled()) {
			$disabled = ($this->getDisabled() || $this->getReadonly());
			$html .= Mage::getSingleton('core/layout')
				->createBlock('adminhtml/widget_button', '', 
					array(
						'label'		=> Mage::helper('catalog')->__('WYSIWYG Editor'),
						'type'		=> 'button',
						'disabled'	=> $disabled,
						'class'		=> ($disabled) ? 'disabled btn-wysiwyg' : 'btn-wysiwyg',
						'onclick'	=> 'catalogWysiwygEditor.open(\''.Mage::helper('adminhtml')->getUrl('*/*/wysiwyg').'\', \'' . $fieldId . '\')'
					)
				)->toHtml();
		}
		return $html;
	}
}