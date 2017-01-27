<?php

class SteveB27_Utility_Helper_Data extends Mage_Catalog_Helper_Output
{
	public function getSeriesText($product, $tag = 'h2')
	{
		$taglen = strpos($tag, ' ');
		if($taglen) {
			$endtag = substr($tag,0,$taglen);
		} else {
			$endtag = $tag;
		}
		$html = '';
		if($product->getBookSeries()) {
			if($product->getBookSeries_2()) {
				$html = sprintf("<%s>%s Series, Book %s</%s>",
					$tag,
					$this->productAttribute($product, $product->getBookSeries(), 'book_series'),
					$this->productAttribute($product, $product->getBookSeries_2(), 'book_series_2'),
					$endtag
				);
			} else {
				$html = sprintf("<%s>%s Series</%s>",
					$tag,
					$this->productAttribute($product, $product->getBookSeries(), 'book_series'),
					$endtag
				);
			}
			$html .= "\n";
		}
		
		return $html;
	}
	
    public function productAttribute($product, $attributeHtml, $attributeName)
    {
        $attribute = Mage::getSingleton('eav/config')->getAttribute(Mage_Catalog_Model_Product::ENTITY, $attributeName);
        if($attribute->getBackendType() == Varien_Db_Ddl_Table::TYPE_DECIMAL &&
			(strpos($attributeHtml,'.') !== false)
		) {
			$attributeHtml = rtrim(rtrim($attributeHtml,'0'),'.');
		}
        $attribute->getBackendType();
        if ($attribute && $attribute->getId() && ($attribute->getFrontendInput() != 'media_image')
            && (!$attribute->getIsHtmlAllowedOnFront() && !$attribute->getIsWysiwygEnabled())) {
                if ($attribute->getFrontendInput() != 'price') {
                    $attributeHtml = $this->escapeHtml($attributeHtml);
                }
                if ($attribute->getFrontendInput() == 'textarea') {
                    $attributeHtml = nl2br($attributeHtml);
                }
        }
        if ($attribute->getIsHtmlAllowedOnFront() && $attribute->getIsWysiwygEnabled()) {
            if (Mage::helper('catalog')->isUrlDirectivesParsingAllowed()) {
                $attributeHtml = $this->_getTemplateProcessor()->filter($attributeHtml);
            }
        }

        $attributeHtml = $this->process('productAttribute', $attributeHtml, array(
            'product'   => $product,
            'attribute' => $attributeName
        ));

        return $attributeHtml;
    }
}
