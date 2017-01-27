<?php

class Infortis_Ultimo_Helper_Cssgen extends Mage_Core_Helper_Abstract
{
	/**
	 * Path and directory of the automatically generated CSS
	 *
	 * @var string
	 */
	protected $_generatedCssFolder;
	protected $_generatedCssPath;
	protected $_generatedCssDir;
	protected $_templatePath;
	
	public function __construct()
	{
		//Create paths
		$this->_generatedCssFolder = 'css/_config/';
		$this->_generatedCssPath = 'frontend/ultimo/default/' . $this->_generatedCssFolder;
		$this->_generatedCssDir = Mage::getBaseDir('skin') . '/' . $this->_generatedCssPath;
		$this->_templatePath = 'infortis/ultimo/css/';
	}
	
	/**
	 * Get directory of automatically generated CSS
	 *
	 * @return string
	 */
	public function getGeneratedCssDir()
    {
        return $this->_generatedCssDir;
    }

	/**
	 * Get path to CSS template
	 *
	 * @return string
	 */
	public function getTemplatePath()
    {
        return $this->_templatePath;
    }

	/**
	 * Get file path: CSS grid
	 *
	 * @return string
	 */
	public function getGridFile()
	{
		return $this->_generatedCssFolder . 'grid_' . Mage::app()->getStore()->getCode() . '.css';
	}
	
	/**
	 * Get file path: CSS layout
	 *
	 * @return string
	 */
	public function getLayoutFile()
	{
		return $this->_generatedCssFolder . 'layout_' . Mage::app()->getStore()->getCode() . '.css';
	}
	
	/**
	 * Get file path: CSS design
	 *
	 * @return string
	 */
	public function getDesignFile()
	{
		return $this->_generatedCssFolder . 'design_' . Mage::app()->getStore()->getCode() . '.css';
	}
}
