<?php
/**
 * @project     AjaxLogin
 * @package LitExtension_AjaxLogin
 * @author      LitExtension
 * @email       litextension@gmail.com
 */

class LitExtension_AjaxLogin_AmazonController extends Mage_Core_Controller_Front_Action
{
    protected $referer = null;
    protected $flag = null;

    public function connectAction()
    {
        try {
            $this->_connectCallback();
        } catch (Exception $e) {
            Mage::getSingleton('core/session')->addError($e->getMessage());
        }

        if(!empty($this->referer)) {
            if(empty($this->flag)){
                echo '
                <script type="text/javascript">
                    try{
                        window.opener.location.href="' . Mage::getUrl("customer/account/") . '";
                    }
                    catch(e){
                        window.opener.location.reload(true);
                    }
                    window.close();
                </script>
                ';
            }else{
                echo '
                <script type="text/javascript">
                    window.close();
                </script>
                ';
            }

        } else {
            Mage::helper('ajaxlogin')->redirect404($this);
        }
    }

    public function disconnectAction()
    {
        $customer = Mage::getSingleton('customer/session')->getCustomer();

        try {
            $this->_disconnectCallback($customer);
        } catch (Exception $e) {
            Mage::getSingleton('core/session')->addError($e->getMessage());
        }

        if(!empty($this->referer)) {
            $this->_redirectUrl($this->referer);
        } else {
            Mage::helper('ajaxlogin')->redirect404($this);
        }
    }

    protected function _disconnectCallback(Mage_Customer_Model_Customer $customer) {
        $this->referer = Mage::getUrl('ajaxlogin/account/amazon');
        
        Mage::helper('ajaxlogin/amazon')->disconnect($customer);
        
        Mage::getSingleton('core/session')
            ->addSuccess(
                $this->__('You have successfully disconnected your %s account from our store account.', $this->__('Amazon'))
            );
    }

    protected function _connectCallback() {
        $errorCode = $this->getRequest()->getParam('error');
        $code = $this->getRequest()->getParam('code');
        $state = $this->getRequest()->getParam('state');
        if(!($errorCode || $code) && !$state) {
            // Direct route access - deny
            return;
        }
        
        $this->referer = Mage::getSingleton('core/session')->getAmazonRedirect();

        if(!$state || $state != Mage::getSingleton('core/session')->getAmazonCsrf()) {
            //return;
        }

        if($errorCode) {
            // Amazon API read light - abort
            if($errorCode === 'access_denied') {
                unset($this->referer);
                $this->flag = "noaccess";
                echo '<script type="text/javascript">window.close();</script>';
            }
            return;
        }

        if ($code) {
            // Amazon API green light - proceed
            $client = Mage::getSingleton('ajaxlogin/amazon_client');

            $userInfo = $client->api('/userinfo');
            $token = $client->getAccessToken();

            $customersByAmazonId = Mage::helper('ajaxlogin/amazon')
                ->getCustomersByAmazonId($userInfo->user_id);

            if(Mage::getSingleton('customer/session')->isLoggedIn()) {
                // Logged in user
                if($customersByAmazonId->count()) {
                    // Amazon account already connected to other account - deny
                    Mage::getSingleton('core/session')
                        ->addNotice(
                            $this->__('Your %s account is already connected to one of our store accounts.', $this->__('Amazon'))
                        );

                    return;
                }

                // Connect from account dashboard - attach
                $customer = Mage::getSingleton('customer/session')->getCustomer();

                Mage::helper('ajaxlogin/amazon')->connectByAmazonId(
                    $customer,
                    $userInfo->user_id,
                    $token
                );

                Mage::getSingleton('core/session')->addSuccess(
                    $this->__('Your %1$s account is now connected to your store account. You can now login using our %1$s Connect button or using store account credentials you will receive to your email address.', $this->__('Amazon'))
                );

                return;
            }

            if($customersByAmazonId->count()) {
                // Existing connected user - login
                $customer = $customersByAmazonId->getFirstItem();

                Mage::helper('ajaxlogin/amazon')->loginByCustomer($customer);

                Mage::getSingleton('core/session')
                    ->addSuccess(
                        $this->__('You have successfully logged in using your %s account.', $this->__('Amazon'))
                    );

                return;
            }

            $customersByEmail = Mage::helper('ajaxlogin/facebook')
                ->getCustomersByEmail($userInfo->email);

            if($customersByEmail->count())  {
                // Email account already exists - attach, login
                $customer = $customersByEmail->getFirstItem();
                
                Mage::helper('ajaxlogin/amazon')->connectByAmazonId(
                    $customer,
                    $userInfo->user_id,
                    $token
                );

                Mage::getSingleton('core/session')->addSuccess(
                    $this->__('We have discovered you already have an account at our store. Your %s account is now connected to your store account.', $this->__('Amazon'))
                );

                return;
            }

            // New connection - create, attach, login
            if(empty($userInfo->name)) {
                throw new Exception(
                    $this->__('Sorry, could not retrieve your %s name. Please try again.', $this->__('Amazon'))
                );
            }
            $name = explode(' ',$userInfo->name,2);

            Mage::helper('ajaxlogin/amazon')->connectByCreatingAccount(
                $userInfo->email,
                $userInfo->$name[0],
                $userInfo->$name[1],
                $userInfo->id,
                $token
            );

            Mage::getSingleton('core/session')->addSuccess(
                $this->__('Your %1$s account is now connected to your new user accout at our store. Now you can login using our %1$s Connect button or using store account credentials you will receive to your email address.', $this->__('Amazon'))
            );
        }
    }

}