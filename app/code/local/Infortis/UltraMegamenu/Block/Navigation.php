<?php 

class Infortis_UltraMegamenu_Block_Navigation extends Mage_Catalog_Block_Navigation{
	const DDTYPE_NONE = 0; 
	const DDTYPE_MEGA = 1; 
	const DDTYPE_CLASSIC= 2; 
	const DDTYPE_SIMPLE = 3; 
	
	protected $ddtype; 
	protected $p0c = FALSE;	
	protected $p0d;	
	protected $p0e = FALSE;	
	protected $p0f = NULL; 
	protected $currentCategory = NULL; 
	
	protected function _construct()
	{ 
		parent::_construct(); 
		
		$this->ddtype = array(
			self::DDTYPE_MEGA => "mega", 
			self::DDTYPE_CLASSIC => "classic", 
			self::DDTYPE_SIMPLE => "simple"
		); 
		$this->p0c = FALSE; 
		$this->p0d = "#@#"; 
		$this->p0e = FALSE; 
		$this->p0f = NULL; 
		
		if (Mage::registry('current_category')) { 
			$this->currentCategory = Mage::registry('current_category')->getId();
		} 
	}
	
	public function getCacheKeyInfo()
	{
		$x11 = array(
			'CATALOG_NAVIGATION', 
			Mage::app()->getStore()->getId(), 
			Mage::getDesign()->getPackageName(), 
			Mage::getDesign()->getTheme('template'), 
			Mage::getSingleton('customer/session')->getCustomerGroupId(), 
			'template' => $this->getTemplate(), 
			'name' => $this->getNameInLayout(), 
			$this->getCurrenCategoryKey(), 
			Mage::helper('ultramegamenu')->getIsOnHome(), 
			(int)Mage::app()->getStore()->isCurrentlySecure(),
		);
		
		$x12 = $x11;
		$x11 = array_values($x11);
		$x11 = implode('|', $x11);
		$x11 = md5($x11);
		
		$x12['category_path'] = $this->getCurrenCategoryKey();
		$x12['short_cache_id'] = $x11;return $x12;
	}
	
