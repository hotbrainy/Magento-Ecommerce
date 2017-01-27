<?php
/**
 * @project     Notification
 * @package	    LitExtension_Notification
 * @author      LitExtension
 * @email       litextension@gmail.com
 */

class LitExtension_Notification_Model_Extensions{

    const URI_EXTENSIONS = "http://litextension.com/feeds/extensions.xml";

    public function getFrequency(){
        return Mage::getStoreConfig('le_extension/feed/frequency') * 3600;
    }

    public function check()
    {
        if (!(Mage::app()->loadCache('le_notifications_extensions')) || (time() - Mage::app()->loadCache('le_notifications_extensions_lastcheck')) > $this->getFrequency()) {
            $this->checkUpdate();
        }
    }

    public function getFeedData()
    {
        $curl = new Varien_Http_Adapter_Curl();
        $curl->setConfig(array(
            'timeout' => 1
        ));
        $curl->write(Zend_Http_Client::GET, self::URI_EXTENSIONS, '1.0');
        $data = $curl->read();
        if ($data === false) {
            return false;
        }
        $data = preg_split('/^\r?$/m', $data, 2);
        $data = trim($data[1]);
        $curl->close();

        try {
            $xml = new SimpleXMLElement($data);
        }
        catch (Exception $e) {
            return false;
        }

        return $xml;
    }

    protected function _getXmlFromUrl(){
        $response_xml_data = file_get_contents(self::URI_EXTENSIONS);
        if($response_xml_data){
            libxml_use_internal_errors(true);
            $data = simplexml_load_string($response_xml_data);
            if (!$data) {
                return false;
            } else {
                return $data;
            }
        } else {
            return false;
        }
    }

    protected function getFeedDataChoose(){
        if($this->getFeedData()){
            return $this->getFeedData();
        } else {
            return $this->_getXmlFromUrl();
        }
    }

    public function checkUpdate(){
        $exts = array();
        try {
            $feedXml = $this->getFeedDataChoose();
//            $feedXml = $this->getFeedData();
            if ($feedXml) {
                foreach ($feedXml->children() as $row) {
                    $exts[(string)$row->extension_code] = array(
                        'name' => (string)$row->name,
                        'version' => (string)$row->version,
                        'url' => (string)$row->link
                    );
                }
                Mage::app()->saveCache(serialize($exts), 'le_notifications_extensions');
                Mage::app()->saveCache(time(), 'le_notifications_extensions_lastcheck');
            }
            return true;
        } catch (Exception $E) {
            return false;
        }
    }

}