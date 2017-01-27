<?php

class Idev_OneStepCheckout_Model_Observers_Deliverydate
{

    protected $_isactive = "";

    /**
     * Check if OSC is active
     *
     * @return int;
     */
    public function isActive()
    {
        if ($this->_isactive === "") {
            $this->_isactive = (int) Mage::getStoreConfig('onestepcheckout/general/rewrite_checkout_links');
        }
        return $this->_isactive;
    }

    /**
     * Mediate the frontend form to quote
     *
     * @param unknown $observer
     */
    public function setDeliverydate($observer)
    {
        if(!$this->isActive()) {
            return;
        }

        $quote = $observer->getEvent()->getQuote();
        $data = Mage::app()->getRequest()->getPost('deliverydate', array());
        $this->saveDeliverydate($data, $quote);

    }

    /**
     * Mediate the backend form to quote
     *
     * @param unknown $observer
     */
    public function setDeliverydateAdmin($observer)
    {
        if (! $this->isActive()) {
            return;
        }
        $quote = $observer->getEvent()->getQuote();
        $data = Mage::app()->getRequest()->getPost('deliverydate', array());
        $this->saveDeliverydate($data, $quote);
        $quote->save();

        $order = $observer->getEvent()->getOrder();
        $data = Mage::app()->getRequest()->getPost('deliverydate', array());
        $this->saveDeliverydate($data, $order);
        $order->save();
    }

    /**
     * save deliverydate data to queue
     *
     * @param array $data
     * @param Mage_Sales_Model_Quote $obj
     */
    public function saveDeliverydate($data = array(), $obj)
    {
        if (! $this->isActive()) {
            return;
        }

        $whitelist = array();

        $enabled = Mage::getStoreConfig('onestepcheckout/delivery/enabled_date');
        if ($enabled) {
            $whitelist['date'] = 'iosc_ddate';
        }

        $enabled = Mage::getStoreConfig('onestepcheckout/delivery/enabled_slot');
        if ($enabled) {
            $whitelist['slot'] = 'iosc_ddate_slot';
        }

        $enabled = Mage::getStoreConfig('onestepcheckout/delivery/enabled_note');
        if ($enabled) {
            $whitelist['note'] = 'iosc_dnote';
        }

        if (! empty($data) && ! empty($whitelist)) {
            foreach ($data as $k => $v) {
                if (! empty($whitelist[$k])) {
                    if ($k === 'date') {
                        $v = strtotime(str_replace('/', '-', $v));
                        if (! $v) {
                            $v = '';
                        }
                    } else {
                        $v = Mage::helper('core')->escapeHtml($v);
                    }
                    $obj->setData($whitelist[$k], $v);
                }
            }
        }
    }

    /**
     * get and display deliverydate data or form in admin
     *
     * @param unknown $observer
     */
    public function getDeliverydate($observer)
    {

        // adding display to order view
        if ($observer->getEvent()->getBlock() instanceof Mage_Adminhtml_Block_Sales_Order_View_Tab_Info) {
            $block = $observer->getEvent()
                ->getBlock()
                ->getLayout()
                ->createBlock('core/template');
            $block->setTemplate('onestepcheckout/deliverydate.phtml')->setOrder($observer->getEvent()
                ->getBlock()
                ->getOrder());
            $html = $observer->getEvent()
                ->getTransport()
                ->getHtml();
            $observer->getEvent()
                ->getTransport()
                ->setHtml($html . $block->toHtml());
        }

        if(!$this->isActive()) {
            return;
        }
        // adding a form to admin order create

        if ($observer->getEvent()->getBlock() instanceof Mage_Adminhtml_Block_Sales_Order_Create_Shipping_Method) {
            $block = $observer->getEvent()
                ->getBlock()
                ->getLayout()
                ->createBlock('core/template');
            $block->setTemplate('onestepcheckout/deliverydate_form.phtml');
            $html = $observer->getEvent()
                ->getTransport()
                ->getHtml();
            $observer->getEvent()
                ->getTransport()
                ->setHtml($html . $block->toHtml());
        }
    }
}
