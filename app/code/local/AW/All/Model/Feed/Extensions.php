<?php
/**
 * aheadWorks Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://ecommerce.aheadworks.com/LICENSE-M1.txt
 *
 * @category   AW
 * @package    AW_All
 * @copyright  Copyright (c) 2009-2010 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/LICENSE-M1.txt
 */

class AW_All_Model_Feed_Extensions extends AW_All_Model_Feed_Abstract
{
    protected $_extensions = array(
        'AW_Featuredproducts' => 'http://confluence.aheadworks.com/display/EUDOC/Featured+Products+2',
        'AW_Searchautocomplete' => 'http://confluence.aheadworks.com/display/EUDOC/Search+Autocomplete+and+Suggest',
        'AW_PQUESTION2' => 'http://confluence.aheadworks.com/display/EUDOC/Product+Questions+2',
        'AW_Helpdesk' => 'http://confluence.aheadworks.com/display/EUDOC/Help+Desk+Ultimate',
        'AW_Zblocks' => 'http://confluence.aheadworks.com/display/EUDOC/Z-Blocks',
        'AW_Aheadvideo' => 'http://confluence.aheadworks.com/display/EUDOC/Video+Module',
        'AW_Rssreader' => 'http://confluence.aheadworks.com/display/EUDOC/RSS+Reader',
        'AW_Advancedmenu' => 'http://confluence.aheadworks.com/display/EUDOC/Advanced+Menu',
        'AW_Helpdesk3' => 'http://confluence.aheadworks.com/display/EUDOC/Help+Desk+Ultimate+3',
        'AW_Ajaxcartpro' => 'http://confluence.aheadworks.com/display/EUDOC/AJAX+Cart+Pro',
        'AW_Reviewrotator' => 'http://confluence.aheadworks.com/display/EUDOC/Review+Rotator',
        'AW_Relatedproducts' => 'http://confluence.aheadworks.com/display/EUDOC/Who+Bought+This+Also+Bought',
        'AW_Boughttogether' => 'http://confluence.aheadworks.com/display/EUDOC/Frequently+Bought+Together',
        'AW_Onsale' => 'http://confluence.aheadworks.com/display/EUDOC/On+Sale',
        'AW_Followupemail' => 'http://confluence.aheadworks.com/display/EUDOC/Follow+Up+Email',
        'AW_Checkoutpromo' => 'http://confluence.aheadworks.com/display/EUDOC/Checkout+Promo',
        'AW_Booking' => 'http://confluence.aheadworks.com/display/EUDOC/Booking+and+Reservations',
        'AW_Blog' => 'http://confluence.aheadworks.com/display/EUDOC/Blog',
        'AW_Gridmanager' => 'http://confluence.aheadworks.com/display/EUDOC/Grid+Manager',
        'AW_Raf' => 'http://confluence.aheadworks.com/display/EUDOC/Refer+a+Friend',
        'AW_Maprice' => 'http://confluence.aheadworks.com/display/EUDOC/Minimum+Advertised+Price',
        'AW_AdvancedReviews' => 'http://confluence.aheadworks.com/display/EUDOC/Advanced+Reviews',
        'AW_Productupdates' => 'http://confluence.aheadworks.com/display/EUDOC/Product+Updates+Notifications',
        'AW_Customerpurchases' => 'http://confluence.aheadworks.com/display/EUDOC/Customer+Purchases',
        'AW_Advancedreports' => 'http://confluence.aheadworks.com/display/EUDOC/Advanced+Reports',
        'AW_Advancednewsletter' => 'http://confluence.aheadworks.com/display/EUDOC/Advanced+Newsletter',
        'AW_Easycategories' => 'http://confluence.aheadworks.com/display/EUDOC/Easy+Categories',
        'AW_Sarp2' => 'http://confluence.aheadworks.com/display/EUDOC/Subscriptions+And+Recurring+Payments+2.x',
        'AW_Deliverydate' => 'http://confluence.aheadworks.com/display/EUDOC/Delivery+Date+and+Notice',
        'AW_Automaticcallouts' => 'http://confluence.aheadworks.com/display/EUDOC/Automatic+Product+Callouts',
        'AW_Previousnext' => 'http://confluence.aheadworks.com/pages/viewpage.action?pageId=18317983',
        'AW_Customsmtp' => 'http://confluence.aheadworks.com/display/EUDOC/Custom+SMTP',
        'AW_Hometabspro' => 'http://confluence.aheadworks.com/display/EUDOC/Home+Tabs+Pro',
        'AW_Marketsuite' => 'http://confluence.aheadworks.com/display/EUDOC/Market+Segmentation+Suite',
        'AW_Kbase' => 'http://confluence.aheadworks.com/display/EUDOC/Knowledge+Base',
        'AW_Vidtest' => 'http://confluence.aheadworks.com/display/EUDOC/Video+Testimonials',
        'AW_Ascurl' => 'http://confluence.aheadworks.com/display/EUDOC/Ultimate+SEO+Suite',
        'AW_Mobile2' => 'http://confluence.aheadworks.com/display/EUDOC/iPhone+Theme+2',
        'AW_Popup' => 'http://confluence.aheadworks.com/pages/viewpage.action?pageId=16910274',
        'AW_Rma' => 'http://confluence.aheadworks.com/display/EUDOC/RMA',
        'AW_Ordertags' => 'http://confluence.aheadworks.com/display/EUDOC/Order+Tags',
        'AW_Sociable' => 'http://confluence.aheadworks.com/display/EUDOC/Sociable',
        'AW_Ppp' => 'http://confluence.aheadworks.com/display/EUDOC/Product+Preview+Pro',
        'AW_Featured' => 'http://confluence.aheadworks.com/display/EUDOC/Featured+Products+3',
        'AW_Pmatch' => 'http://confluence.aheadworks.com/display/EUDOC/Price+Match',
        'AW_Alsoviewed' => 'http://confluence.aheadworks.com/display/EUDOC/Who+Viewed+This+Also+Viewed',
        'AW_Points' => 'http://confluence.aheadworks.com/display/EUDOC/Points+And+Rewards',
        'AW_Islider' => 'http://confluence.aheadworks.com/display/EUDOC/Product+Images+Slider',
        'AW_Goislider' => 'http://confluence.aheadworks.com/display/EUDOC/GoSlider',
        'AW_Catalogpermissions' => 'http://confluence.aheadworks.com/display/EUDOC/Catalog+Permissions',
        'AW_Ajaxcatalog' => 'http://confluence.aheadworks.com/display/EUDOC/AJAX+Catalog',
        'AW_Autorelated' => 'http://confluence.aheadworks.com/display/EUDOC/Automatic+Related+Products+2',
        'AW_Collpur' => 'http://confluence.aheadworks.com/display/EUDOC/Group+Deals',
        'AW_Advancedsearch' => 'http://confluence.aheadworks.com/display/EUDOC/Advanced+Search',
        'AW_Avail' => 'http://confluence.aheadworks.com/display/EUDOC/Custom+Stock+Status',
        'AW_Mobiletracking' => 'http://confluence.aheadworks.com/display/EUDOC/Mobile+Order+Tracking',
        'AW_Countdown' => 'http://confluence.aheadworks.com/display/EUDOC/Countdown',
        'AW_Affiliate' => 'http://confluence.aheadworks.com/display/EUDOC/Magento+Affiliate',
        'AW_Randomprice' => 'http://confluence.aheadworks.com/display/EUDOC/Random+Product+Price',
        'AW_Eventdiscount' => 'http://confluence.aheadworks.com/display/EUDOC/Event-Based+Discounts',
        'AW_Onpulse' => 'http://confluence.aheadworks.com/display/EUDOC/OnPulse+Magento+Connector',
        'AW_Admingridimages' => 'http://confluence.aheadworks.com/display/EUDOC/Admin+Grid+Thumbnail',
        'AW_Shopbybrand' => 'http://confluence.aheadworks.com/display/EUDOC/Shop+by+Brand',
        'AW_Customerattributes' => 'http://confluence.aheadworks.com/display/EUDOC/Customer+Attributes',
        'AW_Activitystream' => 'http://confluence.aheadworks.com/display/EUDOC/Activity+Stream',
        'AW_Storelocator' => 'http://confluence.aheadworks.com/display/EUDOC/Store+Locator',
        'AW_ShippingPrice' => 'http://confluence.aheadworks.com/display/EUDOC/Shipping+Price',
        'AW_Onestepcheckout' => 'http://confluence.aheadworks.com/display/EUDOC/One+Step+Checkout',
        'AW_Auction' => 'http://confluence.aheadworks.com/display/EUDOC/Auction+Pro',
        'AW_Betterthankyoupage' => 'http://confluence.aheadworks.com/display/EUDOC/Better+Thank+You+Page',
        'AW_Gamification' => 'http://confluence.aheadworks.com/display/EUDOC/eCommerce+Gamification+Suite',
        'AW_Callforprice' => 'http://confluence.aheadworks.com/display/EUDOC/Call+For+Price',
        'AW_Afptc' => 'http://confluence.aheadworks.com/display/EUDOC/Add+Free+Product+To+Cart',
        'AW_Orderattributes' => 'http://confluence.aheadworks.com/display/EUDOC/Order+Attributes',
        'AW_Ajaxlogin' => 'http://confluence.aheadworks.com/display/EUDOC/Ajax+Login+Pro',
        'AW_Giftcard' => 'http://confluence.aheadworks.com/pages/viewpage.action?pageId=16909150',
        'AW_Giftwrap' => 'http://confluence.aheadworks.com/display/EUDOC/Gift+Wrap',
        'AW_Extradownloads' => 'http://confluence.aheadworks.com/display/EUDOC/Extra+Downloads',
        'AW_Colorswatches' => 'http://confluence.aheadworks.com/display/EUDOC/Product+Color+Swatches',
        'AW_Layerednavigation' => 'http://confluence.aheadworks.com/display/EUDOC/Layered+Navigation',
        'AW_Eventbooking' => 'http://confluence.aheadworks.com/display/EUDOC/Event+Tickets',
        'AW_Storecredit' => 'http://confluence.aheadworks.com/display/EUDOC/Store+Credit+and+Refund',
        'AW_Coupongenerator' => 'http://confluence.aheadworks.com/display/EUDOC/Quick+Coupon+Generator',
    );