	protected function getItemHtml($item, $x14 = 0, $x15 = FALSE, $x16 = FALSE,$x17 = FALSE, $x18 = '', $x19 = '', $x1a = FALSE, $x1b = null)
	{
		if (!$item->getIsActive()) { 
			return '';
		}
		
		$html = ''; 
		if (Mage::helper('catalog/category_flat')->isEnabled()) {
			$categories = (array)$item->getChildrenNodes(); 
			$category_count = count($categories);
		} else { 
			$categories = $item->getChildren(); 
			$category_count = $categories->count();
		}
		
		$x1f = ($categories && $category_count);
		
		$active_categories = array();
		
		foreach ($categories as $category) {
			if ($category->getIsActive()) {
				$active_categories[] = $category;
			}
		}
		
		$x22 = count($active_categories);
		$x23 = ($x22 > 0);
		$x24 = Mage::helper('ultramegamenu');
		/*$item = Mage::getModel('catalog/category')->load($item->getId());*/ 
		
		$x26 = intval($item->getData('umm_dd_type')); 
		if ($x26 === self::DDTYPE_NONE){ if ($x1b["ddType"] === self::DDTYPE_MEGA) {} else { $x26 = self::DDTYPE_CLASSIC; }}
		elseif ($x26 === self::DDTYPE_MEGA){ if ($x1b["ddType"] === self::DDTYPE_MEGA) { $x26 = self::DDTYPE_NONE; }}
		elseif ($x26 === self::DDTYPE_SIMPLE){ if ($x1b["ddType"] === self::DDTYPE_MEGA) { $x26 = self::DDTYPE_NONE; } elseif ($x14 === 0) { $x26 = self::DDTYPE_CLASSIC; }}
		
		$x27 = array( "ddType" => $x26, );
		$x28 = FALSE;
		$x29 = array();
		$x2a = '';
		$x2b = '';
		$x2c = FALSE;
		$x2d = "level" . $x14;
		if ($x14 == 2){
			$x2d .= " grid12-5 mobile-grid-half";
		}
		if (FALSE === $this->p0c&& ($x26 === self::DDTYPE_MEGA || $x1b["ddType"] === self::DDTYPE_MEGA) )
			{ $x28 = TRUE; }
		if ($x28){ $x2e = $this->x10($item, "umm_dd_blocks");if ($x2e) {$x29 = explode($this->p0d, $x2e); }}
		if (FALSE === $this->p0c && $x26 === self::DDTYPE_MEGA) { 
			$x2f = $item->getData("umm_dd_proportions");
			if ($x2f) { 
				$x30 = explode(";", $x2f);
				$x31= $x30[0];
				$x32 = $x30[1];
				$x33= $x30[2]; 
			} else { 
				$x31 = $x32 = $x33 = 4; 
			} 
			$x34 = "grid12-" . $x31;
			$x35 = "grid12-" . $x32;
			$x36= "grid12-" . $x33;
			if (empty($x29[1]) && empty($x29[2])) {
				$x34= '';
				$x35 = "grid12-12";
				$x36= ''; 
			} elseif (empty($x29[1])) {
				$x34= '';
				$x35 = "grid12-" . ($x31 + $x32); 
			} elseif (empty($x29[2])) {
				$x35 = "grid12-" . ($x32 + $x33);
				$x36= '';
			}elseif (!$x23) {
				$x34= "grid12-" . ($x31 + $x32);
				$x35 = '';
				$x36= "grid12-" . $x33; 
			}
			
			if (!empty($x29[0])) {
				$x2c = TRUE;
				$x2a .= '<div class="nav-block nav-block--top std grid-full">' . $x29[0] . '</div>'; 
			}
			
			if (!empty($x29[1])) {
				$x2c = TRUE;
				$x2a .= '<div class="nav-block nav-block--left std ' . $x34 . '">' . $x29[1] . '</div>'; 
			}
			
			if ($x23) {
				$x2a .= '<div class="nav-block--center ' . $x35 . '">'; $x2b .= '</div>';
			}
			if (!empty($x29[2])) {
				$x2c = TRUE;
				$x2b .= '<div class="nav-block nav-block--right std ' . $x36 . '">' . $x29[2] . '</div>';
			}
			
			if (!empty($x29[3])) {
				$x2c = TRUE;
				$x2b .= '<div class="nav-block nav-block--bottom std grid-full">' . $x29[3] . '</div>';
			}
		}
		
		$x37 = ($x23 || $x2c) ? TRUE : FALSE;
		$classes = array("nav-item");
		$x39 = array();
		$x3a = array(); 
		$x3b = array("nav-submenu");
		$x3c = '';
		$x3d = '';
		$x3e = '';
		$classes[] = $x2d;
		$classes[] = "nav-" . $this->_getItemPosition($x14);
		
		if ($this->isCategoryActive($item)) {
			$classes[] = "active";
			if ($item->getId() === $this->currentCategory) {
				$classes[] = "current"; 
			}
		}
		
		if ($x17 && $x18) {
			$classes[] = $x18;
			$x39[] = $x18;
		}
		
		if ($x16) {
			$classes[] = "first";
		}
		
		if ($x15) {
			$classes[] = "last";
		}
		
		if (FALSE === $this->p0c) {
			if ($x26 === self::DDTYPE_CLASSIC) {
				if ($x37){ 
					$classes[] = "nav-item--parent"; //nav-item--parent
					$x3b[] = "nav-panel--dropdown"; //nav-panel--dropdown
				}
				
				$classes[] = $this->ddtype[self::DDTYPE_CLASSIC];
				$x3b[] = "nav-panel";
			} elseif ($x26 === self::DDTYPE_MEGA) {
				if ($x37){
					$classes[] = "nav-item--parent"; //nav-item--parent
					$x3a[] = "nav-panel--dropdown"; //nav-panel--dropdown
				}
				
				$classes[] = $this->ddtype[self::DDTYPE_MEGA];
				$x3a[] = "nav-panel";
				
				if ($x19){
					$x3a[] = $x19;
				}
				
				$x3b[] = "nav-submenu--mega"; //nav-submenu--mega
				$x3f = intval($item->getData("umm_dd_columns")); //umm_dd_columns
				if ($x3f === 0) { 
					$x3f = 4; 
				}
				
				$x3b[] = "dd-itemgrid dd-itemgrid-" . $x3f . "col"; //dd-itemgrid dd-itemgrid-
			
			} elseif ($x26 === self::DDTYPE_SIMPLE) {
				$classes[] = $this->ddtype[self::DDTYPE_SIMPLE];
				$x3b[] = "nav-panel";
			} elseif ($x26 === self::DDTYPE_NONE) {
				$x3b[] = "nav-panel";
			}
			
			if ($x40 = $item->getData("umm_dd_width")) {
				$x41 = '';
				$x42 = ''; 
				if (strpos($x40, "px") || strpos($x40, "%")) {
					$x41 = ' style="width:' . $x40 . ';"';
				} else {
					$x42 = intval($x40);
					if (0 < $x42 && $x42 <= 12) {
						$x42 = "no-gutter grid12-" . $x42; //no-gutter grid12-
					} else { 
						$x42 = ''; 
					}
				}
				
				if ($x26 === self::DDTYPE_CLASSIC) {
					$x3d = $x41;
				} elseif ($x26 === self::DDTYPE_MEGA) {
					$x3c = $x41;
					if ($x42) {
						$x3a[] = $x42;
					}
				}
			} else { 
				if ($x26 === self::DDTYPE_MEGA) {
					$x3a[] = "full-width";
				}
			}
			if ($x2c) {
				if (FALSE === $x23){
					$classes[] = "nav-item--only-blocks"; //nav-item--only-blocks
				}
			} else {
				if ($x23) {
					$classes[] = "nav-item--only-subcategories"; //nav-item--only-subcategories
				}
			}
		}
		
		if ($x37){$classes[] = "parent";if (FALSE === $this->p0c) {$x3e = '<span class="caret">&nbsp;</span>'; }}
		
		$x43 = '';
		if ($this->p0e && $this->p0c) { $x43 = '<span class="number">('. $this->x0f($item) .')</span>';}
		$x44 = $this->x11($item, $x14);
		if ($x45 = $item->getData("umm_cat_target")) {
			if ($x45 === "#") {
				$url = "#";
				$x39[] = "no-click";
			} 
			elseif ($x45 = trim($x45)) {
				if (strpos($x45, "http") === 0){$url = $x45;}else{$url = Mage::getBaseUrl() . $x45;}
			} else { $url = $this->getCategoryUrl($item); }
		} else {
			$url = $this->getCategoryUrl($item);
		}
		
		$html .= "<li" . ($classes ? ' class="' . implode(" ", $classes) . '"': ''). ">";
		
		if (FALSE === $this->p0c && $x1b["ddType"] === self::DDTYPE_MEGA){
			if (!empty($x29[0])) {
				$html .= '<div class="nav-block nav-block--top std">' . $x29[0] . '</div>'; 
			}
		}
		$html .= '<a href="' . $url . '"' . ($x39 ? ' class="' . implode(" ", $x39) . '"': '') . '>';
		$html .= '<span>' . $this->escapeHtml($item->getName()) . $x43 . $x44 . '</span>' . $x3e;
		$html .= '</a>';
		$x47 = '';
		$x48 = 0;
		
		foreach ($active_categories as $category) {
			$x47 .= $this->getItemHtml($category, ($x14 + 1), ($x48 == $x22 - 1), ($x48 == 0), FALSE, $x18,$x19,$x1a,$x27); $x48++;
		}
		if (!empty($x47) || $x2c) {
			$html .= '<span class="opener"></span>';
			if (!empty($x3a)) {
				$html .= '<div class="' . implode(' ', $x3a) . '"' . $x3c . '>
				<div class="nav-blur-back">
            	</div>
				<div class="nav-panel-inner">';
			}
			$html .= $x2a;
			if (!empty($x47)) {
				$html .= '<ul class="' . $x2d .' '. implode(' ', $x3b) . '"' . $x3d . '>';
				$html .= $x47;
				$html .= '</ul>';
			}
			$html .= $x2b;
			if (!empty($x3a)) {
				$html .= "</div></div>"; 
			}
		}
		if (FALSE === $this->p0c && $x1b["ddType"] === self::DDTYPE_MEGA) {
			if (!empty($x29[3])) {
				$html .= '<div class="nav-block nav-block--bottom std">' . $x29[3] . '</div>';
			}
		}
		$html .= "</li>";
		
		return $html;
	}
	
