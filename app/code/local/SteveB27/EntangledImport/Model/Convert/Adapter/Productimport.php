<?php

class SteveB27_EntangledImport_Model_Convert_Adapter_Productimport extends Mage_Catalog_Model_Convert_Adapter_Product
{
	protected $book_imprints_category 		= 'Imprints';
	protected $book_imprints_category_id 	= '';
	
	protected $book_genre_category 			= 'Genres';
	protected $book_genre_category_id 		= '';
	
	protected $book_category	 			= 'Books';
	protected $book_category_id 			= '';
	
	/**
     * Affected entity ids
     *
     * @var array
     */
    protected $_affectedEntityIds = array();

    /**
     * Store affected entity ids
     *
     * @param  int|array $ids
     * @return Mage_Catalog_Model_Convert_Adapter_Product
     */
    protected function _addAffectedEntityIds($ids)
    {
        if (is_array($ids)) {
            foreach ($ids as $id) {
                $this->_addAffectedEntityIds($id);
            }
        } else {
            $this->_affectedEntityIds[] = $ids;
        }

        return $this;
    }
	
	/**
	* Save product (import)
	* 
	* @param array $importData 
	* @throws Mage_Core_Exception
	* @return bool 
	*/
	public function saveRow( array $importData )
	{
		#$product = $this -> getProductModel();
		$product = $this->getProductModel()
            ->reset();
		#$product -> setData( array() );

		#if ( $stockItem = $product -> getStockItem() ) {
			#$stockItem -> setData( array() );
		#}
		$importData = $this->remap($importData);
		
		if (empty($importData['store'])) {
            if (!is_null($this->getBatchParams('store'))) {
                $store = $this->getStoreById($this->getBatchParams('store'));
            } else {
                $message = Mage::helper('catalog')->__('Skip import row, required field "%s" not defined', 'store');
                Mage::throwException($message);
				Mage::log(sprintf('Skip import row, required field "store" not defined', $message), null,'ce_product_import_export_errors.log');
            }
        }
        else {
            $store = $this->getStoreByCode($importData['store']);
        }
		
		if ($store === false) {
            $message = Mage::helper('catalog')->__('Skip import row, store "%s" field not exists', $importData['store']);
            Mage::throwException($message);
			Mage::log(sprintf('Skip import row, store "'.$importData['store'].'" field not exists', $message), null,'ce_product_import_export_errors.log');
		}

		if (empty($importData['sku'])) {
				$message = Mage::helper('catalog')->__('Skip import row, required field "%s" not defined', 'sku');
				Mage::throwException($message);
				Mage::log(sprintf('Skip import row, required field "sku" not defined', $message), null,'ce_product_import_export_errors.log');
		}
		$product->setStoreId($store->getId());
		$productId = $product->getIdBySku($importData['sku']);

		$iscustomoptions = "false"; //sets currentcustomoptionstofalse
		$iscustomoptionsrequired = "false";
		$finalsuperattributepricing = "";
		$finalgroup_price_price = "";
		$finalsuperattributetype = $importData['type'];
		if (isset($importData['super_attribute_pricing']) && $importData['super_attribute_pricing'] !="") {
    	$finalsuperattributepricing = $importData['super_attribute_pricing'];
		}
		if (isset($importData['group_price_price']) && $importData['group_price_price'] !="") {
    	$finalgroup_price_price = $importData['group_price_price'];
		}
		$new = true; // fix for duplicating attributes error
		
		if ($productId) {
            $product->load($productId);
			$new = false; // fix for duplicating attributes error
        }
        else {
            $productTypes = $this->getProductTypes();
            $productAttributeSets = $this->getProductAttributeSets();

            /**
             * Check product define type
             */
            if (empty($importData['type']) || !isset($productTypes[strtolower($importData['type'])])) {
                $value = isset($importData['type']) ? $importData['type'] : '';
                $message = Mage::helper('catalog')->__('Skip import row, is not valid value "%s" for field "%s"', $value, 'type');
                Mage::throwException($message);
				Mage::log(sprintf('Skip import row, is not valid value "'.$value.'" for field type', $message), null,'ce_product_import_export_errors.log');
            }
            $product->setTypeId($productTypes[strtolower($importData['type'])]);
            /**
             * Check product define attribute set
             */
            if (empty($importData['attribute_set']) || !isset($productAttributeSets[$importData['attribute_set']])) {
                $value = isset($importData['attribute_set']) ? $importData['attribute_set'] : '';
                $message = Mage::helper('catalog')->__('Skip import row, is not valid value "%s" for field "%s"', $value, 'attribute_set');
                Mage::throwException($message);
				Mage::log(sprintf('Skip import row, is not valid value "'.$value.'" for field attribute_set', $message), null,'ce_product_import_export_errors.log');
            }
            $product->setAttributeSetId($productAttributeSets[$importData['attribute_set']]);

            foreach ($this->_requiredFields as $field) {
                $attribute = $this->getAttribute($field);
                if (!isset($importData[$field]) && $attribute && $attribute->getIsRequired()) {
                    $message = Mage::helper('catalog')->__('Skip import row, required field "%s" for new products not defined', $field);
                    Mage::throwException($message);
                }
            }
        }
		
		$this->setProductTypeInstance($product);
		
		// delete disabled products
		// note "Disabled text should be converted to handle multi-lanugage values aka age::helper('catalog')->__(''); type deal
		
		if ( $importData['status'] == 'Delete' || $importData['status'] == 'delete' ) {
			$product = Mage :: getSingleton( 'catalog/product' ) -> load( $productId );
			$this -> _removeFile( Mage :: getSingleton( 'catalog/product_media_config' ) -> getMediaPath( $product -> getData( 'image' ) ) );
			$this -> _removeFile( Mage :: getSingleton( 'catalog/product_media_config' ) -> getMediaPath( $product -> getData( 'small_image' ) ) );
			$this -> _removeFile( Mage :: getSingleton( 'catalog/product_media_config' ) -> getMediaPath( $product -> getData( 'thumbnail' ) ) );
			$media_gallery = $product -> getData( 'media_gallery' );
			foreach ( $media_gallery['images'] as $image ) {
				$this -> _removeFile( Mage :: getSingleton( 'catalog/product_media_config' ) -> getMediaPath( $image['file'] ) );
			} 
			$product -> delete();
			return true;
		} 
		
		
		$currentproducttype = $importData['type'];
		
		if ($importData['type'] == 'configurable') {
			
			$product->setCanSaveConfigurableAttributes(true);
			$configAttributeCodes = $this->userCSVDataAsArray($importData['config_attributes']);
			$usingAttributeIds = array();

			/***
			* Check the product's super attributes (see catalog_product_super_attribute table), and make a determination that way.
			**/
			$cspa  = $product->getTypeInstance()->getConfigurableAttributesAsArray($product);
			$attr_codes = array();
			if(isset($cspa) && !empty($cspa)){ //found attributes
				foreach($cspa as $cs_attr){
				//$attr_codes[$cs_attr['attribute_id']] = $cs_attr['attribute_code'];
					$attr_codes[] = $cs_attr['attribute_id'];
				}
			}


			foreach($configAttributeCodes as $attributeCode) {
				$attribute = $product->getResource()->getAttribute($attributeCode);
				if ($product->getTypeInstance()->canUseAttribute($attribute)) {
					//if (!in_array($attributeCode,$attr_codes)) { // fix for duplicating attributes error
					if ($new) { // fix for duplicating attributes error // <---------- this must be true to fill $usingAttributes
						$usingAttributeIds[] = $attribute->getAttributeId();
					}
				}
			}
			if (!empty($usingAttributeIds)) {
				$product->getTypeInstance()->setUsedProductAttributeIds($usingAttributeIds);
				$updateconfigurablearray = array();
				$insidearraycount=0;
				$finalarraytoimport = $product->getTypeInstance()->getConfigurableAttributesAsArray();
				$updateconfigurablearray = $product->getTypeInstance()->getConfigurableAttributesAsArray();
					
					foreach($updateconfigurablearray as $eacharrayvalue) {	
					 if($this->getBatchParams('configurable_use_default') != "") {			
					 	$finalarraytoimport[$insidearraycount]['use_default'] = $this->getBatchParams('configurable_use_default'); //added in 1.5.x 
						//<var name="configurable_use_default"><![CDATA[1]]></var>
					 }
					 $finalarraytoimport[$insidearraycount]['label'] = $eacharrayvalue['frontend_label'];
					 #$finalarraytoimport[$insidearraycount]['values'] = array( );
					 #$attribute = Mage::getModel('catalog/product_type_configurable_attribute')->setProductAttribute($eacharrayvalue['attribute_id']);
					 #$attribute->setStoreLabel($eacharrayvalue['frontend_label']);
					 #print_r($attribute->getStoreLabel());
					 $insidearraycount+=1;
					}
				$product->setConfigurableAttributesData($finalarraytoimport);
				$product->setCanSaveConfigurableAttributes(true);
				$product->setCanSaveCustomOptions(true);
			}
			if (isset($importData['associated'])) {
				$product->setConfigurableProductsData($this->skusToIds($importData['associated'], $product));
			}
		}
		//THIS IS FOR DOWNLOADABLE PRODUCTS
		$filenameforsamplearrayforimport = array();
		if ($importData['type'] == 'downloadable' && $importData['downloadable_options'] != "") {
			if ($new) {
			    $downloadableitems = array();
			  	$filearrayforimport = array();
			  	$filenameforsamplearrayforimport = array();
				#$filenameforsamplearrayforimport = "";
				$downloadableitemsoptionscount=0;
				//THIS IS FOR DOWNLOADABLE OPTIONS
				$commadelimiteddata = explode('|',$importData['downloadable_options']);
				foreach ($commadelimiteddata as $data) {
					$configBundleOptionsCodes = $this->userCSVDataAsArray($data);
					
					$downloadableitems['link'][$downloadableitemsoptionscount]['is_delete'] = 0;
					$downloadableitems['link'][$downloadableitemsoptionscount]['link_id'] = 0;
					$downloadableitems['link'][$downloadableitemsoptionscount]['title'] = $configBundleOptionsCodes[0];
					$downloadableitems['link'][$downloadableitemsoptionscount]['price'] = $configBundleOptionsCodes[1];
					$downloadableitems['link'][$downloadableitemsoptionscount]['number_of_downloads'] = $configBundleOptionsCodes[2];
					$downloadableitems['link'][$downloadableitemsoptionscount]['is_shareable'] = 2;
					if(isset($configBundleOptionsCodes[5])) {
						#$downloadableitems['link'][$downloadableitemsoptionscount]['sample'] = '';
						if($configBundleOptionsCodes[3] == "file") {
						#$filenameforsamplearrayforimport = $configBundleOptionsCodes[5];
						$filenameforsamplearrayforimport[] = array('file'  => ''.$configBundleOptionsCodes[5].'' , 'name'  => ''.$configBundleOptionsCodes[0].'' , 'price'  => ''.$configBundleOptionsCodes[1].'');
						  //Create and send the JSON structure instead of the file  name
					  $tempSampleFile = '[{"file": "'.$configBundleOptionsCodes[5].'", "status": "new"}]';
					  $downloadableitems['link'][$downloadableitemsoptionscount]['sample'] = array('file' => ''.$tempSampleFile.'', 'type' => 'file', 'url'  => '');
  
					  //$downloadableitems['link'][$downloadableitemsoptionscount]['sample'] = array('file' => ''.$configBundleOptionsCodes[5].'', 'type' => 'file', 'url'  => '');
						} else {
						
						$downloadableitems['link'][$downloadableitemsoptionscount]['sample'] = array('file' => '[]', 'type' => 'url', 'url'  => ''.$configBundleOptionsCodes[5].'');
						}
					} else {
						$downloadableitems['link'][$downloadableitemsoptionscount]['sample'] = '';
					}
					$downloadableitems['link'][$downloadableitemsoptionscount]['file'] = '';
					$downloadableitems['link'][$downloadableitemsoptionscount]['type'] = $configBundleOptionsCodes[3];
					#$downloadableitems['link'][$downloadableitemsoptionscount]['link_url'] = $configBundleOptionsCodes[4];
					if($configBundleOptionsCodes[3] == "file") {
						#$filearrayforimport = array('file'  => 'media/import/mypdf.pdf' , 'name'  => 'asdad.txt', 'size'  => '316', 'status'  => 'old');
						#$document_directory =  Mage :: getBaseDir( 'media' ) . DS . 'import' . DS;
						#echo "DIRECTORY: " . $document_directory;
						#$filearrayforimport = '[{"file": "/home/discou33/public_html/media/import/mypdf.pdf", "name": "mypdf.pdf", "status": "new"}]';
						#$filearrayforimport = '[{"file": "mypdf.pdf", "name": "quickstart.pdf", "size": 324075, "status": "new"}]';
						$filearrayforimport[] = array('file'  => ''.$configBundleOptionsCodes[4].'' , 'name'  => ''.$configBundleOptionsCodes[0].'' , 'price'  => ''.$configBundleOptionsCodes[1].'');
						
						if(isset($configBundleOptionsCodes[5])) {
							if($configBundleOptionsCodes[5] == 0) {
											$linkspurchasedstatus = 0;
											$linkspurchasedstatustext = false;
							} else {
											$linkspurchasedstatus = 1;
											$linkspurchasedstatustext = true;
							}
							$product->setLinksPurchasedSeparately($linkspurchasedstatus);
							$product->setLinksPurchasedSeparately($linkspurchasedstatustext);
						}
						
						
						#$product->setLinksPurchasedSeparately(0);
						#$product->setLinksPurchasedSeparately(false);
					
						#$files = Zend_Json::decode($filearrayforimport);
						#$files = "mypdf.pdf";
						
						#$downloadableitems['link'][$downloadableitemsoptionscount]['file'] = $filearrayforimport;
					} else if($configBundleOptionsCodes[3] == "url") {
						$downloadableitems['link'][$downloadableitemsoptionscount]['link_url'] = $configBundleOptionsCodes[4];
					}
					$downloadableitems['link'][$downloadableitemsoptionscount]['sort_order'] = 0;
			    $product->setDownloadableData($downloadableitems);
					$downloadableitemsoptionscount+=1;
				}
				#print_r($downloadableitems);
			} else {
					//first delete all links then we update
				  $download_info=Mage::getModel('downloadable/product_type');
					$download_info->setProduct($product);
					if ($download_info->hasLinks()) {
						$_links=$download_info->getLinks();
						foreach ($_links as $_link) {
										$_link->delete();
						}
					}
					
					//begin update
				$downloadableitems = array();
			  	$filearrayforimport = array();
				//$filenameforsamplearrayforimport = "";
				$filenameforsamplearrayforimport = array(); //bug fix 
				$downloadableitemsoptionscount=0;
				//THIS IS FOR DOWNLOADABLE OPTIONS
				$commadelimiteddata = explode('|',$importData['downloadable_options']);
				foreach ($commadelimiteddata as $data) {
					$configBundleOptionsCodes = $this->userCSVDataAsArray($data);
					
					$downloadableitems['link'][$downloadableitemsoptionscount]['is_delete'] = 0;
					$downloadableitems['link'][$downloadableitemsoptionscount]['link_id'] = 0;
					$downloadableitems['link'][$downloadableitemsoptionscount]['title'] = $configBundleOptionsCodes[0];
					$downloadableitems['link'][$downloadableitemsoptionscount]['price'] = $configBundleOptionsCodes[1];
					$downloadableitems['link'][$downloadableitemsoptionscount]['number_of_downloads'] = $configBundleOptionsCodes[2];
					$downloadableitems['link'][$downloadableitemsoptionscount]['is_shareable'] = 2;
					if(isset($configBundleOptionsCodes[5])) {
						#$downloadableitems['link'][$downloadableitemsoptionscount]['sample'] = '';
						if($configBundleOptionsCodes[3] == "file") {
						#$filenameforsamplearrayforimport = $configBundleOptionsCodes[5];
						$filenameforsamplearrayforimport[] = array('file'  => ''.$configBundleOptionsCodes[5].'' , 'name'  => ''.$configBundleOptionsCodes[0].'' , 'price'  => ''.$configBundleOptionsCodes[1].'');
						  //Create and send the JSON structure instead of the file  name
					  $tempSampleFile = '[{"file": "'.$configBundleOptionsCodes[5].'", "status": "new"}]';
					  $downloadableitems['link'][$downloadableitemsoptionscount]['sample'] = array('file' => ''.$tempSampleFile.'', 'type' => 'file', 'url'  => '');
					  //$downloadableitems['link'][$downloadableitemsoptionscount]['sample'] = array('file' => ''.$configBundleOptionsCodes[5].'', 'type' => 'file', 'url'  => '');
						} else {
						
						$downloadableitems['link'][$downloadableitemsoptionscount]['sample'] = array('file' => '[]', 'type' => 'url', 'url'  => ''.$configBundleOptionsCodes[5].'');
						}
					} else {
						$downloadableitems['link'][$downloadableitemsoptionscount]['sample'] = '';
					}
					$downloadableitems['link'][$downloadableitemsoptionscount]['file'] = '';
					$downloadableitems['link'][$downloadableitemsoptionscount]['type'] = $configBundleOptionsCodes[3];
					#$downloadableitems['link'][$downloadableitemsoptionscount]['link_url'] = $configBundleOptionsCodes[4];
					if($configBundleOptionsCodes[3] == "file") {
						#$filearrayforimport = array('file'  => 'media/import/mypdf.pdf' , 'name'  => 'asdad.txt', 'size'  => '316', 'status'  => 'old');
						#$document_directory =  Mage :: getBaseDir( 'media' ) . DS . 'import' . DS;
						#echo "DIRECTORY: " . $document_directory;
						#$filearrayforimport = '[{"file": "/home/discou33/public_html/media/import/mypdf.pdf", "name": "mypdf.pdf", "status": "new"}]';
						#$filearrayforimport = '[{"file": "mypdf.pdf", "name": "quickstart.pdf", "size": 324075, "status": "new"}]';
						#echo "FILE: " . $configBundleOptionsCodes[4];
						$filearrayforimport[] = array('file'  => ''.$configBundleOptionsCodes[4].'' , 'name'  => ''.$configBundleOptionsCodes[0].'' , 'price'  => ''.$configBundleOptionsCodes[1].'');
						
						if(isset($configBundleOptionsCodes[5])) {
							if($configBundleOptionsCodes[5] == 0) {
											$linkspurchasedstatus = 0;
											$linkspurchasedstatustext = false;
							} else {
											$linkspurchasedstatus = 1;
											$linkspurchasedstatustext = true;
							}
							$product->setLinksPurchasedSeparately($linkspurchasedstatus);
							$product->setLinksPurchasedSeparately($linkspurchasedstatustext);
						}
						
						
						#$product->setLinksPurchasedSeparately(0);
						#$product->setLinksPurchasedSeparately(false);
					
						#$files = Zend_Json::decode($filearrayforimport);
						#$files = "mypdf.pdf";
						
						#$downloadableitems['link'][$downloadableitemsoptionscount]['file'] = $filearrayforimport;
					} else if($configBundleOptionsCodes[3] == "url") {
						$downloadableitems['link'][$downloadableitemsoptionscount]['link_url'] = $configBundleOptionsCodes[4];
					}
					$downloadableitems['link'][$downloadableitemsoptionscount]['sort_order'] = 0;
			    $product->setDownloadableData($downloadableitems);
					$downloadableitemsoptionscount+=1;
				}
			
			}
		}
		//THIS IS FOR BUNDLE PRODUCTS
		if ($importData['type'] == 'bundle') {
			if ($new) {
				$optionscount=0;
				$items = array();
				//THIS IS FOR BUNDLE OPTIONS
				$commadelimiteddata = explode('|',$importData['bundle_options']);
				foreach ($commadelimiteddata as $data) {
					$configBundleOptionsCodes = $this->userCSVDataAsArray($data);
					$titlebundleselection = ucfirst(str_replace('_',' ',$configBundleOptionsCodes[0]));
					$items[$optionscount]['title'] = $titlebundleselection;
					$items[$optionscount]['type'] = $configBundleOptionsCodes[1];
					$items[$optionscount]['required'] = $configBundleOptionsCodes[2];
					$items[$optionscount]['position'] = $configBundleOptionsCodes[3];
					$items[$optionscount]['delete'] = 0;
					$optionscount+=1;
					
					
					if ($items) {
							$product->setBundleOptionsData($items);
					}
					$options_id = $product->getOptionId();
					$selections = array();
					$bundleConfigData = array();
					$optionscountselection=0;
					//THIS IS FOR BUNDLE SELECTIONS
					$commadelimiteddataselections = explode('|',$importData['bundle_selections']);
					foreach ($commadelimiteddataselections as $selection) {
						$configBundleSelectionCodes = $this->userCSVDataAsArray($selection);
						$selectionscount=0;
						foreach ($configBundleSelectionCodes as $selectionItem) {
							$bundleConfigData = explode(':',$selectionItem);
							$selections[$optionscountselection][$selectionscount]['option_id'] = $options_id;
							$selections[$optionscountselection][$selectionscount]['product_id'] = $product->getIdBySku($bundleConfigData[0]);
							$selections[$optionscountselection][$selectionscount]['selection_price_type'] = $bundleConfigData[1];
              $selections[$optionscountselection][$selectionscount]['selection_price_value'] = $bundleConfigData[2];
							$selections[$optionscountselection][$selectionscount]['is_default'] = $bundleConfigData[3];
							if(isset($bundleConfigData) && isset($bundleConfigData[4]) && $bundleConfigData[4] != '') {
							$selections[$optionscountselection][$selectionscount]['selection_qty'] = $bundleConfigData[4];
							$selections[$optionscountselection][$selectionscount]['selection_can_change_qty'] = $bundleConfigData[5];
							}
							if(isset($bundleConfigData) && isset($bundleConfigData[6]) && $bundleConfigData[6] != '') {
							$selections[$optionscountselection][$selectionscount]['position'] = $bundleConfigData[6];
							}
							$selections[$optionscountselection][$selectionscount]['delete'] = 0;
							$selectionscount+=1;
						}
						$optionscountselection+=1;
					}
					if ($selections) {
							$product->setBundleSelectionsData($selections);
					}
				
				}
				

				if ($product->getPriceType() == '0') {
					$product->setCanSaveCustomOptions(true);
					if ($customOptions = $product->getProductOptions()) {
						foreach ($customOptions as $key => $customOption) {
							$customOptions[$key]['is_delete'] = 1;
						}
						$product->setProductOptions($customOptions);
					}
				}
			
				$product->setCanSaveBundleSelections();
			} 
		}
		if ( isset( $importData['related'] ) ) {
			$pos = strpos($importData['related'], ":");
			if ($pos !== false) {
				$linkIds = $this -> skusToIdswithPosition( $importData['related'], $product );
			} else {
				$linkIds = $this -> skusToIds( $importData['related'], $product );
			}
			if ( !empty( $linkIds ) ) {
				$product -> setRelatedLinkData( $linkIds );
			} 
		} 

		if ( isset( $importData['upsell'] ) ) {
			$pos = strpos($importData['upsell'], ":");
			if ($pos !== false) {
				$linkIds = $this -> skusToIdswithPosition( $importData['upsell'], $product );
			} else {
				$linkIds = $this -> skusToIds( $importData['upsell'], $product );
			}
			if ( !empty( $linkIds ) ) {
				$product -> setUpSellLinkData( $linkIds );
			} 
		} 

		if ( isset( $importData['crosssell'] ) ) {
			$pos = strpos($importData['crosssell'], ":");
			if ($pos !== false) {
				$linkIds = $this -> skusToIdswithPosition( $importData['crosssell'], $product );
			} else {
				$linkIds = $this -> skusToIds( $importData['crosssell'], $product );
			}
			if ( !empty( $linkIds ) ) {
				$product -> setCrossSellLinkData( $linkIds );
			} 
		} 
		/*
		if ( isset( $importData['grouped'] ) ) {
			$linkIds = $this -> skusToIds( $importData['grouped'], $product );
			if ( !empty( $linkIds ) ) {
				$product -> setGroupedLinkData( $linkIds );
			} 
		} 
		*/
		/* MODDED TO ALLOW FOR GROUP POSITION AS WELL AND SHOULD WORK IF NO POSITION IS SET AS WELL CAN COMBO */
		if ( isset( $importData['grouped'] ) && $importData['grouped'] != "" ) {
			
			$finalIDssthatneedtobeconvertedto=array();
			$finalskusthatneedtobeconvertedtoID="";
			$groupedpositioncounter=0;
			$finalskusforarraytoexplode = explode(",",$importData['grouped']);
			foreach($finalskusforarraytoexplode as $productskuexploded)
			{
					$pos = strpos($productskuexploded, ":");
					if ($pos !== false) {
					//if( isset($finalidsforarraytoexplode[1]) ) {	
						$finalidsforarraytoexplode = explode(":",$productskuexploded);
						$finalIDssthatneedtobeconvertedto[$groupedpositioncounter]['position'] = $finalidsforarraytoexplode[0];
						$finalIDssthatneedtobeconvertedto[$groupedpositioncounter]['sku'] = $finalidsforarraytoexplode[1];
						if ($finalidsforarraytoexplode[2]) {
						$finalIDssthatneedtobeconvertedto[$groupedpositioncounter]['qty'] = $finalidsforarraytoexplode[2];
						}
						$finalskusthatneedtobeconvertedtoID .= $finalidsforarraytoexplode[1] . ",";
					} else {
						$finalskusthatneedtobeconvertedtoID .= $productskuexploded . ",";
					}	
					$groupedpositioncounter++;
			}		
			$linkIds = $this -> skusToIds( $finalskusthatneedtobeconvertedtoID, $product );
			if ( !empty( $linkIds ) ) {
				$product -> setGroupedLinkData( $linkIds );
			} 
		} 
		if ( isset( $importData['category_ids'] ) ) {
			$product -> setCategoryIds( $importData['category_ids'] );
		} 


		if( isset($importData['tier_prices']) && !empty($importData['tier_prices']) ) {
			$this->_editTierPrices($product, $importData['tier_prices'], $store);
		}

		
		if ( isset( $importData['categories'] ) && $importData['categories'] !="" ) {
			if (!empty($importData['store'])) {
				$cat_store = $this -> _stores[$importData['store']];
			} else {
				$message = Mage :: helper( 'catalog' ) -> __( 'Skip import row, required field "store" for new products not defined', $field );
				Mage :: throwException( $message );
			} 
			$categoryIds = $this -> _addCategories( $importData['categories'], $cat_store );
			if ( $categoryIds ) {
				$product -> setCategoryIds( $categoryIds );
			} 
		} 
		
		foreach ( $this -> _ignoreFields as $field ) {
			if ( isset( $importData[$field] ) ) {
				unset( $importData[$field] );
			} 
		} 
		
		if ($store->getId() != 0) {
            $websiteIds = $product->getWebsiteIds();
            if (!is_array($websiteIds)) {
                $websiteIds = array();
            }
            if (!in_array($store->getWebsiteId(), $websiteIds)) {
                $websiteIds[] = $store->getWebsiteId();
            }
            $product->setWebsiteIds($websiteIds);
		}
		
		if ( isset( $importData['websites'] ) ) {
			$websiteIds = $product -> getWebsiteIds();
			if ( !is_array( $websiteIds ) ) {
				$websiteIds = array();
			} 
			$websiteCodes = explode( ',', $importData['websites'] );
			foreach ( $websiteCodes as $websiteCode ) {
				try {
					$website = Mage :: app() -> getWebsite( trim( $websiteCode ) );
					if ( !in_array( $website -> getId(), $websiteIds ) ) {
						$websiteIds[] = $website -> getId();
					} 
				} 
				catch ( Exception $e ) {
				} 
			} 
			$product -> setWebsiteIds( $websiteIds );
			unset( $websiteIds );
		} 
		
		$custom_options = array();
		$author_data = array();
        $author = false;
        
        if (!empty($importData['author_email'])) {
			$author_id = Mage::getModel('publish/author')->getAuthorByEmail(trim($importData['author_email']));
				if($author_id !== false) {
					$author = Mage::getModel('publish/author')->load($author_id);
				}
		
			$author = Mage::getModel('publish/author');
		}

		foreach ( $importData as $field => $value ) {
			if ( in_array( $field, $this -> _imageFields ) ) {
				continue;
			} 
			
			$attribute = $this -> getAttribute( $field );
			if ( !$attribute ) {
			/* Author Import Code */
			if($author !== false) {
				if((strpos($field,'author_') !==FALSE) && strlen($value)) {
					$authorField = substr($field,7);
					$author_data[$authorField] = $value;
				}
			}
			/* CUSTOM OPTION CODE */
			if(strpos($field,':')!==FALSE && strlen($value)) {
			   $values=explode('|',$value);
			   if(count($values)>0) {
					$iscustomoptions = "true";
					
					foreach($values as $v) {
						$parts = explode(':',$v);
						$title = $parts[0];
					}
				  //RANDOM ISSUE HERE SOMETIMES WITH TITLES OF LAST ITEM IN DROPDOWN SHOWING AS TITLE MIGHT NEED TO SEPERATE TITLE variables
				  @list($title,$type,$is_required,$sort_order) = explode(':',$field);
				  $title2 = ucfirst(str_replace('_',' ',$title));
				  $custom_options[] = array(
					 'is_delete'=>0,
					 'title'=>$title2,
					 'previous_group'=>'',
					 'previous_type'=>'',
					 'type'=>$type,
					 'is_require'=>$is_required,
					 'sort_order'=>$sort_order,
					 'values'=>array()
				  );
				  if($is_required ==1) {
						$iscustomoptionsrequired = "true";
				  }
				  foreach($values as $v) {
					 $parts = explode(':',$v);
					 $title = $parts[0];
					 if(count($parts)>1) {
						$price_type = $parts[1];
					 } else {
						$price_type = 'fixed';
					 }
					 if(count($parts)>2) {
						$price = $parts[2];
					 } else {
						$price =0;
					 }
					 if(count($parts)>3) {
						$sku = $parts[3];
					 } else {
						$sku='';
					 }
					 if(count($parts)>4) {
						$sort_order = $parts[4];
					 } else {
						$sort_order = 0;
					 }
					 if(count($parts)>5) {
						$max_characters = $parts[5];
					 } else {
						$max_characters = '';
					 }
					 if(count($parts)>6) {
						$file_extension = $parts[6];
					 } else {
						$file_extension = '';
					 }
					 if(count($parts)>7) {
						$image_size_x = $parts[7];
					 } else {
						$image_size_x = '';
					 }
					 if(count($parts)>8) {
						$image_size_y = $parts[8];
					 } else {
						$image_size_y = '';
					 }
					 switch($type) {
						case 'file':
						   /* TODO */
						   $custom_options[count($custom_options) - 1]['price_type'] = $price_type;
						   $custom_options[count($custom_options) - 1]['price'] = $price;
						   $custom_options[count($custom_options) - 1]['sku'] = $sku;
						   $custom_options[count($custom_options) - 1]['file_extension'] = $file_extension;
						   $custom_options[count($custom_options) - 1]['image_size_x'] = $image_size_x;
						   $custom_options[count($custom_options) - 1]['image_size_y'] = $image_size_y;
						   break;
						   
						case 'field':
						   $custom_options[count($custom_options) - 1]['max_characters'] = $max_characters;
						case 'area':
						   $custom_options[count($custom_options) - 1]['max_characters'] = $max_characters;
						   /* NO BREAK */
						   
						case 'date':
						case 'date_time':
						case 'time':
						   $custom_options[count($custom_options) - 1]['price_type'] = $price_type;
						   $custom_options[count($custom_options) - 1]['price'] = $price;
						   $custom_options[count($custom_options) - 1]['sku'] = $sku;
						   break;
													  
						case 'drop_down':
						case 'radio':
						case 'checkbox':
						case 'multiple':
						default:
						   $custom_options[count($custom_options) - 1]['values'][]=array(
							  'is_delete'=>0,
							  'title'=>$title,
							  'option_type_id'=>-1,
							  'price_type'=>$price_type,
							  'price'=>$price,
							  'sku'=>$sku,
							  'sort_order'=>$sort_order,
							  'max_characters'=>$max_characters,
						   );
							   break;
						 }
					  }
				   }
				}
				/* END CUSTOM OPTION CODE */
				continue;
			} 

			$isArray = false;
			$setValue = $value;
			
			if ( $attribute -> getFrontendInput() == 'multiselect' ) {
				$value = explode( self :: MULTI_DELIMITER, $value );
				foreach($value as $key => $kvalue) {
					$value[$key] = trim($kvalue);
				}
				$isArray = true;
				$setValue = array();
			} 
			
			if ( $value && $attribute -> getBackendType() == 'decimal' ) {
				$setValue = $this -> getNumber( $value );
			} 
			
			if ( $attribute -> usesSource() ) {
				$options = $attribute -> getSource() -> getAllOptions( false );

                if ($isArray) {
                    foreach ($options as $item) {
                        if (in_array($item['label'], $value)) {
                            $setValue[] = $item['value'];
                            $foundKey = array_search($item['label'],$value);
                            unset($value[$foundKey]);
                        }
                    }
                    if(count($value)) {
						$new_values = $this->addAttributeOptions($field, $value);
						array_merge($setValue,$new_values);
					}
                } else {
                    $setValue = false;
                    foreach ($options as $item) {
                        if (is_array($item['value'])) {
                            foreach ($item['value'] as $subValue) {
                                if (isset($subValue['value']) && $subValue['value'] == $value) {
                                    $setValue = $value;
                                }
                            }
                        } else if ($item['label'] == $value) {
                            $setValue = $item['value'];
                        }
                    }
                    if(!$setValue) {
						$setValue = $this->addAttributeOptions($field, array($value));
					}
                }
			} 
			
			if($new || $this->getBatchParams('reimport_images') == "true") {
				$product -> setData( $field, $setValue );
			} else {
				if (!in_array($field, array('image', 'thumbnail', 'small_image', 'gallery'))) {
					$product->setData($field, $setValue);
				}
			}
		} 
		
		if(($author !== false) && (count($author_data) > 1)) {
			if((isset($author_id)) AND ($author_id !== false)) {
				$author_data['entity_id'] = $author_id;
			}
			$author->setData($author_data);
			$author->save();
			$product->setPublishAuthor($author->getId());
		}
		
		if ( !$product -> getVisibility() ) {
			$product -> setVisibility( Mage_Catalog_Model_Product_Visibility :: VISIBILITY_NOT_VISIBLE );
		} 
		
		$stockData = array();
		$inventoryFields = isset($this->_inventoryFieldsProductTypes[$product->getTypeId()])
			? $this->_inventoryFieldsProductTypes[$product->getTypeId()]
			: array(); 
			
		foreach ( $inventoryFields as $field ) {
			if ( isset( $importData[$field] ) ) {
				if ( in_array( $field, $this -> _toNumber ) ) {
					$stockData[$field] = $this -> getNumber( $importData[$field] );
				} 
				else {
					$stockData[$field] = $importData[$field];
				} 
			} 
		} 
		$product->setStockData( $stockData );
		
		
		
		
		if($new || $this->getBatchParams('reimport_images') == "true") {  //starts CHECK FOR IF REIMPORTING IMAGES TO PRODUCTS IS TRUE
			//this is a check if we want to delete all images before import of images from csv
			if($this->getBatchParams('deleteall_andreimport_images') == "true" && $importData["image"] != "" && $importData["small_image"] != "" && $importData["thumbnail"] != "") {
			$attributes = $product->getTypeInstance()->getSetAttributes();                     
				if (isset($attributes['media_gallery'])) {
					$gallery = $attributes['media_gallery'];
					//Get the images
					$galleryData = $product->getMediaGallery();
					if(!empty($galleryData)) {                                                                    
					foreach($galleryData['images'] as $image){
						if ($gallery->getBackend()->getImage($product, $image['file'])) {
							$gallery->getBackend()->removeImage($product, $image['file']);
							if ( file_exists( $image['file'] ) ) {
								$ext = substr(strrchr($image['file'], '.'), 1);
								if( strlen( $ext ) == 4 ) {
									unlink (Mage::getBaseDir('media') . DS . 'catalog' . DS . 'product' . $image['file']);
								}
							}
						}
					}      
				}                                                       
			}
		}		
		if($importData["image"] != "" || $importData["small_image"] != "" || $importData["thumbnail"] != "" ) {		
			$mediaGalleryBackendModel = $this->getAttribute('media_gallery')->getBackend();

			$arrayToMassAdd = array();
	
			foreach ($product->getMediaAttributes() as $mediaAttributeCode => $mediaAttribute) {
				if (isset($importData[$mediaAttributeCode])) {
					$file = $importData[$mediaAttributeCode];
					if(file_exists(Mage :: getBaseDir( 'media' ) . DS . 'import' . $file)){
						if (trim($file) && !$mediaGalleryBackendModel->getImage($product, $file)) {
							$arrayToMassAdd[] = array('file' => trim($file), 'mediaAttribute' => $mediaAttributeCode);
						}
					}
				}
			}
			
			if($this->getBatchParams('exclude_images') == "true") {
				#$product -> addImageToMediaGallery( Mage :: getBaseDir( 'media' ) . DS . 'import/' . $file, $fields, false );
				$addedFilesCorrespondence = $mediaGalleryBackendModel->addImagesWithDifferentMediaAttributes($product, $arrayToMassAdd, Mage::getBaseDir('media') . DS . 'import', false);
			} else {
				#$product -> addImageToMediaGallery( Mage :: getBaseDir( 'media' ) . DS . 'import/' . $file, $fields, false, false );
				$addedFilesCorrespondence = $mediaGalleryBackendModel->addImagesWithDifferentMediaAttributes($product, $arrayToMassAdd, Mage::getBaseDir('media') . DS . 'import', false, false);
			}
	
			foreach ($product->getMediaAttributes() as $mediaAttributeCode => $mediaAttribute) {
				$addedFile = '';
				if (isset($importData[$mediaAttributeCode . '_label'])) {
					$fileLabel = trim($importData[$mediaAttributeCode . '_label']);
					if (isset($importData[$mediaAttributeCode])) {
						$keyInAddedFile = array_search($importData[$mediaAttributeCode],
							$addedFilesCorrespondence['alreadyAddedFiles']);
						if ($keyInAddedFile !== false) {
							$addedFile = $addedFilesCorrespondence['alreadyAddedFilesNames'][$keyInAddedFile];
						}
					}
	
					if (!$addedFile) {
						$addedFile = $product->getData($mediaAttributeCode);
					}
					if ($fileLabel && $addedFile) {
						$mediaGalleryBackendModel->updateImage($product, $addedFile, array('label' => $fileLabel));
					}
				}
			}		
									
		} //end check on empty values
			
			if ( !empty( $importData['gallery'] ) ) {
				$galleryData = explode( ',', $importData["gallery"] );
				foreach( $galleryData as $gallery_img ) {
					try {
						if($this->getBatchParams('exclude_gallery_images') == "true") {
						  $product -> addImageToMediaGallery( Mage :: getBaseDir( 'media' ) . DS . 'import' . $gallery_img, null, false, true );
						} else {
						  $product -> addImageToMediaGallery( Mage :: getBaseDir( 'media' ) . DS . 'import' . $gallery_img, null, false, false );
						}
					} 
					catch ( Exception $e ) {
						Mage::log(sprintf('failed to import gallery images: %s', $e->getMessage()), null,'ce_product_import_export_errors.log');
					} 
				} 
			} 
		}
		$product -> setIsMassupdate( true );
		$product -> setExcludeUrlRewrite( true );
		//PATCH FOR Fatal error: Call to a member function getStoreId() on a non-object in D:\web\magento\app\code\core\Mage\Bundle\Model\Selection.php on line 52
		if (!Mage::registry('product')) {
			Mage::register('product', Mage::getModel('catalog/product')->setStoreId(0));
			//Mage::register('product', $product); maybe this is needed for when importing multi-store bundle vs above
		}
		$product -> save();
		
		// Store affected products ids
        $this->_addAffectedEntityIds($product->getId());
		
		if ( isset( $importData['product_tags'] ) && $importData['product_tags'] !="" ) {
		
			#$configProductTags = $this->userCSVDataAsArray($importData['product_tags']);
			$configProductTags = explode(',', $importData['product_tags']);
			
			#foreach ($commadelimiteddata as $dataseperated) {
			foreach ($configProductTags as $tagName) {
				try {
				$commadelimiteddata = explode(':',$tagName);
				
				$tagName = $commadelimiteddata[1];
				$tagModel = Mage::getModel('tag/tag');
				$result = $tagModel->loadByName($tagName);
				$tagModel->setName($tagName)
									->setStoreId($importData['store'])
									->setStatus($tagModel->getApprovedStatus())
									->save();
									
				$tagRelationModel = Mage::getModel('tag/tag_relation');
				/*$tagRelationModel->loadByTagCustomer($product -> getIdBySku( $importData['sku'] ), $tagModel->getId(), '13194', Mage::app()->getStore()->getId());*/
				if($importData['customerID'] != "NULL") {
					$tagRelationModel->setTagId($tagModel->getId())
							->setCustomerId(trim($commadelimiteddata[0]))
							->setProductId($product -> getIdBySku( $importData['sku'] ))
							->setStoreId($importData['store'])
							->setCreatedAt( now() )
							->setActive(1)
							->save();
				} else {
					 $data['tag_id']             = $tagModel->getId();
					 $data['name']               = trim($tagName);
				     $data['status']             = $tagModel->getApprovedStatus();
					 $data['first_customer_id']  = "0";
					 $data['first_store_id'] 	 = "0";
					 $data['visible_in_store_ids'] = array();
					 $data['store_id'] 			 = "1";
					 $data['base_popularity']    = (isset($importData['base_popularity'])) ? $importData['base_popularity'] : 0;
					 $data['store']              = $importData['store'];
					 $tagModel2 = Mage::getModel('tag/tag');
					 $tagModel2->addData($data);
					 $productIds[] = $product->getIdBySku($importData['sku']);
                     $tagRelationModel2 = Mage::getModel('tag/tag_relation');
               		 $tagRelationModel2->addRelations($tagModel2, $productIds);
					 $tagModel2->save();
					 $tagModel2->aggregate();
				}
				$tagModel->aggregate();
				}
				catch ( Exception $e ) {
					Mage::log(sprintf('failed to import product tags: %s', $e->getMessage()), null,'ce_product_import_export_errors.log');
				}		
			}
		}
		
		/* Add the custom options specified in the CSV import file */
		if(count($custom_options)) {

			/* Remove existing custom options attached to the product */
			foreach ($product->getOptions() as $o) {
			   $o->getValueInstance()->deleteValue($o->getId());
			   $o->deletePrices($o->getId());
			   $o->deleteTitles($o->getId());
			   $o->delete();
			}
		   foreach($custom_options as $option) {
			  try {
				$opt = Mage::getModel('catalog/product_option');
				$opt->setProduct($product);
				$opt->addOption($option);
				$opt->saveOptions();
			  }
			  catch (Exception $e) {
				Mage::log(sprintf('failed to import custom options: %s', $e->getMessage()), null,'ce_product_import_export_errors.log');
			  }
		   }
		}

		if($iscustomoptions == "true") {
			if($productId == "") {
				$productId = $product->getId();
			}
			if($currentproducttype == "simple") {
				$prefix = Mage::getConfig()->getNode('global/resources/db/table_prefix'); 
				$fixOptions = Mage::getSingleton('core/resource')->getConnection('core_write'); 
				if($iscustomoptionsrequired == "true") {
					$fixOptions->query("UPDATE ".$prefix."catalog_product_entity SET required_options = 1 WHERE has_options = 1 AND type_id = 'simple' AND entity_id = '".$productId."'"); 
				} else {
					$fixOptions->query("UPDATE ".$prefix."catalog_product_entity SET has_options = 1 WHERE has_options = 0 AND type_id = 'simple' AND entity_id = '".$productId."'"); 
				}
			} else if ($currentproducttype == "configurable") {
				$prefix = Mage::getConfig()->getNode('global/resources/db/table_prefix'); 
				$fixOptions = Mage::getSingleton('core/resource')->getConnection('core_write'); 
				
				if($iscustomoptionsrequired == "true") {
					$fixOptions->query("UPDATE ".$prefix."catalog_product_entity SET required_options = 1 WHERE has_options = 1 AND type_id = 'configurable' AND entity_id = '".$productId."'"); 
				
				} else {
					$fixOptions->query("UPDATE ".$prefix."catalog_product_entity SET has_options = 1 WHERE has_options = 0 AND type_id = 'configurable' AND entity_id = '".$productId."'"); 
				}
			}
		}

		/* DOWNLOADBLE PRODUCT FILE METHOD START */
		if(isset($filearrayforimport)) {
			$filecounterinternall=1;
			foreach($filearrayforimport as $fileinfo) {
				$document_directory = Mage :: getBaseDir( 'media' ) . DS . 'import' . DS;
				$files = $fileinfo['file'];
				$resource = Mage::getSingleton('core/resource');
				$prefix = Mage::getConfig()->getNode('global/resources/db/table_prefix'); 
				$write = $resource->getConnection('core_write');
				$read = $resource->getConnection('core_read');
				$select_qry =$read->query("SHOW TABLE STATUS LIKE '".$prefix."downloadable_link' ");
				$row = $select_qry->fetch();
				$next_id = $row['Auto_increment'];
			
				$okvalueformodelID = $next_id - $filecounterinternall;
				$linkModel = Mage::getModel('downloadable/link')->load($okvalueformodelID);
													
				$link_file = 	$document_directory . $files;	
					
				$file = realpath($link_file);
		
				if (!$file || !file_exists($file)) {
					Mage::throwException(Mage::helper('catalog')->__('Link  file '.$file.' not exists'));
				}
				$pathinfo = pathinfo($file);
       
				$linkfile       = Varien_File_Uploader::getCorrectFileName($pathinfo['basename']);
				$dispretionPath = Varien_File_Uploader::getDispretionPath($linkfile);
				$linkfile       = $dispretionPath . DS . $linkfile;

				$linkfile = $dispretionPath . DS
						  . Varien_File_Uploader::getNewFileName(Mage_Downloadable_Model_Link::getBasePath().DS.$linkfile);

				$ioAdapter = new Varien_Io_File();
				$ioAdapter->setAllowCreateFolders(true);
				$distanationDirectory = dirname(Mage_Downloadable_Model_Link::getBasePath().DS.$linkfile);

				try {
					$ioAdapter->open(array(
						'path'=>$distanationDirectory
					));

						$ioAdapter->cp($file, Mage_Downloadable_Model_Link::getBasePath().DS.$linkfile);
						$ioAdapter->chmod(Mage_Downloadable_Model_Link::getBasePath().DS.$linkfile, 0777);
				
				}
				catch (Exception $e) {
					Mage::throwException(Mage::helper('catalog')->__('Failed to move file: %s', $e->getMessage()));
					Mage::log(sprintf('failed to move file: %s', $e->getMessage()), null,'ce_product_import_export_errors.log');
				}

				$linkfile = str_replace(DS, '/', $linkfile);
		
				$linkModel->setLinkFile($linkfile);
				$linkModel->save();

				$intesdf = $next_id - $filecounterinternall;
				$write->query("UPDATE `".$prefix."downloadable_link_title` SET title = '".$fileinfo['name']."' WHERE link_id = '".$intesdf."'");
				$write->query("UPDATE `".$prefix."downloadable_link_price` SET price = '".$fileinfo['price']."' WHERE link_id = '".$intesdf."'");
				$filecounterinternall++;
			}
		}
		/* END DOWNLOADBLE METHOD */

		/* SAMPLE FILE DOWNLOADBLE PRODUCT SAMPLE FILE METHOD START */
		if(count($filenameforsamplearrayforimport)) {
			$filecounterinternall=1;
			foreach($filenameforsamplearrayforimport as $fileinfo) {
				$document_directory = Mage :: getBaseDir( 'media' ) . DS . 'import';
				$samplefiles = $fileinfo['file'];
				#print_r($filenameforsamplearrayforimport);
				#echo "ID: " . $fileinfo['name'] ."<br/>";
				$resource = Mage::getSingleton('core/resource');
				$prefix = Mage::getConfig()->getNode('global/resources/db/table_prefix'); 
				$write = $resource->getConnection('core_write');
				$read = $resource->getConnection('core_read');
				$select_qry =$read->query("SHOW TABLE STATUS LIKE '".$prefix."downloadable_link' ");
				$row = $select_qry->fetch();
				$next_id = $row['Auto_increment'];
					
				$okvalueformodelID = $next_id - $filecounterinternall;
				#echo "next_id: " . $okvalueformodelID."<br/>";
				$linkSampleModel = Mage::getModel('downloadable/link')->load($okvalueformodelID);
				$link_sample_file = 	$document_directory . $samplefiles;
							
				$file = realpath($link_sample_file);

				if (!$file || !file_exists($file)) {
					Mage::throwException(Mage::helper('catalog')->__('Link sample file '.$file.' not exists'));
					Mage::log(sprintf('downloadable product sample file '.$file.' link does not exist: %s', $e->getMessage()), null,'ce_product_import_export_errors.log');
				}
				$pathinfo = pathinfo($file);
				$linksamplefile = Varien_File_Uploader::getCorrectFileName($pathinfo['basename']);
				$dispretionPath = Varien_File_Uploader::getDispretionPath($linksamplefile);
				$linksamplefile = $dispretionPath . DS . $linksamplefile;
				$linksamplefile = $dispretionPath . DS . Varien_File_Uploader::getNewFileName(Mage_Downloadable_Model_Link::getBaseSamplePath().DS.$linksamplefile);

				$ioAdapter = new Varien_Io_File();
				$ioAdapter->setAllowCreateFolders(true);
				$distanationDirectory = dirname(Mage_Downloadable_Model_Link::getBaseSamplePath().DS.$linksamplefile);

				try {
					$ioAdapter->open(array(
						'path'=>$distanationDirectory
					));

						$ioAdapter->cp($file, Mage_Downloadable_Model_Link::getBaseSamplePath().$linksamplefile);
						$ioAdapter->chmod(Mage_Downloadable_Model_Link::getBaseSamplePath().$linksamplefile, 0777);

				}
				catch (Exception $e) {
					Mage::throwException(Mage::helper('catalog')->__('Failed to move sample file: %s', $e->getMessage()));
					Mage::log(sprintf('Failed to move sample file: %s', $e->getMessage()), null,'ce_product_import_export_errors.log');
				}

				$linksamplefile = str_replace(DS, '/', $linksamplefile);
				$linkSampleModel->setSampleFile($linksamplefile);
				$linkSampleModel->save();

				#$intesdf = $next_id-1;
				$intesdf = $next_id - $filecounterinternall;
				$write->query("UPDATE `".$prefix."downloadable_link_title` SET title = '".$fileinfo['name']."' WHERE link_id = '".$intesdf."'");
				$write->query("UPDATE `".$prefix."downloadable_link_price` SET price = '".$fileinfo['price']."' WHERE link_id = '".$intesdf."'");
				$filecounterinternall++;
			}
		}
		/* END SAMPLE FILE DOWNLOADBLE METHOD */	
		/* START OF SUPER ATTRIBUTE PRICING */


		if ($finalsuperattributetype == 'configurable') {
			if ($finalsuperattributepricing != "") {

				$adapter = Mage::getSingleton('core/resource')->getConnection('core_write');
				$read = Mage::getSingleton('core/resource')->getConnection('core_read');
				$superProduct = Mage :: getModel ( 'catalog/product' )-> load ( $product -> getId ()); 
				$superArray = $superProduct -> getTypeInstance ()-> getConfigurableAttributesAsArray (); 
					
				$SuperAttributePricingData = array();
				$FinalSuperAttributeData = array();
				$SuperAttributePricingData = explode('|',$finalsuperattributepricing);
				
				$prefix = Mage::getConfig()->getNode('global/resources/db/table_prefix'); 
				foreach( $superArray AS $key => $val ) { 
					#$x = 0 ; 
					foreach( $val[ 'values' ] AS $keyValues => $valValues ) { 
			
						foreach($SuperAttributePricingData as $singleattributeData) {
							$FinalSuperAttributeData = explode(':',$singleattributeData);
							
							if($FinalSuperAttributeData[0] == $superArray[$key][ 'values' ][$keyValues][ 'label' ]) {
								if($new) {
									$insertPrice='INSERT into '.$prefix.'catalog_product_super_attribute_pricing (product_super_attribute_id, value_index, is_percent, pricing_value) VALUES
											 ("'.$superArray[$key][ 'values' ][$keyValues][ 'product_super_attribute_id' ].'", "'.$superArray[$key][ 'values' ][$keyValues][ 'value_index' ].'", "'.$FinalSuperAttributeData[2].'", "'.$FinalSuperAttributeData[1].'");';
									#echo "SQL2: " . $insertPrice;
									$adapter->query($insertPrice);
								} else {
									if($FinalSuperAttributeData[1] != "") {
										$finalpriceforupdate = $FinalSuperAttributeData[1];
										  
										$select_qry2 = $read->query("SELECT value_id FROM ".$prefix."catalog_product_super_attribute_pricing WHERE product_super_attribute_id = '".$superArray[$key][ 'values' ][$keyValues][ 'product_super_attribute_id' ]."' AND value_index = '".$superArray[$key][ 'values' ][$keyValues][ 'value_index' ]."'");
										$newrowItemId2 = $select_qry2->fetch();
										$db_product_id = $newrowItemId2['value_id'];
										if($db_product_id == "") {
											$insertPrice='INSERT into '.$prefix.'catalog_product_super_attribute_pricing (product_super_attribute_id, value_index, is_percent, pricing_value) VALUES
													 ("'.$superArray[$key][ 'values' ][$keyValues][ 'product_super_attribute_id' ].'", "'.$superArray[$key][ 'values' ][$keyValues][ 'value_index' ].'", "'.$FinalSuperAttributeData[2].'", "'.$FinalSuperAttributeData[1].'");';
											#echo "SQL2: " . $insertPrice;
											$adapter->query($insertPrice);
										
										} else {
											$updatePrice="UPDATE ".$prefix."catalog_product_super_attribute_pricing SET pricing_value = '".$finalpriceforupdate."' WHERE value_id = '".$db_product_id."'";
											#echo "SQL UPDATE: " . $updatePrice;
											$adapter->query($updatePrice);
										}
									}
								}
							}
						}
					}
				}
			}
		}			
		/* END OF SUPER ATTRIBUTE PRICING */
		/* START OF GROUPED POSITION */
		if ($finalsuperattributetype == 'grouped') {
			foreach($finalIDssthatneedtobeconvertedto as $data_product_position_id)
			{
				 $resource = Mage::getSingleton('core/resource');
				 $prefix = Mage::getConfig()->getNode('global/resources/db/table_prefix'); 
				 $read = $resource->getConnection('core_read');
				 $write = $resource->getConnection('core_write');
				 $data_productID = ( int )$product -> getIdBySku( $data_product_position_id['sku'] );
				 #$data_productID = (int)$product->getId();
				 
				 $select_qry3 = $read->query("SELECT link_type_id FROM ".$prefix."catalog_product_link_type WHERE code = 'super'");
				 $newrowItemId3 = $select_qry3->fetch();
				 $link_type_id = $newrowItemId3['link_type_id'];
				 
				 $select_qry_catalog_product_link = "SELECT link_id FROM ".$prefix."catalog_product_link WHERE linked_product_id = '" . trim($data_productID) . "' AND link_type_id = '".$link_type_id."'";
				 
				 #echo "T: SELECT link_id FROM ".$prefix."catalog_product_link WHERE linked_product_id= '". trim($data_productID) . "'";
				 $catalog_product_link_rows = $read->fetchAll($select_qry_catalog_product_link);
				 foreach($catalog_product_link_rows as $data_catalog_product_link)
				 {
					$write->query("UPDATE ".$prefix."catalog_product_link_attribute_int SET value = '" . $data_product_position_id['position'] . "' WHERE link_id = '". $data_catalog_product_link['link_id'] . "'");
					 
					$select_qry2 = $read->query("SELECT value_id FROM ".$prefix."catalog_product_link_attribute_decimal WHERE link_id = '".$data_catalog_product_link['link_id']."'");
					
					$select_qry4 = $read->query("SELECT product_link_attribute_id FROM ".$prefix."catalog_product_link_attribute WHERE product_link_attribute_code = 'qty' AND link_type_id = '".$link_type_id."'");
					
					$newrowItemId2 = $select_qry2->fetch();
					$newrowItemId4 = $select_qry4->fetch();
					
					$db_product_id = $newrowItemId2['value_id'];
					$product_link_attribute_id = $newrowItemId4['product_link_attribute_id'];
					
					$adapter = Mage::getSingleton('core/resource')->getConnection('core_write');
					$read = Mage::getSingleton('core/resource')->getConnection('core_read');
					
					if(isset($data_product_position_id['qty'])) {	
						if($db_product_id == "") {
							$insertPrice='INSERT into '.$prefix.'catalog_product_link_attribute_decimal (product_link_attribute_id, link_id, value) VALUES
									 ("'.$product_link_attribute_id.'", "'.$data_catalog_product_link['link_id'].'", "'.$data_product_position_id['qty'].'");';
							#echo "SQL2: " . $insertPrice;
							$adapter->query($insertPrice);
				
						} else {
							$updatePrice="UPDATE ".$prefix."catalog_product_link_attribute_decimal SET value = '" . $data_product_position_id['qty'] . "' WHERE link_id = '". $data_catalog_product_link['link_id'] . "'";
							#echo "SQL UPDATE: " . $updatePrice;
							$adapter->query($updatePrice);
						}
					}
				}
			}
		}
		/* END OF GROUPED POSITION */
		/* START OF GROUPED PRICINGPRICING 1.7.x beta rc1 */
		if ($finalgroup_price_price != "") {
			$resource = Mage::getSingleton('core/resource');
			$adapter = Mage::getSingleton('core/resource')->getConnection('core_write');
			$read = Mage::getSingleton('core/resource')->getConnection('core_read');
			$prefix = Mage::getConfig()->getNode('global/resources/db/table_prefix'); 
			
			if($this->getBatchParams('append_group_prices') != "true") { 
             //delete existing group prices
             $select_qry2 = $read->query("DELETE FROM ".$resource->getTableName('catalog/product_attribute_group_price')." WHERE entity_id = '".$product->getId()."'");
			}
			$groupedProductModel = Mage::getModel('catalog/product')->load($product->getId()); 
			$group_price_priceData = explode('|',$finalgroup_price_price);
						
			foreach($group_price_priceData as $singleattributeData) {
				$FinalGroupedPriceData = explode('=',$singleattributeData);
				
				if($FinalGroupedPriceData[0] != "") {
					if($new) {
						#echo "SQL2: ";
						$insertPrice='INSERT into '.$resource->getTableName('catalog/product_attribute_group_price').' (entity_id,all_groups,customer_group_id,value,website_id) VALUES ("'.$product->getId().'","0","'.$FinalGroupedPriceData[0].'","'.$FinalGroupedPriceData[1].'","0");';
						$adapter->query($insertPrice);
						
					} else {
						#echo "SQL UPDATE: " . $updatePrice;
						$select_qry2 = $read->query("SELECT value_id FROM ".$resource->getTableName('catalog/product_attribute_group_price')." WHERE value = '".$FinalGroupedPriceData[1]."' AND customer_group_id = '".$FinalGroupedPriceData[0]."' AND entity_id = '".$product->getId()."'");
						$newrowItemId2 = $select_qry2->fetch();
						$db_product_id = $newrowItemId2['value_id'];
						if($db_product_id == "") {
							$insertPrice='INSERT into '.$resource->getTableName('catalog/product_attribute_group_price').' (entity_id,all_groups,customer_group_id,value,website_id) VALUES ("'.$product->getId().'","0","'.$FinalGroupedPriceData[0].'","'.$FinalGroupedPriceData[1].'","0");';
							#echo "INSERT SQL2: " . $insertPrice;
							$adapter->query($insertPrice);
						
						} else {
						#echo "VALUE ID: " . $db_product_id;
						#echo "SQL UPDATE2: ";
						
						$updatePrice="UPDATE ".$resource->getTableName('catalog/product_attribute_group_price')." SET customer_group_id = '".$FinalGroupedPriceData[0]."', value = '".$FinalGroupedPriceData[1]."' WHERE value_id = '".$db_product_id."'";
						$adapter->query($updatePrice);
						}
					}
				}
            }
		}		
		/* END OF GROUPED PRICING PRICING */
		/* ADDED FIX FOR IMAGE LABELS */

		if(isset($imagelabeldataforimport)) {
			$resource = Mage::getSingleton('core/resource');
			$prefix = Mage::getConfig()->getNode('global/resources/db/table_prefix'); 
			$prefixlabels = Mage::getConfig()->getNode('global/resources/db/table_prefix');  
			$readlabels = $resource->getConnection('core_read');
			$writelabels = $resource->getConnection('core_write');
			$select_qry_labels =$readlabels->query("SELECT value_id FROM ".$prefixlabels."catalog_product_entity_media_gallery WHERE entity_id = '". $product->getId() ."'");
			$row_labels = $select_qry_labels->fetch();
			$value_id = $row_labels['value_id'];
			// now $write label to db
			$writelabels->query("UPDATE ".$prefix."catalog_product_entity_media_gallery_value SET label = '".$imagelabeldataforimport."' WHERE value_id = '".$value_id."'");  
			//this is for if you have flat product catalog enabled.. need to write values to both places
			#$writelabels->query("UPDATE ".$prefix."catalog_product_flat_1 SET image_label = '".$imagelabeldataforimport."' WHERE entity_id = '". $product->getId() ."'"); 
			#$writelabels->query("UPDATE ".$prefix."catalog_product_flat_1 SET image_label = '".$imagelabeldataforimport."' WHERE entity_id = '". $product->getId() ."'");
			#SELECT attribute_id FROM ".$prefixlabels."eav_attribute WHERE attribute_code = 'image_label';
			#$writelabels->query("UPDATE ".$prefix."catalog_product_entity_varchar SET value = '".$imagelabeldataforimport."' WHERE entity_id = '". $product->getId() ."' AND attribute_id = 101");  
				
		}
		if(isset($smallimagelabeldataforimport)) {
	
			#echo "PROD ID: " . $product->getId() . "<br/>";
			#echo "LABELS: " . $smallimagelabeldataforimport . "<br/>";
			$resource = Mage::getSingleton('core/resource');
			$prefix = Mage::getConfig()->getNode('global/resources/db/table_prefix'); 
			$prefixlabels = Mage::getConfig()->getNode('global/resources/db/table_prefix');  
			$readlabels = $resource->getConnection('core_read');
			$writelabels = $resource->getConnection('core_write');
			$select_qry_labels =$readlabels->query("SELECT value_id FROM ".$prefixlabels."catalog_product_entity_media_gallery WHERE entity_id = '". $product->getId() ."'");
			$row_labels = $select_qry_labels->fetch();
			$value_id = $row_labels['value_id']+1;
			// now $write label to db
			$writelabels->query("UPDATE ".$prefix."catalog_product_entity_media_gallery_value SET label = '".$smallimagelabeldataforimport."' WHERE value_id = '".$value_id."'"); 
				
		}
		if(isset($thumbnailimagelabeldataforimport)) {
	
			#echo "PROD ID: " . $product->getId() . "<br/>";
			#echo "LABELS: " . $smallimagelabeldataforimport . "<br/>";
			$resource = Mage::getSingleton('core/resource');
			$prefix = Mage::getConfig()->getNode('global/resources/db/table_prefix'); 
			$prefixlabels = Mage::getConfig()->getNode('global/resources/db/table_prefix');  
			$readlabels = $resource->getConnection('core_read');
			$writelabels = $resource->getConnection('core_write');
			$select_qry_labels =$readlabels->query("SELECT value_id FROM ".$prefixlabels."catalog_product_entity_media_gallery WHERE entity_id = '". $product->getId() ."'");
			$row_labels = $select_qry_labels->fetch();
			$value_id = $row_labels['value_id']+2;
			// now $write label to db
			$writelabels->query("UPDATE ".$prefix."catalog_product_entity_media_gallery_value SET label = '".$thumbnailimagelabeldataforimport."' WHERE value_id = '".$value_id."'"); 
				
		}
		
		return true;
	}
	
