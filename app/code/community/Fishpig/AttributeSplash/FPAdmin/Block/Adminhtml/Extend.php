<?php
/**
 * @category    Fishpig
 * @package     Fishpig_FPAdmin
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

 	// Required for compiler as this is used in multiple modules
 	if (!defined('__fishpig_extend')) {
 		define('__fishpig_extend', true);

abstract class Fishpig_FPAdmin_Block_Adminhtml_Extend extends Mage_Core_Block_Template
implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
	/**
	 * Tracking string for GA
	 *
	 * @var const string
	 */
	const TRACKING_STRING = '?utm_source=%s&utm_medium=%s&utm_term=%s&utm_campaign=Extend';
	
	/**
	 * Base URL for links
	 *
	 * @var const string
	 */
	 const BASE_URL = 'http://fishpig.co.uk/';

	 /**
	  * The URL for the S3 bucket for images
	  *
	  * @var const string
	  */
	 const S3_BUCKET_URL = 'https://s3.amazonaws.com/FishPig-Extend/image/';
	 
	/**
	 * Cache for all available extensions
	 *
	 * @var array
	 */
	static protected $_extensions = null;
	
	protected function _construct()
	{
		$this->setTemplate('small.phtml');
		
		return parent::_construct();
	}

	/**
	 * Retrieve extensions set via XML
	 *
	 * @return array
	 */
	public function getSelectedExtensions()
	{
		return $this->getExtensions($this->getLimit(), $this->getPreferred() ? ($this->getPreferred()) : null);	
	}

	/**
	 * Retrieve the available extensions taking into account $count and $pref
	 *
	 * @param int $count = 0
	 * @param array $pref = array()
	 * @return false|array
	 */
	public function getExtensions($count = 0, array $pref = array(), $rand = false)
	{
		if (!isset($pref[0])) {
			$pref = array_keys($pref);
		}

		if (($pool = $this->_getAllExtensions()) !== false) {
			$winners = array();

			foreach($pref as $code) {
				if (isset($pool[$code])) {
					$winners[$code] = $pool[$code];
					unset($pool[$code]);
				}
				
				if (!$rand && $count > 0 && count($winners) >= $count) {
					break;
				}
			}
			
			if ($rand) {
				$winners = $this->shuffleArray($winners);

				if ($count > 0 && count($winners) > $count) {
					$xcount = count($winners);

					while($xcount-- > $count) {
						array_pop($winners);
					}
				}
			}
			
			while(count($winners) < $count && count($pool) > 0) {
				$code = key($pool);
				
				$winners[$code] = $pool[$code];
				unset($pool[$code]);
			}
					
			end($winners);
			
			$winners[key($winners)]['last'] = true;
	
			return $winners;
		}
		
		return false;
	}
	
	/**
	 * Retrieve all of the available extensions
	 *
	 * @return array
	 */
	protected function _getAllExtensions()
	{
		if (!is_null(self::$_extensions)) {
			return self::$_extensions;
		}
		
		$installedModules = array_keys((array)$this->_getConfig()->getNode('modules'));
		$config = (array)$this->_getConfig()->getNode('fishpig/extend')->asArray();
		self::$_extensions = array();

		foreach($config as $code => $extension) {
			$extension['module'] = $code;
			$reqMultistore = isset($extension['require_multistore']) ? (int)$extension['require_multistore'] : null;

			if (!isset($_SERVER['IS_FISHPIG']) && in_array($code, $installedModules)) {
				continue;
			}
			else if (!is_null($reqMultistore) && $reqMultistore === (int)Mage::app()->isSingleStoreMode()) {
				continue;
			}
			else if (isset($extension['depends'])) {
				$depends = array_keys((array)$extension['depends']);

				if (count(array_diff($depends, $installedModules)) > 0) {
					continue;
				}
			}

			self::$_extensions[$code] = (array)$extension;
		}
		
		if (count(self::$_extensions) === 0) {
			self::$_extensions = false;
		}

		return self::$_extensions;
	}

	/**
	 * Retrieve the title of the extension
	 *
	 * @param array $e
	 * @return string
	 */
	public function getTitle(array $e = null)
	{
		// Being called as a tab
		if (is_null($e)) {
			return $this->_getData('title');
		}

		return $this->_getField($e, 'title');
	}
	
	/**
	 * Retrieve the subtitle of the extension
	 *
	 * @param array $e
	 * @return string
	 */
	public function getSubTitle(array $e)
	{
		return $this->_getField($e, 'subtitle');
	}

	/**
	 * Rertrieve the URL for $e with the tracking code
	 *
	 * @param array $e
	 * @param string $campaign
	 * @param string $source
	 * @param string $medium
	 * @return string
	 */
	public function getTrackedUrl(array $e, $source, $content = null)
	{
		$term = $this->_getField($e, 'module');	
		 
		$trackedUrl = sprintf(self::BASE_URL . $this->_getField($e, 'url') . self::TRACKING_STRING, $source, $this->getMedium(), $term);
		
		if (!is_null($content)) {
			$trackedUrl .= '&utm_content=' . $content;
		}
		
		return $trackedUrl;
	}
	
	/**
	 * Retrieve the utm_medium parameter
	 *
	 * @return string
	 */
	public function getMedium()
	{
		return $this->_getData('medium')
			? $this->_getData('medium')
			: 'Magento Admin';
	}
	
	/**
	 * Retrieve the short definition of the extension
	 *
	 * @param array $e
	 * @return string
	 */
	public function getShortDefinition(array $e)
	{
		return $this->_getField($e, 'short_definition');
	}
	
	/**
	 * Retrieve the image URL of the extension
	 *
	 * @param array $e
	 * @return string
	 */
	public function getImageUrl(array $e)
	{
		return self::S3_BUCKET_URL . $this->_getField($e, 'image');
	}
	
	/**
	 * Retrieve a field from the extension
	 *
	 * @param array $e
	 * @param string $field
	 * @return string
	 */
	protected function _getField(array $e, $field)
	{
		return $e && is_array($e) && isset($e[$field]) ? $e[$field] : '';
	}
	
	/**
	 * Determine wether $e is the last $e in the array
	 *
	 * @param array $e
	 * @return bool
	 */
	public function isLast(array $e)
	{
		return $this->_getField($e, 'last') === true;
	}

	/**
	 * Retrieve the Magento config model
	 *
	 * @return Mage_Core_Model_Config
	 */
	protected function _getConfig()
	{
		return Mage::app()->getConfig();
	}
	
	/**
	 * Retrieve the ID
	 *
	 * @return string
	 */
	public function getId()
	{
		if (!$this->_getData('id')) {
			$this->setId('fp-extend-' . rand(1111, 9999));
		}
		
		return $this->_getData('id');
	}

	/**
	 * Retrieve the full path to the template
	 *
	 * @return string
	 */
    public function getTemplateFile()
    {
    	if (($dir = $this->_getFPAdminDir()) !== false) {
	    	return $dir . 'template' . DS . $this->getTemplate();
		}
    }

	/**
	 * Set the template include path
	 *
	 * @param string $dir
	 * @return $this
	 */    
	public function setScriptPath($dir)
	{
		$this->_viewDir = '';
		
		return $this;
	}
	
	/**
	 * Retrieve any available FPAdmin directory
	 *
	 * @return false|string
	 */
	protected function _getFPAdminDir()
	{
		$candidates = array(
			$this->getModule(),
			'Fishpig_Wordpress',
			'Fishpig_AttributeSplash',
			'Fishpig_iBanners'
		);

		foreach(array_unique($candidates) as $candidate) {
			if (!$candidate) {
				continue;
			}

			$dir = Mage::getModuleDir('', $candidate) . DS . 'FPAdmin' . DS;
			
			if (is_dir($dir)) {
				return $dir;
			}
		}
		
		return false;
	}
	
	/**
	 * If tab, always show
	 *
	 * @return bool
	 */
	public function canShowTab()
	{
		return true;
	}
	
	/**
	 * Don't hide if a tab
	 *
	 * @return bool
	 */
	public function isHidden()
	{
		return false;
	}
	
	/**
	 * Retrieve the tab title
	 *
	 * @return string
	 */
	public function getTabTitle()
	{
		return $this->getTabLabel();
	}
	
	/**
	 * Retrieve the tab label
	 *
	 * @return string
	 */
	public function getTabLabel()
	{
		return $this->_getData('tab_label');
	}
	
	/**
	 * Determine whether to skip generate content
	 *
	 * @return bool
	 */
	public function getSkipGenerateContent()
	{
		return true;
	}
	
	/**
	 * Retrieve the tab class name
	 *
	 * @return string
	 */
	public function getTabClass()
	{
		if ($this->getSkipGenerateContent()) {
			return 'ajax';
		}
		
		return parent::getTabClass();
	}
	
	/**
	 * Retrieve the URL used to load the tab content
	 *
	 * @return string
	 */
	public function getTabUrl()
	{
		if ($tabUrl = $this->_getData('tab_url')) {
			return $this->getUrl($tabUrl);
		}
		
		return '#';
	}
	
	/**
	 * Legacy fix that stops the HTML output from displaying
	 *
	 * @param string $fileName
	 * @return string
	 */
    public function fetchView($fileName)
    {
    	return is_file($fileName)
    		? parent::fetchView($fileName)
    		: '';
    }
    
    /**
     * Shuffle an array and preserve the keys
     *
     * @param array $a
     * @return array
     */
	public function shuffleArray(array $a)
	{
		$keys = array_keys($a); 
		
		shuffle($keys); 

		$random = array(); 
		
		foreach ($keys as $key) { 
			$random[$key] = $a[$key]; 
		}
		
		return $random; 
	} 
}

	// End of compilation fix
	}
	