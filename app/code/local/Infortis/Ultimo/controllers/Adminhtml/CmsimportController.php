<?php

class Infortis_Ultimo_Adminhtml_CmsimportController extends Mage_Adminhtml_Controller_Action
{
	public function indexAction()
	{
		$this->getResponse()->setRedirect($this->getUrl("adminhtml/system_config/edit/section/ultimo/"));
	}
	
	public function blocksAction()
	{
		$overwrite = Mage::helper('ultimo')->getCfg('install/overwrite_blocks');
		Mage::getSingleton('ultimo/import_cms')->importCmsItems('cms/block', 'blocks', $overwrite);
		
		$this->getResponse()->setRedirect($this->getUrl("adminhtml/system_config/edit/section/ultimo/"));
	}
	
	public function pagesAction()
	{
		$overwrite = Mage::helper('ultimo')->getCfg('install/overwrite_pages');
		Mage::getSingleton('ultimo/import_cms')->importCmsItems('cms/page', 'pages', $overwrite);
		
		$this->getResponse()->setRedirect($this->getUrl("adminhtml/system_config/edit/section/ultimo/"));
	}
}
