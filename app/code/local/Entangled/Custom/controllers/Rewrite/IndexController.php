<?php
require_once "Mage/Wishlist/controllers/IndexController.php";

class Entangled_Custom_Rewrite_IndexController extends Mage_Wishlist_IndexController  {

    /**
     * Adding new item
     *
     * @return Mage_Core_Controller_Varien_Action|void
     */
    public function addAction()
    {
        $this->_addItemToWishList();
    }

    /**
     * Add the item to wish list
     *
     * @return Mage_Core_Controller_Varien_Action|void
     */
    protected function _addItemToWishList()
    {
        $wishlist = $this->_getWishlist();
        if (!$wishlist) {
            return $this->norouteAction();
        }

        $session = Mage::getSingleton('customer/session');
        $coreSession = Mage::getSingleton('core/session');

        $productId = (int)$this->getRequest()->getParam('product');
        if (!$productId) {
            $this->_redirect('*/');
            return;
        }

        $product = Mage::getModel('catalog/product')->load($productId);
        if (!$product->getId() || !$product->isVisibleInCatalog()) {
            $session->addError($this->__('Cannot specify product.'));
            $this->_redirect('*/');
            return;
        }

        try {
            $requestParams = $this->getRequest()->getParams();
            if ($session->getBeforeWishlistRequest()) {
                $requestParams = $session->getBeforeWishlistRequest();
                $session->unsBeforeWishlistRequest();
            }
            $buyRequest = new Varien_Object($requestParams);

            $result = $wishlist->addNewItem($product, $buyRequest,true);
            if (is_string($result)) {
                Mage::throwException($result);
            }
            $wishlist->save();

            Mage::dispatchEvent(
                'wishlist_add_product',
                array(
                    'wishlist' => $wishlist,
                    'product' => $product,
                    'item' => $result
                )
            );

            $referer = $session->getBeforeWishlistUrl();
            if ($referer) {
                $session->setBeforeWishlistUrl(null);
            } else {
                $referer = $this->_getRefererUrl();
            }

            /**
             *  Set referer to avoid referring to the compare popup window
             */
            $session->setAddActionReferer($referer);

            Mage::helper('wishlist')->calculate();

            $message = $this->__('%1$s has been added to your wishlist.',$product->getName());
            $coreSession->addSuccess($message);
        } catch (Mage_Core_Exception $e) {
            $coreSession->addError($this->__('An error occurred while adding item to wishlist: %s', $e->getMessage()));
        }
        catch (Exception $e) {
            $coreSession->addError($this->__('An error occurred while adding item to wishlist.'));
        }
        if(!$redirectUrl = $coreSession->getAfterWishlistRedirect()){
            $this->_redirect('*', array('wishlist_id' => $wishlist->getId()));
        }else{
            $coreSession->unsAfterWishlistRedirect();
            $this->_redirectUrl($redirectUrl);
        }
    }

    /**
     * Add the item to wish list through an AJAX call
     *
     * @return Mage_Core_Controller_Varien_Action|void
     */
    public function ajaxAddAction()
    {
        $this->getResponse()->clearHeaders()->setHeader('Content-type','application/json',true);
        $wishlist = $this->_getWishlist();
        if (!$wishlist) {
            $response_array['status'] = 'error';
            $response_array['msg'] = $this->__('An error occurred while adding item to wishlist. Please try again later or contact us');
            return $this->getResponse()->setBody(json_encode($response_array));
        }

        $session = Mage::getSingleton('customer/session');

        $productId = (int)$this->getRequest()->getParam('product');
        if (!$productId) {
            $response_array['status'] = 'error';
            $response_array['msg'] = $this->__('An error occurred while adding item to wishlist. Please try again later or contact us');
            return $this->getResponse()->setBody(json_encode($response_array));
        }

        $product = Mage::getModel('catalog/product')->load($productId);
        if (!$product->getId() || !$product->isVisibleInCatalog()) {
            $response_array['status'] = 'error';
            $response_array['msg'] = $this->__('Cannot specify product. Please try again later or contact us');
            return $this->getResponse()->setBody(json_encode($response_array));
        }

        try {
            $requestParams = $this->getRequest()->getParams();
            if ($session->getBeforeWishlistRequest()) {
                $requestParams = $session->getBeforeWishlistRequest();
                $session->unsBeforeWishlistRequest();
            }
            $buyRequest = new Varien_Object($requestParams);

            $result = $wishlist->addNewItem($product, $buyRequest,true);
            if (is_string($result)) {
                Mage::throwException($result);
            }
            $wishlist->save();

            Mage::dispatchEvent(
                'wishlist_add_product',
                array(
                    'wishlist' => $wishlist,
                    'product' => $product,
                    'item' => $result
                )
            );

            $referer = $session->getBeforeWishlistUrl();
            if ($referer) {
                $session->setBeforeWishlistUrl(null);
            } else {
                $referer = $this->_getRefererUrl();
            }

            /**
             *  Set referer to avoid referring to the compare popup window
             */
            $session->setAddActionReferer($referer);

            Mage::helper('wishlist')->calculate();

            $response_array['status'] = 'success';
            $message = $this->__('%1$s has been added to your wishlist.',$product->getName());
            $response_array['msg'] = $message;
//            $session->addSuccess($message);
        } catch (Mage_Core_Exception $e) {
            $response_array['status'] = 'error';
            $message = $this->__('An error occurred while adding item to wishlist: %s. Please try again later or contact us', $e->getMessage());
            $response_array['msg'] = $message;
//            $session->addError($message);
        } catch (Exception $e) {
            $response_array['status'] = 'error';
            $message = $this->__('An error occurred while adding item to wishlist. Please try again later or contact us');
            $response_array['msg'] = $message;
//            $session->addError($message);
        }
        return $this->getResponse()->setBody(json_encode($response_array));
    }
}