    /**
     * Retrieve feed url
     *
     * @return string
     */
    public function getFeedUrl()
    {
        return AW_All_Helper_Config::EXTENSIONS_FEED_URL;
    }


    /**
     * Checks feed
     * @return
     */
    public function check()
    {
        if (!(Mage::app()->loadCache('aw_all_extensions_feed')) || (time() - Mage::app()->loadCache('aw_all_extensions_feed_lastcheck')) > Mage::getStoreConfig('awall/feed/check_frequency')) {
            $this->refresh();
        }
    }

    public function refresh()
    {
        $exts = array();
        try {
            $Node = $this->getFeedData();
            if (!$Node) return false;
            foreach ($Node->children() as $ext) {
                $exts[(string)$ext->name] = array(
                    'display_name' => (string)$ext->display_name,
                    'version' => (string)$ext->version,
                    'url' => (string)$ext->url,
                    'documentation_url' => $this->getDocumentationUrl((string)$ext->name),
                );
            }

            Mage::app()->saveCache(serialize($exts), 'aw_all_extensions_feed');
            Mage::app()->saveCache(time(), 'aw_all_extensions_feed_lastcheck');
            return true;
        } catch (Exception $E) {
            return false;
        }
    }

    public function checkExtensions()
    {
        $modules = array_keys((array)Mage::getConfig()->getNode('modules')->children());
        sort($modules);

        $magentoPlatform = AW_All_Helper_Versions::getPlatform();
        foreach ($modules as $extensionName) {
            if (strstr($extensionName, 'AW_') === false) {
                continue;
            }
            if ($extensionName == 'AW_Core' || $extensionName == 'AW_All') {
                continue;
            }
            if ($platformNode = $this->getExtensionPlatform($extensionName)) {
                $extensionPlatform = AW_All_Helper_Versions::convertPlatform($platformNode);
                if ($extensionPlatform < $magentoPlatform) {
                    $this->disableExtensionOutput($extensionName);
                }
            }
        }
        return $this;
    }

    public function getExtensionPlatform($extensionName)
    {
        try {
            if ($platform = Mage::getConfig()->getNode("modules/$extensionName/platform")) {
                $platform = strtolower($platform);
                return $platform;
            } else {
                throw new Exception();
            }
        } catch (Exception $e) {
            return false;
        }
    }


    public function disableExtensionOutput($extensionName)
    {
        $coll = Mage::getModel('core/config_data')->getCollection();
        $coll->getSelect()->where("path='advanced/modules_disable_output/$extensionName'");
        $i = 0;
        foreach ($coll as $cd) {
            $i++;
            $cd->setValue(1)->save();
        }
        if ($i == 0) {
            Mage::getModel('core/config_data')
                    ->setPath("advanced/modules_disable_output/$extensionName")
                    ->setValue(1)
                    ->save();
        }
        return $this;
    }

    public function getDocumentationUrl($extensionName)
    {
        if (isset($this->_extensions[$extensionName])) {
            return $this->_extensions[$extensionName];
        }
        return 'http://confluence.aheadworks.com/display/EUDOC/';
    }

}