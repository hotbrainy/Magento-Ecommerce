<?php
class SteveB27_EbookDelivery_Model_Observer {
	
	const ORDER_ATTRIBUTE_FHC_ID = 'ebookdelivery';
	
	public function hookToOrderSaveEvent() {
		if (Mage::helper('ebookdelivery')->isEnabled()) {
            $incrementId = Mage::getSingleton('checkout/session')->getLastRealOrderId();
            $order = Mage::getModel("sales/order")->loadByIncrementId($incrementId);
			$customerId = $order->getCustomerId();
            $deliveryDevices = $this->_getDeliveryDevices($customerId);

            $downloadPurchases = Mage::getModel('downloadable/link_purchased')->getCollection()->addFieldToFilter('order_increment_id', $incrementId);
			$links = array();
			foreach($downloadPurchases as $purchase) {
                $links = Mage::getModel('downloadable/link_purchased_item')->getCollection()->addFieldToFilter('order_item_id', $purchase->getOrderItemId());

                foreach($deliveryDevices as $deliveryDevice){
                    $helper = 'ebookdelivery/'.$deliveryDevice['device_type'];
                    Mage::helper($helper)->deliver($deliveryDevice['device_email'],$links);
                }
            }

			$order->setDeliveryDevices(serialize($deliveryDevices));
			$order->save();
            Mage::getSingleton('core/session')->setSteveB27EbookDelivery("");
            Mage::getSingleton('core/session')->setSteveB27EbookDeliveryNew("");
		}
	}

	protected function _getDeliveryDevices($customerId){
        // Fetch the data
        $devices = null;
        $devices = unserialize(Mage::getSingleton('core/session')->getSteveB27EbookDelivery());

        foreach($devices as $key => $deviceData) {
            if(array_key_exists('id',$deviceData)) {
                //get device info from model
                $device = Mage::getModel('ebookdelivery/devices')->load((int)$deviceData['id']);
                $devices[$key] = array(
                    'device_type'		=> $device->getDeviceType(),
                    'device_nickname'	=> $device->getDeviceNickname(),
                    'device_email'		=> $device->getDeviceEmail(),
                );
                $deviceData = $devices[$key];
            } elseif($deviceData["device_nickname"] && $deviceData["device_email"]) {
                if($customerId) {
                    $deviceData['customer_id'] = $customerId;
                    $device = Mage::getModel('ebookdelivery/devices');
                    $device->setData($deviceData);
                    $device->save();
                }
            }else{
                continue;
            }
        }

        return $devices;
    }

	public function controllerActionPredispatchOnestepcheckoutIndexIndex(Varien_Event_Observer $observer){

        if (Mage::app()->getRequest()->isPost()) {

            $newDevicesParam = Mage::app()->getRequest()->getPost('delivery', "");
            $existingDevices = Mage::app()->getRequest()->getPost('existing_devices', array());
            $devices = array();
            foreach ($newDevicesParam as $key => $device) {
                foreach ($device as $device_type => $device_fields) {
                    foreach ($device_fields as $field => $value) {
                        $devices[$key]['device_type'] = $device_type;
                        $devices[$key][$field] = $value;
                    }
                }
            }
            foreach ($existingDevices as $deviceField => $deviceType) {
                foreach ($deviceType as $field => $value) {
                    $newDevice['device_type'] = $deviceField;
                    $newDevice['id'] = $value;
                    $devices[] = $newDevice;
                }
            }

            Mage::getSingleton('core/session')->setSteveB27EbookDelivery(serialize($devices));
        }
    }
}