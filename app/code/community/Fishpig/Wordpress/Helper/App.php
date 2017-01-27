<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Wordpress_Helper_App extends Fishpig_Wordpress_Helper_Abstract
{
	/**
	 * App errors that occur while integrating
	 *
	 * @var array
	 */	
	protected $_errors = array();
	
	/**
	 * DB connection with WordPress
	 *
	 * @var
	 */
	static protected $_db = null;
	
	/**
	 * Array of post type data
	 *
	 * @var array
	 */
	static protected $_postTypes = null;

	/**
	 * Array of taxonomy data
	 *
	 * @var array
	 */
	static protected $_taxonomies = null;
	
	/**
	 * Holds the current store
	 * Determines correct store if Admin
	 *
	 * @var Mage_Core_Model_Store
	 */
	static protected $_store = null;
	
	/**
	 * Blog ID. This is taken from Fishpig_Wordpress_Addon_Multisite
	 *
	 * @var int
	 */
	static protected $_blogId = 1;

	/**
	 * Content taken from WordPress to be injected into Magento
	 *
	 * @var array
	 */	
	protected $_contentHolder = array();

	public function __construct()
	{
		/**
		 * Setup the real store
		 */
		$this->getStore();

		/**
		 * Set the blog ID
		 * This is taken from Fishpig_Wordpress_Addon_Multisite
		 */
		if ($blogId = (int)Mage::getStoreConfig('wordpress/mu/blog_id', $this->getStore()->getId())) {
			self::$_blogId = $blogId;
		}
		
		/**
		 * Initialise the DB
		 */
		$this->_initDb();
	}
	
	/**
	 * Initialise the post type and taxonomy data
	 *
	 * @return $this
	 */
	public function init()
	{
		$this->_initPostTypes();
		$this->_initTaxonomies();

		return $this;
	}
	
	/**
	 * Initialise the DB
	 *
	 * @return $this
	 */
	protected function _initDb()
	{
		if (!is_null(self::$_db)) {
			return $this;
		}
		
		self::$_db = false;
		
		/**
		  * Before connecting to the database
		  * Map the WordPress table names with the table prefix
		  */
		$wordpressEntities = (array)Mage::app()->getConfig()->getNode()->wordpress->database->before_connect->tables;
		$tablePrefix = $this->getTablePrefix();

		foreach($wordpressEntities as $entity => $table) {
			Mage::getSingleton('core/resource')->setMappedTableName((string)$table->table, $tablePrefix . $table->table);
		}

		if ($this->getBlogId() > 1) {
			$networkTablePrefix = $this->getTablePrefix() . $this->getBlogId() . '_';

			$entities = (array)Mage::app()->getConfig()->getNode()->wordpress->database->before_connect->tables_mu;

			foreach($entities as $entity => $table) {
				Mage::getSingleton('core/resource')->setMappedTableName((string)$table->table, $networkTablePrefix . $table->table);
			}
		}

		if (!Mage::getStoreConfigFlag('wordpress/database/is_shared', $this->getStore()->getId())) {
			// If database not shared, connect to WP database
			$configs = array('model' => 'mysql4', 'active' => '1', 'host' => '', 'username' => '', 'password' => '', 'dbname' => '', 'charset' => 'utf8');
		
			foreach($configs as $key => $defaultValue) {
				if ($value = Mage::getStoreConfig('wordpress/database/' . $key, $this->getStore()->getId())) {
					$configs[$key] = $value;
				}
			}

			foreach(array('username', 'password', 'dbname') as $field) {
				if (isset($configs[$field])) {
					$configs[$field] = Mage::helper('core')->decrypt($configs[$field]);
				}
			}
		
			if (!isset($configs['host']) || !$configs['host']) {
				return $this->addError('Database host not defined.');
			}
			
			try {
				$connection = Mage::getSingleton('core/resource')->createConnection('wordpress', 'pdo_mysql', $configs);
			
				if (!is_object($connection)) {
					return $this;
				}
				
				$connection->getConnection();
				
				if (!$connection->isConnected()) {
					return $this->addError('Unable to connect to WordPress database.');
				}
			}
			catch (Exception $e) {
				return $this->addError($e->getMessage());
			}
			
			$db = $connection;
		}
		else {
			$db = Mage::getSingleton('core/resource')->getConnection('core_read');
		}

		try {
			$db->fetchOne(
				$db->select()->from(Mage::getSingleton('core/resource')->getTableName('wordpress/post'), 'ID')->limit(1)
			);
		}
		catch (Exception $e) {
			return $this->addError($e->getMessage())
				->addError(sprintf('Unable to query WordPress database. Is the table prefix (%s) correct?', $tablePrefix));
		}

		$db->query('SET NAMES UTF8');

		$wordpressEntities = (array)Mage::app()->getConfig()->getNode()->wordpress->database->after_connect->tables;

		foreach($wordpressEntities as $entity => $table) {
			Mage::getSingleton('core/resource')->setMappedTableName((string)$table->table, $tablePrefix . $table->table);
		}
		
		self::$_db = $db;
		
		return $this;
	}

	/**
	 * Initialise the post type data
	 *
	 * @return $this
	 */
	protected function _initPostTypes()
	{
		if (!is_null(self::$_postTypes)) {
			return $this;	
		}

		self::$_postTypes = false;

		$transportObject = new Varien_Object(array('post_types' => false));
		
		Mage::dispatchEvent('wordpress_app_init_post_types', array('transport' => $transportObject, 'helper' => $this));

		if ($transportObject->getPostTypes()) {
			self::$_postTypes = $transportObject->getPostTypes();
		}
		else {
			self::$_postTypes = array(
				'post' => Mage::getModel('wordpress/post_type')->setData(array(
					'post_type' => 'post',
					'rewrite' => array('slug' => $this->getWpOption('permalink_structure')),
					'taxonomies' => array('category', 'post_tag'),
					'_builtin' => true,
				)),
				'page' => Mage::getModel('wordpress/post_type')->setData(array(
					'post_type' => 'page',
					'rewrite' => array('slug' => '%postname%/'),
					'hierarchical' => true,
					'taxonomies' => array(),
					'_builtin' => true,
				))
			);
		}
		
		$transportObject = new Varien_Object(array('post_types' => self::$_postTypes));
		
		Mage::dispatchEvent('wordpress_app_init_post_types_after', array('transport' => $transportObject, 'helper' => $this));
		
		self::$_postTypes = $transportObject->getPostTypes();

		return $this;
	}
	
	/**
	 * Get the DB connection
	 *
	 * @return 
	 */	
	public function getDbConnection()
	{
		return self::$_db;
	}
	
	/**
	 * Get the post type array
	 *
	 * @return array
	 */
	public function getPostTypes()
	{
		$this->init();
		
		return self::$_postTypes;
	}
	
	/**
	 * Get a single post type
	 *
	 * @param string $type
	 * @return Fishpig_Wordpress_Model_Post_Type|false
	 */
	public function getPostType($type)
	{
		$this->init();
		
		return isset(self::$_postTypes[$type])
			? self::$_postTypes[$type]
			: false;
	}

	/**
	 * Get the taxonomy array
	 *
	 * @return array
	 */
	public function getTaxonomies()
	{
		$this->init();
		
		return self::$_taxonomies;
	}
	
	/**
	 * Get a taxonomy
	 *
	 * @param string $taxonomy
	 * @return Fishpig_Wordpress_Model_Term_Taxonomy|false
	 */
	public function getTaxonomy($taxonomy)
	{
		$this->init();
		
		return isset(self::$_taxonomies[$taxonomy])
			? self::$_taxonomies[$taxonomy]
			: false;
	}
	
	/**
	 * Initialise the taxonomy data
	 *
	 * @return $this
	 */
	protected function _initTaxonomies()
	{
		if (!is_null(self::$_taxonomies)) {
			return $this;
		}
		
		self::$_taxonomies = false;
					
		$transportObject = new Varien_Object(array('taxonomies' => false));
		
		Mage::dispatchEvent('wordpress_app_init_taxonomies', array('transport' => $transportObject));
		
		if ($transportObject->getTaxonomies()) {
			self::$_taxonomies = $transportObject->getTaxonomies();
		}
		else {
			$blogPrefix = Mage::helper('wordpress')->isWordPressMU()
				 && Mage::helper('wp_addon_multisite')->canRun()
				 && Mage::helper('wp_addon_multisite')->isDefaultBlog();

			$bases = array(
				'category' => Mage::helper('wordpress')->getWpOption('category_base'),
				'post_tag' => Mage::helper('wordpress')->getWpOption('tag_base'),
			);
			
			foreach($bases as $baseType => $base) {
				if ($blogPrefix && $base && strpos($base, '/blog') === 0) {
					$bases[$baseType] = substr($base, strlen('/blog'));	
				}
			}

			self::$_taxonomies = array(
				'category' => Mage::getModel('wordpress/term_taxonomy')->setData(array(
					'type' => 'category',
					'taxonomy_type' => 'category',
					'labels' => array(
						'name' => 'Categories',
						'singular_name' => 'Category',
					),
					'public' => true,
					'hierarchical' => true,
					'rewrite' => array(
						'hierarchical' => true,
						'slug' => $bases['category'],
					),
					'_builtin' => true,
				)),
				'post_tag' => Mage::getModel('wordpress/term_taxonomy')->setData(array(
					'type' => 'post_tag',
					'taxonomy_type' => 'post_tag',
					'labels' => array(
						'name' => 'Tags',
						'singular_name' => 'Tag',
					),
					'public' => true,
					'hierarchical' => false,
					'rewrite' => array(
						'slug' => $bases['post_tag'],
					),
					'_builtin' => true,
				))
			);
		}

		if (isset(self::$_taxonomies['category'])) {
			$helper = Mage::helper('wordpress');
			
			$canRemoveCategoryPrefix = $helper->isPluginEnabled('wp-no-category-base/no-category-base.php')
				|| $helper->isPluginEnabled('wp-remove-category-base/wp-remove-category-base.php')
				|| $helper->isPluginEnabled('remove-category-url/remove-category-url.php')
				|| Mage::helper('wp_addon_wordpressseo')->canRemoveCategoryBase();
			
			if ($canRemoveCategoryPrefix) {
				self::$_taxonomies['category']->setSlug('');
			}
		}
		
		$transportObject = new Varien_Object(array('taxonomies' => self::$_taxonomies));
		
		Mage::dispatchEvent('wordpress_app_init_taxonomies_after', array('transport' => $transportObject, 'helper' => $this));
		
		self::$_taxonomies = $transportObject->getTaxonomies();

		return $this;
	}

	/*
	 * Returns the table prefix used by Wordpress
	 *
	 * @return string
	 */
	public function getTablePrefix()
	{
		return Mage::getStoreConfig('wordpress/database/table_prefix', $this->getStore()->getId());
	}
	
	/**
	 * Get the blog ID
	 *
	 * @return int
	 */
	public function getBlogId()
	{
		return self::$_blogId;
	}
	
	/**
	 * Add an error message to the inernal errors array
	 *
	 * @param string $msg
	 * @return $this
	 */
	public function addError($msg)
	{
		$this->_errors[] = $msg;
		
		return $this;
	}
	
	/**
	 * Get a table name
	 *
	 * @param string $entity
	 * @return string
	 */
	public function getTableName($entity)
	{
		return Mage::getSingleton('core/resource')->getTableName($entity);
	}
	
	/**
	 * Get the current store
	 *
	 * @return Mage_Core_Model_Store
	 */
	public function getStore()
	{
		if (self::$_store === false) {
			return Mage::app()->getStore();
		}
		
		self::$_store = Mage::app()->getStore();

		if (Mage::app()->getStore()->getCode() === 'admin') {
			$storeValue = Mage::app()->getRequest()->getParam('store', false);

			$store = Mage::getModel('core/store')->load($storeValue, (int)$storeValue > 0 ? null : 'code');

			if ($store->getId()) {
				self::$_store = $store;
			}
			else {
				self::$_store = $this->getDefaultStore(Mage::app()->getRequest()->getParam('website', null));
			}
			
			if (!self::$_store) {
				self::$_store = Mage::app()->getStore();
			}
		}
		
		return self::$_store;
	}	
	
	/**
	 * Add content from WP to the Magento footer
	 *
	 * @param string $content
	 * @return $this
	 */
	public function addWordPressContentToFooter($content)
	{
		if (!is_array($content)) {
			if ($content === false || $content === '') {
				return $this;
			}
		}
		else {
			$content = implode("\n", $content);
		}
		
		$key = md5(trim($content));
		
		if (!isset($this->_contentHolder[$key])) {
			$this->_contentHolder[$key] = $content;
		}
		
		return $this;
	}
	
	public function getWordPressContent()
	{
		if (count($this->_contentHolder) === 0) {
			return false;
		}

		$content = $this->_contentHolder;
		$head = $this->getLayout()->getBlock('head');
		$jsTemplate = '<script type="text/javascript" src="%s"></script>';

		if (!$head || strpos(implode(',', array_keys($head->getItems())), 'jquery') === false) {
			if (!$head || strpos(implode(',', array_keys($headBlock->getItems())), 'underscore') === false) {
				array_shift($content, sprintf($jsTemplate, $helper->getBaseUrl('wp-includes/js/underscore.min.js?ver=1.6.0')));
			}
			
			array_shift($content, sprintf($jsTemplate, $helper->getBaseUrl('wp-includes/js/jquery/jquery-migrate.min.js?ver=1.2.1')));			
			array_shift($content, sprintf($jsTemplate, $helper->getBaseUrl('wp-includes/js/jquery/jquery.js?ver=1.11.3')));
		}

		return implode("\n", $content);
	}
}