	/**
	 * Edit tier prices
	 * 
	 * Uses a pipe-delimited string of qty:price to set tiers for the product row and appends.
	 * Removes if REMOVE is present.
	 * 
	 * @todo Prevent duplicate tiers (by qty) being set
	 * @internal Magento will save duplicate tiers; no enforcing unique tiers by qty, so we have to do this manually
	 * @param Mage_Catalog_Model_Product $product Current product row
	 * @param string $tier_prices_field Pipe-separated in the form of qty:price (e.g. 0=250=12.75|0=500=12.00)
	 */	
	private function _editTierPrices(&$product, $tier_prices_field = false, $store)
	{
		if (($tier_prices_field) && !empty($tier_prices_field)) {
            
            if(trim($tier_prices_field) == 'REMOVE'){
            
                $product->setTierPrice(array());
            
            } else {
                
                
                if($this->getBatchParams('append_tier_prices') == "true") { 
               		 //get current product tier prices
                	$existing_tps = $product->getTierPrice();
				} else {
                	$existing_tps = array();
				}
                
                $etp_lookup = array();
                //make a lookup array to prevent dup tiers by qty
                foreach($existing_tps as $key => $etp){
                    $etp_lookup[intval($etp['price_qty'])] = $key;
                }
                
                //parse incoming tier prices string
                $incoming_tierps = explode('|',$tier_prices_field);
								$tps_toAdd = array();  
								$tierpricecount=0;              
							foreach($incoming_tierps as $tier_str){
										//echo "t: " . $tier_str;
                    if (empty($tier_str)) continue;
                    
                    $tmp = array();
                    $tmp = explode('=',$tier_str);
                    
                    if ($tmp[1] == 0 && $tmp[2] == 0) continue;
										//echo ('adding tier');
                    //print_r($tmp);
                    $tps_toAdd[$tierpricecount] = array(
                                        'website_id' => 0, // !!!! this is hard-coded for now
                                        #'website_id' => $tmp[0], // !!!! this is hard-coded for now
										#'website_id' => $store->getWebsiteId(),
                                        'cust_group' => $tmp[0], // !!! so is this
                                        'price_qty' => $tmp[1],
                                        'price' => $tmp[2],
                                        'delete' => ''
                                    );
                                    
                    //drop any existing tier values by qty
                    if(isset($etp_lookup[intval($tmp[1])])){
                        unset($existing_tps[$etp_lookup[intval($tmp[1])]]);
                    }
                    $tierpricecount++;
                }

                //combine array
                $tps_toAdd =  array_merge($existing_tps, $tps_toAdd);
               
							 	//print_r($tps_toAdd);
                //save it
                $product->setTierPrice($tps_toAdd);
            }
            
        }
	}

	
	protected function userCSVDataAsArray( $data )
	{
		return explode( ',', str_replace( " ", " ", $data ) );
	} 
	