	public function renderCategoriesMenuHtml($x49 = FALSE, $x14 = 0, $x18 = '', $x19 = '') 
	{
		$items = array();
		foreach ($this->getStoreCategories() as $category) { 
			if ($category->getIsActive()) {
				$items[] = $category; 
			}
		}
		
		$x4b = count($items);
		$x4c = ($x4b > 0);
		if (!$x4c) { return '';}
		
		$x1b = array("ddType" => self::DDTYPE_NONE);
		$html = '';
		$x48 = 0;
		
		foreach ($items as $item) { 
			$html .= $this->getItemHtml($item,$x14,($x48 == $x4b - 1),($x48 == 0),TRUE,$x18,$x19,TRUE,$x1b );
			$x48++;
		}

		$customHelper = Mage::helper('entangled_custom');
		
        // Add teen menu here
        $html .= '<li class="nav-item level0 level-top parent">
                <a class="level-top" href="' . $this->getUrl('books/imprints/teen.html') . '" title="Teen"> <span>Teen</span><span class="caret">&nbsp;</span></a>
                <div class="nav-panel--dropdown nav-panel tmp-full-teen-width" style="left: 0px; top: 52px; display: none;">
                	<div class="nav-blur-back">
                	</div>
                	<div class="nav-panel-inner">
                		<div class="nav-block--center grid12-12">
                			<ul class="level0 nav-submenu nav-submenu--mega dd-itemgrid dd-itemgrid-2col">
                				<li class="nav-item level1 nav-1-1 teen_last nav-item--only-subcategories parent">
                					<a href="' . $this->getUrl('books/featured.html').'"><span>Featured</span></a>
                					<span class="opener"></span>
                					<ul class="level1 nav-submenu nav-panel">
                						<li class="nav-item level2 grid12-12 mobile-grid-half nav-1-1-1 first classic">
                							<a href="' . $this->getUrl('promotions/teen/new-releases.html') . '"><span>New Releases</span></a>
                						</li>
                						<li class="nav-item level2 grid12-12 mobile-grid-half nav-1-1-3 first classic">
                							<a href="' . $this->getUrl('promotions/teen/award-winners.html') . '"><span>Award Winners</span></a>
                						</li>
                						<li class="nav-item level2 grid12-12 mobile-grid-half nav-1-1-4 first classic">
                							<a href="' . $this->getUrl('promotions/teen/best-sellers.html') . '"><span>Best Sellers</span></a>
                						</li>
                						<li class="nav-item level2 grid12-12 mobile-grid-half nav-1-1-4 first classic">
                							<a href="' . $this->getUrl('promotions/teen/on-sale.html') . '"><span>On Sale</span></a>
                						</li>
                					</ul>
                				</li>
                				<li class="nav-item level1 nav-1-2 teen_last nav-item--only-subcategories parent">
                					<a href="' . $customHelper->getLayerUrl("book_imprint","Teen") . '"><span>Imprints</span></a>
                					<span class="opener"></span>
                					<ul class="level1 nav-submenu nav-panel">
                						<li class="nav-item level2 grid12-12 mobile-grid-half nav-1-2-1 first classic">
                							<a href="' . $customHelper->getLayerUrl("book_imprint","Teen Crave") . '"><span>Teen Crave</span></a>
                						</li>
                						<li class="nav-item level2 grid12-12 mobile-grid-half nav-1-2-2 first classic">
                							<a href="' . $customHelper->getLayerUrl("book_imprint","Teen Crush") . '"><span>Teen Crush</span></a>
                						</li>
                                        <li class="nav-item level2 grid12-12 mobile-grid-half nav-1-2-2 first classic">
                							<a href="' . $customHelper->getLayerUrl("book_imprint","Teen") . '"><span>Entangled Teen</span></a>
                						</li>
                					</ul>
                				</li>
                			</ul>
                		</div>
                	</div>
                </div>
        </li>';
        // End
		// Start Author link insertion
		$action = Mage::app()->getFrontController()->getAction()->getFullActionName();
		$activeAuthor = '';
		if($action == 'publish_author_index' || $action == 'publish_author_view') $activeAuthor = ' active';
		
		$html .= '<li class="mobile-grid-half nav-item nav-item--authors level0 level-top'.$activeAuthor.'">';
		$html .= '<a class="level-top" href="' . Mage::helper('publish/author')->getAuthorsUrl() . '">';
		$html .= '<span>'. Mage::helper('publish')->__('Authors').'</span>';
		$html .= '</a></li>';
		// End Author link insertion
		
		return $html;
	} 
	
