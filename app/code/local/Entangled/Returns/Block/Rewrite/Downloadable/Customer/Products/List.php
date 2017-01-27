<?php

class Entangled_Returns_Block_Rewrite_Downloadable_Customer_Products_List extends Mage_Downloadable_Block_Customer_Products_List {

    /**
     * @return Mage_Downloadable_Block_Customer_Products_List
     */
    protected function _prepareLayout()
    {
        call_user_func(array(get_parent_class(get_parent_class($this)), '_prepareLayout'));
        $resource = $this->getItems()->getResource();
        /** @var Zend_Db_Select $select */
        $select = $this->getItems()->getSelect();
        $select->join( array('order_item'=> $resource->getTable("sales/order_item")), 'order_item.item_id = main_table.order_item_id', array("base_row_invoiced","name","order_id"));
        $select->order($this->getSort()." ".$this->getDir());

        $this->getItems()->load();
        $relatedFiles = array();
        foreach ($this->getItems() as $item) {
            $item->setPurchased($this->getPurchased()->getItemById($item->getPurchasedId()));
            if(!isset($relatedFiles[$item->getOrderItemId()])){
                $relatedFiles[$item->getOrderItemId()] = array();
            }

            $fileType = explode(".", $item->getLinkFile())[1];
            $relatedFiles[$item->getOrderItemId()][$fileType] = $item;
        }
        foreach ($this->getItems() as $item) {
            $item->setRelatedFiles($relatedFiles[$item->getOrderItemId()]);
        }

        return $this;
    }

    public function isReturnable($item){
        $order = Mage::getModel("sales/order")->load($item->getPurchased()->getOrderId());

        $orderTimestamp = Mage::getModel('core/date')->timestamp(strtotime($order->getCreatedAt())); //Magento's timestamp function makes a usage of timezone and converts it to timestamp
        $currentTimestamp = Mage::getModel('core/date')->timestamp(); //Magento's timestamp function makes a usage of timezone and converts it to timestamp
        $date = date('Y-m-d', $currentTimestamp);
        $finishAt = strtotime("-30 days",$currentTimestamp);

        return $finishAt < $orderTimestamp;
    }

    public function hasDevices(){
        $customer = Mage::getSingleton("customer/session")->getCustomer();

        $devices = Mage::getModel('ebookdelivery/devices')->getCollection();
        $devices->addFieldToFilter("customer_id",$customer->getId());

        return (bool)$devices->count();
    }

    public function getKindleUrl($item){
        return $this->getUrl("ebookdelivery/devices/send",array("id"=>$item->getId()));
    }

    public function getSortUrl($attribute){
        $dir = ($this->getSort() == $attribute && $this->getDir() == "asc") ? "desc" : "asc";
        return $this->getUrl("*/*/*",array("sort"=>$attribute,"dir"=>$dir));
    }

    public function getSort(){
        return $this->getRequest()->getParam("sort","order_id");
    }

    public function getDir(){
        return $this->getRequest()->getParam("dir","desc");
    }

}