	protected function skusToIds( $userData, $product )
	{
		$productIds = array();
		foreach ( $this -> userCSVDataAsArray( $userData ) as $oneSku ) {
			if ( ( $a_sku = ( int )$product -> getIdBySku( $oneSku ) ) > 0 ) {
				parse_str( "position=", $productIds[$a_sku] );
			} 
		} 
		return $productIds;
	} 
	
	
	protected function skusToIdswithPosition( $userData, $product )
	{
		
		$productIds = array();
		foreach ( $this -> userCSVDataAsArray( $userData ) as $oneSku ) {
			$oneSkuexploded = explode(':', $oneSku);
			if($oneSkuexploded[0] !="" && $oneSkuexploded[1] !="") {
			 	$final_position_value = $oneSkuexploded[0];
			} else {
				$final_position_value = "";
			}
			if($oneSkuexploded[1] !="") {
			 	$final_sku_value = $oneSkuexploded[1];
			} else {
				$final_sku_value = $oneSku;
			}
			if ( ( $a_sku = ( int )$product -> getIdBySku( $final_sku_value ) ) > 0 ) {
				parse_str( "position=".$final_position_value."", $productIds[$a_sku] );
			} 
		} 
		return $productIds;
	} 
	 protected $_categoryCache = array();
	 
