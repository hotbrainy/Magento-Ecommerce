<?php
/**
 * @category    Fishpig
 * @package     Fishpig_AttributeSplash
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_AttributeSplash_Block_Adminhtml_Page_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
	public function __construct()
	{
		parent::__construct();
		
		$this->setId('splash_page_grid');
		$this->setDefaultSort('page_id');
		$this->setDefaultDir('asc');
		$this->setSaveParametersInSession(false);
		$this->setUseAjax(true);
	}

	/**
	 * Insert the Add New button
	 *
	 * @return $this
	 */
	protected function _prepareLayout()
	{
		$this->setChild('add_button',
			$this->getLayout()->createBlock('adminhtml/widget_button')
				->setData(array(
					'label'     => Mage::helper('adminhtml')->__('Add New Page'),
					'class' => 'add',
					'onclick'   => "setLocation('" . $this->getUrl('*/attributeSplash_page/new') . "');",
				))
		);
				
		return parent::_prepareLayout();
	}
	
	/**
	 * Retrieve the main buttons html
	 *
	 * @return string
	 */
	public function getMainButtonsHtml()
	{
		return parent::getMainButtonsHtml()
			. $this->getChildHtml('add_button');
	}

	/**
	 * Initialise and set the collection for the grid
	 *
	 */
	protected function _prepareCollection()
	{
		$this->setCollection(
			Mage::getResourceModel('attributeSplash/page_collection')
		);
	
		return parent::_prepareCollection();
	}

	/**
	 * Add store information to pages
	 *
	 * @return $this
	 */
	protected function _afterLoadCollection()
	{
		$this->getCollection()->walk('afterLoad');

		parent::_afterLoadCollection();
	}
	
	/**
	 * Apply the store filter
	 *
	 * @param $collection
	 * @param $column
	 * @return void
	 */
    protected function _filterStoreCondition($collection, $column)
    {
        if (!$value = $column->getFilter()->getValue()) {
            return;
        }

        $this->getCollection()->addStoreFilter($value);
    }
    	
	/**
	 * Add the columns to the grid
	 *
	 */
	protected function _prepareColumns()
	{
		$this->addColumn('page_id', array(
			'header'	=> $this->__('ID'),
			'align'		=> 'right',
			'width'     => 1,
			'index'		=> 'page_id',
		));

		$this->addColumn('attribute_id', array(
			'header'		=> $this->__('Attribute'),
			'align'			=> 'left',
			'index'			=> 'attribute_id',
			'filter_index' 	=> '_attribute_table.attribute_id',
			'type'			=> 'options',
			'options' 		=> Mage::getSingleton('attributeSplash/system_config_source_attribute_splashed')->setLabelField('attribute_code')->toOptionHash(),
		));
		
		$this->addColumn('display_name', array(
			'header'	=> $this->__('Name'),
			'align'		=> 'left',
			'index'		=> 'display_name',
		));
		
		$this->addColumn('url_key', array(
			'header'	=> $this->__('URL Key'),
			'align'		=> 'left',
			'index'		=> 'url_key',
		));

		if (!Mage::app()->isSingleStoreMode()) {
			$this->addColumn('store_ids', array(
				'header'	=> $this->__('Store'),
				'align'		=> 'left',
				'index'		=> 'store_ids',
                'type'          => 'store',
                'store_all'     => true,
                'store_view'    => true,
                'sortable'      => false,
                'filter_condition_callback' => array($this, '_filterStoreCondition'),
				'options' 	=> $this->getStores(),
			));
		}

		
		$this->addColumn('is_enabled', array(
			'width'     => 1,
			'header'	=> $this->__('Enabled'),
			'index'		=> 'is_enabled',
			'type'		=> 'options',
			'options'	=> array(
				1 => $this->__('Enabled'),
				0 => $this->__('Disabled'),
			),
		));
	
		$this->addColumn('action', array(
			'type'      => 'action',
			'getter'     => 'getId',
			'actions'   => array(array(
				'caption' => Mage::helper('catalog')->__('Edit'),
				'url'     => array(
				'base'=>'*/attributeSplash_page/edit',
				),
				'field'   => 'id'
			)),
			'filter'    => false,
			'sortable'  => false,
			'align' 	=> 'center',
		));

		return parent::_prepareColumns();
	}

	protected function _prepareMassaction()
	{
		$this->setMassactionIdField('page_id');
		$this->getMassactionBlock()->setFormFieldName('page');
	
		$this->getMassactionBlock()->addItem('delete', array(
			'label'=> $this->__('Delete'),
			'url'  => $this->getUrl('*/attributeSplash_page/massDelete'),
			'confirm' => Mage::helper('catalog')->__('Are you sure?')
		));
	}
	
	/**
	 * Retrieve the URL used to modify the grid via AJAX
	 *
	 * @return string
	 */
	public function getGridUrl()
	{
		return $this->getUrl('*/*/pageGrid');
	}
	
	/**
	 * Retrieve the URL for the row
	 *
	 */
	public function getRowUrl($row)
	{
		return $this->getUrl('*/attributeSplash_page/edit', array('id' => $row->getId()));
	}
	
	/**
	 * Retrieve an array of all of the stores
	 *
	 * @return array
	 */
	protected function getStores()
	{
		$options = array(0 => $this->__('Global'));
		$stores = Mage::getResourceModel('core/store_collection')->load();
		
		foreach($stores as $store) {
			$options[$store->getId()] = $store->getWebsite()->getName() . ' &gt; ' . $store->getName();
		}

		return $options;
	}
}