	public function renderMe($x49, $x4d = 0, $x4e = 0)
	{
		$x4f = '';
		$x50 = '';
		if ($x4d === 'parent_no_siblings'){ 
			if ($current_category = Mage::registry('current_category')) {
				$x4f= $current_category->getId();
				$x50 = $current_category->getLevel(); 
			}
		}
		$this->p0c = TRUE;
		$this->p0e = Mage::helper('ultramegamenu')->getCfg('sidemenu/num_of_products'); 
		
		$x14 = 0;
		$x18 = '';
		$x19 = ''; 
		$x52 = $this->x0e($x4d); 
		$x53 = $this->x0c($x52, $x4e);
		
		$items = array();
		foreach ($x53 as $category){ 
			if ($category->getIsActive()) {
				if ($x4d === 'parent_no_siblings') { 
					if ($x50 !== '' && $category->getLevel() == $x50 && $category->getId() != $x4f) { 
						continue; 
					}
				}
				$items[] = $category;
			}
		}
		$x4b = count($items);
		$x4c = ($x4b > 0);
		if (!$x4c) { return '';} 
		$x1b = array("ddType" => self::DDTYPE_NONE);
		
		$html = '';
		$x48 = 0;
		foreach ($items as $item) { 
			$html .= $this->getItemHtml($item,$x14,($x48 == $x4b - 1),($x48 == 0),TRUE,$x18,$x19,TRUE,$x1b ); 
			$x48++;
		}
		
		return $html;
	}
	