   protected function _addCategories($categories, $store)
    {
		// $rootId = $store->getRootCategoryId();
		// $rootId = Mage::app()->getStore()->getRootCategoryId();
        //$rootId = 2; // our store's root category id
				if($this->getBatchParams('root_catalog_id') != "") {
					$rootId = $this->getBatchParams('root_catalog_id');
				} else {
				  $rootId = 2; 
				}
        if (!$rootId) {
            return array();
        }
        $rootPath = '1/'.$rootId;
        if (empty($this->_categoryCache[$store->getId()])) {
            $collection = Mage::getModel('catalog/category')->getCollection()
                ->setStore($store)
                ->addAttributeToSelect('name');
            $collection->getSelect()->where("path like '".$rootPath."/%'");

            foreach ($collection as $cat) {
                $pathArr = explode('/', $cat->getPath());
                $namePath = '';
                for ($i=2, $l=sizeof($pathArr); $i<$l; $i++) {
					//if(!is_null($collection->getItemById($pathArr[$i]))) { }
                    $name = $collection->getItemById($pathArr[$i])->getName();
                    $namePath .= (empty($namePath) ? '' : '/').trim($name);
                }
                $cat->setNamePath($namePath);
            }
            
            $cache = array();
            foreach ($collection as $cat) {
                $cache[strtolower($cat->getNamePath())] = $cat;
                $cat->unsNamePath();
            }
            $this->_categoryCache[$store->getId()] = $cache;
        }
        $cache =& $this->_categoryCache[$store->getId()];
        
        $catIds = array();
		  //->setIsAnchor(1)
	      //Delimiter is ' , ' so people can use ', ' in multiple categorynames
        foreach (explode(' , ', $categories) as $categoryPathStr) {
			//Remove this line if your using ^ vs / as delimiter for categories.. fix for cat names with / in them
           $categoryPathStr = preg_replace('#\s*/\s*#', '/', trim($categoryPathStr));
            if (!empty($cache[$categoryPathStr])) {
                $catIds[] = $cache[$categoryPathStr]->getId();
                continue;
            }
            $path = $rootPath;
            $namePath = '';
             #foreach (explode('^', $categoryPathStr) as $catName) {
             foreach (explode('/', $categoryPathStr) as $catName) {
                $namePath .= (empty($namePath) ? '' : '/').strtolower($catName);
                if (empty($cache[$namePath])) {
                    $cat = Mage::getModel('catalog/category')
                        ->setStoreId($store->getId())
                        ->setPath($path)
                        ->setName($catName)
						->setIsActive(1)
                        ->save();
                    $cache[$namePath] = $cat;
                }
                $catId = $cache[$namePath]->getId();
                $path .= '/'.$catId;
            }
            if ($catId) {
                $catIds[] = $catId;
            }
        }
        return join(',', $catIds);
    }
	
