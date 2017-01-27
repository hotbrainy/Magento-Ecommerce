<?php

/**
 * Grid columns form field
 *
 */
class Infortis_Infortis_Lib_Data_Form_Element_Grid_Columns
	extends Varien_Data_Form_Element_Text
{
	protected $_secondaryAttributes = array('type');
	protected $_delimiter = ';';
	protected $_maxColumns = 3;
	protected $_gridUnitMax = 12;
	protected $_gridUnitZero = 0;

	protected $_labels = array('Default 1', 'Default 2');
	
	public function getElementHtml()
	{
		$this->_gridUnitZero = 0;
		$html = '';
		$id = $this->getHtmlId();
		$wrapperId = $id . '_gridcolumns';
		$mainValidationId = 'advice-entry-' . $id;
		$attributeValue = $this->getEscapedValue();

		//Prepare unit values
		$exploded = explode($this->_delimiter, $attributeValue);
		$units = array();
		for ($i = 0; $i < $this->_maxColumns; $i++)
		{
			if (isset($exploded[$i]))
			{
				//Replace empty units with zero
				if (!trim($exploded[$i]))
				{
					$units[] = $this->_gridUnitZero;
				}
				else
				{
					$units[] = $exploded[$i];
				}
			}
			else
			{
				$units[] = $this->_gridUnitZero;
			}
		}

		//Main field
		$html .= '<input id="' . $id . '" name="' . $this->getName() . '" ';
		$html .= 'style="display:none;" ';
		$html .= 'value="' . $attributeValue . '" ';
		$html .= $this->serialize($this->getHtmlAttributes()).'/>'."\n";

		//Unit fields
		$html .= '<div id="' . $wrapperId . '" class="">';

			for ($i = 0; $i < $this->_maxColumns; $i++)
			{
				$curFieldId = $id . '_' . ($i+1);
				$html .= '<div class="unit">';
				if (isset($this->_labels[$i]))
				{
					$html .= '<label for="' . $curFieldId . '">' . $this->_labels[$i] . '</label>';
				}
				else
				{
					$html .= '<label for="' . $curFieldId . '"></label>';
				}
				$html .= '<input id="' . $curFieldId . '" ';
				$html .= 'class="input-text grid-unit-entry validate-number" ';
				$html .= 'value="' . $units[$i] . '" ';
				$html .= $this->serialize($this->_secondaryAttributes).'/>'."\n";
				$html .= '</div>';
			}

		$html .= '</div>';
		$html .= '<div class="validation-advice" style="display:none;" id="' . $mainValidationId . '">' . Mage::helper('ultramegamenu')->__('Sum of the grid units should be equal %s', $this->_gridUnitMax) . '</div>';

		//Styles
		$html .= '<style>
		#' . $wrapperId . ' .grid-unit-entry { width:100% !important; }
		#' . $mainValidationId . ' { background:none; padding-left:0; margin-left:0; }
		#' . $wrapperId . ' .unit { display:inline-block; margin-right:10px; width:100px !important;}
		</style>';

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
				var mainValidationId	= \'#' . $mainValidationId . '\';
				var delimiter			= \'' . $this->_delimiter . '\';
				var gridMax				= ' . $this->_gridUnitMax . ';
				var gridZero			= 0;
				var colorValid			= "deepskyblue";
				var colorInvalid		= "red";

				var onChange = function(e) {
					var target = $(e.target);
					var output = "";
					var sum = 0;

					target.css("color", colorValid);
					target.addClass("modified");

					//Clear the value
					clear(target);

					//Sum
					$(fieldsWrapperId + " input").each(function() {
						output += $(this).val() + delimiter;
						sum += parseInt($(this).val(), 10);
					});
					$(mainId).val(output);

					//Validate all
					if (sum !== gridMax)
					{
						validationFailed(target);
					}
					else
					{
						validationPassed(target);
					}
				}

				var clear = function(target) {
					var n = $.trim(target.val());
					n = parseInt(n);
					if (isNaN(n))
					{
						n = gridZero;
					}

					target.val(n);
				}

				var validationFailed = function(target) {
					$(mainValidationId).show();
					target.css("color", colorInvalid);
				}

				var validationPassed = function(target) {
					$(mainValidationId).hide();
					$(fieldsWrapperId + " input").each(function() {
						$this = $(this);
						if ($this.hasClass("modified"))
						{
							$this.css("color", colorValid);
						}
					});
				}

				$(fieldsWrapperId).on("change keyup", "input", function(e) {
					onChange(e);
				});
				
			}); //end: on document ready
		//]]>
		</script>
		';

		$html .= $this->getAfterElementHtml();
		return $html;
	}
}