	protected function x0c($x52 = 0, $x4e = 0, $x54=FALSE, $x55=FALSE, $x56=TRUE)
	{
		$item = Mage::getModel('catalog/category');
		if ($x52 === NULL || !$item->checkId($x52)){ 
			return array();
		}
		if (Mage::helper('catalog/category_flat')->isEnabled()){
			$x57 = Mage::getResourceModel('catalog/category_flat');
			$x53 = $x57->getCategories($x52, $x4e, $x54, $x55, $x56);
		} else {
			$x53 = $item->getCategories($x52, $x4e, $x54, $x55, $x56);
		}
		
		return $x53;
	}
	
	protected function x0e($x4d)
	{ 
		$x52 = NULL;
		if ($x4d === 'current'){ 
			$current_category = Mage::registry('current_category'); 
			if ($current_category) {
				$x52 = $current_category->getId(); 
			}
		} elseif ($x4d === 'parent') { 
			$current_category = Mage::registry('current_category'); 
			if ($current_category) {
				$x52 = $current_category->getParentId(); 
			}
		} elseif ($x4d === 'parent_no_siblings'){ 
			$current_category = Mage::registry('current_category'); 
			if ($current_category) {
				$x52 = $current_category->getParentId(); 
			}
		} elseif ($x4d === 'root' || !$x4d) { 
			$x52 = Mage::app()->getStore()->getRootCategoryId();
		} elseif (is_numeric($x4d)) { 
			$x52 = intval($x4d);
		}
		
		$x58 = Mage::helper('ultramegamenu')->getCfg('sidemenu/fallback');
		if ($x52 === NULL && $x58) { 
			$x52 = Mage::app()->getStore()->getRootCategoryId();
		}
		
		return $x52; 
	} 
	
	protected function x0f($item)
	{
		return Mage::getModel('catalog/layer') ->setCurrentCategory($item->getID()) ->getProductCollection() ->getSize(); 
	} 
	
	public function renderBlockTitle()
	{
		$x24 = Mage::helper('ultramegamenu');
		$current_category = Mage::registry('current_category');
		if (!$current_category) {
			$x58 = $x24->getCfg('sidemenu/fallback'); 
			if ($x58) {
				$x59 = $x24->getCfg('sidemenu/block_name_fallback');
				if ($x59){
					return $x59;
				}
			}
		}
		$x5a = $this->getBlockName();
		if ($x5a === NULL) { 
			$x5a = $x24->getCfg('sidemenu/block_name');
		}
		$x5b = '';
		if ($current_category){
			$x5b = $current_category->getName();
		}
		$x5a = str_replace('[current_category]', $x5b, $x5a);
		
		return $x5a;
	} 
	
	protected function x10($item, $x5c)
	{
		if (!$this->p0f){ 
			$this->p0f = Mage::helper('cms')->getBlockTemplateProcessor();
		}
		
		return $this->p0f->filter( trim($item->getData($x5c)) );
	} 
		
	protected function x11($item, $x14)
	{
		$label_class = $item->getData('umm_cat_label');
		if ($label_class){
			$label = trim(Mage::helper('ultramegamenu')->getCfg('category_labels/' . $label_class));
			if ($label) {
				if ($x14 == 0){
					return '<span class="cat-label cat-label-'. $label_class .' pin-bottom">' . $label . '</span>';
				} else {
					return '<span class="cat-label cat-label-'. $label_class .'">' . $label . '</span>';
				}
			}
		}
		
		return '';
	}

    /**
     * Get url for category data
     *
     * @param Mage_Catalog_Model_Category $category
     * @return string
     */
    public function getCategoryUrl($category)
    {
        if ($category instanceof Mage_Catalog_Model_Category) {
            $customHelper = Mage::helper('entangled_custom');
            if(strpos($category->getPath(),"1/2/5/4") === 0){
                $url = $customHelper->getLayerUrl("book_imprint",$category->getName());
            }elseif(strpos($category->getPath(),"1/2/5/3") === 0){
                $url = $customHelper->getLayerUrl("book_genre",$category->getName());
            }else{
                $url = $category->getUrl();
            }
        } else {
            $url = $this->_getCategoryInstance()
                ->setData($category->getData())
                ->getUrl();
        }

        return $url;
    }
} ?>