	protected function _removeFile( $file )
	{
		if ( file_exists( $file ) ) {
		$ext = substr(strrchr($file, '.'), 1);
			if( strlen( $ext ) == 4 ) {
				if ( unlink( $file ) ) {
					return true;
				} 
			}
		} 
		return false;
	}
	
	protected function remap($oldData)
	{
		$newData = array();
		
		$columnMap = array(
			/* Ignore administrative fields */
			'Completed (means all the files are in the imprint dropbox for this title)'	=> '',
			'Updated PDF'					=> '',
			'Updated Epub'					=> '',
			
			/* Product Fields */
			'Title'							=> 'name',
			'Series'						=> 'book_series',
			'Series2'						=> 'book_series_2',
			'Length'						=> 'book_series_length',
			'Imprint'						=> 'category_imprint',
			'Genre'							=> 'category_genre',
			'Release Date'					=> 'release_date',
			'List Price'					=> 'price',
			'ISBN'							=> '',
			'ASIN'							=> 'asin',
		//	'print List Price'				=> '',						// TODO: Make Attribute or Product
		//	'p ISBN'						=> '',						// TODO: Make Attribute or Product
			'Page Count'					=> 'book_page_count',
			'Long Description'				=> 'description',
			'Short Description'				=> 'short_description',
			
			/* Author Fields */
			'Contract ID'					=> 'author_contract_id',
			'Authors'						=> 'author_name',
			'Author Bio'					=> 'author_biography',
			'Author Real Name'				=> 'author_real_name',
			'Author Email'					=> 'author_email',
			'Agented By'					=> 'author_agented_by',
			'USA TODAY Bestselling Author'	=> 'author_best_seller_usa_today',
			'NY Times Bestselling Author'	=> 'author_best_seller_ny_times',
		);
			/* Parsed Fields */
			$columnMap[]					=  'author_url_key';
			$columnMap[]					=  'author_status';
			$columnMap[]					=  'store';
			$columnMap[]					=  'sku';
			$columnMap[]					=  'websites';
			$columnMap[]					=  'attribute_set';
			$columnMap[]					=  'type';
			$columnMap[]					=  'category_ids';
			$columnMap[]					=  'has_options';
			$columnMap[]					=  'tax_class_id';
			$columnMap[]					=  'visibility';
			$columnMap[]					=  'is_in_stock';
			$columnMap[]					=  'weight';
			$columnMap[]					=  'status';
			
			$columnMap[]					=  'meta_title';
			$columnMap[]					=  'meta_description';
			$columnMap[]					=  'meta_keyword';
			
			$columnMap[]					=  'image';
			$columnMap[]					=  'image_label';
			$columnMap[]					=  'small_image';
			$columnMap[]					=  'small_image_label';
			$columnMap[]					=  'thumbnail';
			$columnMap[]					=  'thumbnail_label';
			
			$columnMap[]					=  'downloadable_options';
			
			$columnMap[]					=  'special_price';
			$columnMap[]					=  'special_from_date';
			$columnMap[]					=  'special_to_date';
			$columnMap[]					=  'news_from_date';
			$columnMap[]					=  'news_to_date';
			
		/* Clean Sku */
		$sku = preg_replace("/[^0-9]/", "", $oldData['ISBN']);
		
		/* Parse file paths and check that they exist */
		$files = array(
			'image'		=> $sku . '.jpg',
			'epub'		=> $sku . '.epub',
			'pdf'		=> $sku . '.pdf',
		);
		
		foreach($files as $type => $filename) {
			if(file_exists(Mage :: getBaseDir( 'media' ) . DS . 'import' . DS . $filename)) {
				$files[$type] = '/'.$filename;
			} else {
				$files[$type] = '';
			}
		}
		
		/* Compute download link data */
		$downloadable_options	= array();
		if(!empty($files['epub']))
			$downloadable_options['epub']	= implode(',',array(
					'title'				=> 'ePub file for e-readers',
					'price'				=> 0,
					'numberofdownloads'	=> 0,
					'type'				=> 'file',
					'filename'			=> $files['epub'],
				//	'linkspurchased'	=> 0,
				));
		if(!empty($files['pdf']))
			$downloadable_options['pdf']	= implode(',',array(
					'title'				=> 'PDF file for universal reading',
					'price'				=> 0,
					'numberofdownloads'	=> 0,
					'type'				=> 'file',
					'filename'			=> $files['pdf'],
				//	'linkspurchased'	=> 0,
				));
		
		/* Compute or assign attribute values for import */		
		$dataMap = array(
			'store'							=> 'admin',
			'websites'						=> 'base',
			'sku'							=> $sku,
			'attribute_set'					=> (isset($sku)) ? 'Books' : 'Default',
			'has_options'					=> (count($downloadable_options)) ? '1' : '0',
			'tax_class_id'					=> 'Taxable Goods',
			'visibility'					=> 'Catalog, Search',
			'is_in_stock'					=> 1,
			'status'						=> 'Enabled',
			'weight'						=> 1,
			
			'category_ids'					=> $this->getBookCategoryIds($oldData['Imprint'],$oldData['Genre']),
			'type'							=> 'downloadable', // (count($downloadable_options)) ? 'downloadable' : 'simple', // Assume all downloadable for now
			'description'					=> nl2br($oldData['Long Description']),
			'downloadable_options'			=> implode('|',$downloadable_options),
			'asin'							=> trim($oldData['ASIN']),
			
			'image'							=> $files['image'],
			'small_image'					=> $files['image'],
			'thumbnail'						=> $files['image'],
			
			'book_series_2'					=> preg_replace("/[^0-9]/", "", $oldData['Series2']),
			'book_page_count'				=> preg_replace("/[^0-9]/", "", $oldData['Page Count']),
			'release_date'					=> date('Y-m-d',strtotime($oldData['Release Date'])),
			
			'author_status'					=> 1,
			'author_url_key'				=> preg_replace("/[^a-z0-9]/", "-", strtolower($oldData['Authors'])),
			'author_best_seller_usa_today'	=> ($oldData['USA TODAY Bestselling Author'] == 'Y') ? 1 : 0,
			'author_best_seller_ny_times'	=> ($oldData['NY Times Bestselling Author'] == 'Y') ? 1 : 0,
		);
		
		/* Apply column and data maps to import data */
		foreach($columnMap as $oldKey => $newKey) {
			if(array_key_exists($newKey,$dataMap)) {
				$value = $dataMap[$newKey];
			} elseif(is_numeric($oldKey)) {
				$value = '';
			} elseif(array_key_exists($oldKey,$oldData)) {
				$value = $oldData[$oldKey];
			} else {
				continue;
			}
			
			$newData[$newKey] = $value;
		}
		
		return $newData;
	}
	
