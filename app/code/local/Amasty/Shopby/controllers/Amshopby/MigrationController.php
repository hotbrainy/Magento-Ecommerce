<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */
class Amasty_Shopby_Amshopby_MigrationController extends Mage_Adminhtml_Controller_Action
{
    // show grid
    public function indexAction()
    {
        $this->loadLayout();
        $this->_setActiveMenu('catalog/amshopby');
        $this->_addBreadcrumb($this->__('Migrations'), $this->__('Migrations'));
        $this->_addContent($this->getLayout()->createBlock('amshopby/adminhtml_migrations')->setTemplate('amasty/amshopby/migrations.phtml'));
        $this->_title($this->__('Improved Navigation Migrations'));
        $this->_getSession()->addNotice('Please flush cache BEFORE analyzing script state');
        $this->renderLayout();
    }

    public function fixAction()
    {
        $version = $this->getRequest()->getParam('version');

        if ($version) {
            /** @var Mage_Core_Model_Resource $resource */
            $resource = Mage::getSingleton('core/resource');
            $connection = $resource->getConnection('core_read');
            $table = $resource->getTableName('core/resource');
            $connection->update($table, array('version' => $version, 'data_version' => $version), 'code = "amshopby_setup"');
            $this->_getSession()->addWarning('amshopby_setup resource version has been set to ' . $version);
        }

        $this->_redirect('*/*');
    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('catalog/amshopby/migrations');
    }
}