	public function getBookCategoryIds($imprint,$genre) {
		$category_ids = array();
		$websites = Mage::app()->getWebsites();
		$default_store = $websites[1]->getDefaultStore();
		
		$imprint_array = explode(',',$imprint);
		foreach($imprint_array as $key => $kvalue) {
			$imprint_array[$key] = trim($kvalue);
		}
		
		$genre_array = explode(',',$genre);
		foreach($genre_array as $key => $kvalue) {
			$genre_array[$key] = trim($kvalue);
		}
		
		/* Get Main Books Category */
		if(!empty($this->book_category_id)) {
			$base_category = $this->book_category_id;
			$category_ids[] = $base_category;
		} else {
			$websites = Mage::app()->getWebsites();
			$root_category = $default_store->getRootCategoryId();
			$children = Mage::getModel('catalog/category')->load($root_category)->getChildrenCategories();
			foreach($children as $child) {
				if($child->getName() == $this->book_category) {
					$this->book_category_id = $child->getId();
					$base_category = $child->getId();
					$category_ids[] = $child->getId();
					break;
				}
			}
		}
		
		/* Get Main Imprints Category */
		if(!empty($this->book_imprints_category_id)) {
			$imprints_category = $this->book_imprints_category_id;
			$category_ids[] = $imprints_category;
		} else {
			$children = Mage::getModel('catalog/category')->load($base_category)->getChildrenCategories();
			foreach($children as $child) {
				if($child->getName() == $this->book_imprints_category) {
					$this->book_imprints_category_id = $child->getId();
					$imprints_category = $child->getId();
					$category_ids[] = $child->getId();
					break;
				}
			}
		}
		
		/* Get Main Genre Category */
		if(!empty($this->book_genre_category_id)) {
			$genre_category = $this->book_genre_category_id;
			$category_ids[] = $genre_category;
		} else {
			$children = Mage::getModel('catalog/category')->load($base_category)->getChildrenCategories();
			foreach($children as $child) {
				if($child->getName() == $this->book_genre_category) {
					$this->book_genre_category_id = $child->getId();
					$genre_category = $child->getId();
					$category_ids[] = $child->getId();
					break;
				}
			}
		}
		
		/* Get This Imprints Category */
		$children = Mage::getModel('catalog/category')->load($imprints_category)->getChildrenCategories();
		
		foreach($imprint_array as $imprint_item) {
			$current_imprint = '';
			foreach($children as $child) {
				if($child->getName() == $imprint_item) {
					$current_imprint = $child->getId();
					$category_ids[] = $child->getId();
					break;
				}
			}
			if(!$current_imprint) {
				$urlkey = preg_replace("/[^a-z0-9]/", "-", strtolower($imprint_item));
				$category = Mage::getModel('catalog/category');
				$category->setName($imprint_item);
				$category->setUrlKey($urlkey);
				$category->setIsActive(1);
				$category->setDisplayMode('PRODUCTS');
				$category->setIsAnchor(1);
				$category->setStoreId($default_store->getId());
				$parentCategory = Mage::getModel('catalog/category')->load($imprints_category);
				$category->setPath($parentCategory->getPath());
				$category->save();
				$category_ids[] = $category->getId();
			}
		}
		
		/* Get This Genre Category */
		$children = Mage::getModel('catalog/category')->load($genre_category)->getChildrenCategories();
		foreach($genre_array as $genre_item) {
			$current_genre = '';
			foreach($children as $child) {
				if($child->getName() == $genre_item) {
					$current_genre = $child->getId();
					$category_ids[] = $child->getId();
					break;
				}
			}
			if(!$current_genre) {
				$urlkey = preg_replace("/[^a-z0-9]/", "-", strtolower($genre_item));
				$category = Mage::getModel('catalog/category');
				$category->setName($genre_item);
				$category->setUrlKey($urlkey);
				$category->setIsActive(1);
				$category->setDisplayMode('PRODUCTS');
				$category->setIsAnchor(1);
				$category->setStoreId($default_store->getId());
				$parentCategory = Mage::getModel('catalog/category')->load($genre_category);
				$category->setPath($parentCategory->getPath());
				$category->save();
				$category_ids[] = $category->getId();
			}
		}
		
		return implode(',',$category_ids);
	}
	
	public function addAttributeOptions($attribute_code, array $optionsArray) 
	{
		$attribute_model        = Mage::getModel('eav/entity_attribute');
        $attributeId         = $attribute_model->getIdByCode('catalog_product', $attribute_code);
        //$attributeId = (int) $this->getAttribute('catalog_product', $attribute_code, 'attribute_id');
        $attribute            = $attribute_model->load($attributeId);
		
        $values = array();
        foreach ($optionsArray as $sortOrder => $label) {
			if(!$this->attributeValueExists($attribute_code, $label))
			{
				$value['option'] = array($label,$label);
				$result = array('value' => $value);
				$attribute->setData('option',$result);
				$attribute->save();
			}
			$attribute_options_model= Mage::getModel('eav/entity_attribute_source_table') ;
			$attribute_table        = $attribute_options_model->setAttribute($attribute);
			$options                = $attribute_options_model->getAllOptions(false);

			foreach($options as $option)
			{
				if ($option['label'] == $label)
				{
					$values[] = $option['value'];
				}
			}
        }
        
        return $values;
    } 
    
    public function attributeValueExists($arg_attribute, $arg_value)
    {
        $attribute_model        = Mage::getModel('eav/entity_attribute');
        $attribute_options_model= Mage::getModel('eav/entity_attribute_source_table') ;

        $attribute_code         = $attribute_model->getIdByCode('catalog_product', $arg_attribute);
        $attribute              = $attribute_model->load($attribute_code);

        $attribute_table        = $attribute_options_model->setAttribute($attribute);
        $options                = $attribute_options_model->getAllOptions(false);

        foreach($options as $option)
        {
            if ($option['label'] == $arg_value)
            {
                return $option['value'];
            }
        }

        return false;
    }
}