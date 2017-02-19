<?php
/**
 * @project     AjaxLogin
 * @package LitExtension_AjaxLogin
 * @author      LitExtension
 * @email       litextension@gmail.com
 */

class LitExtension_AjaxLogin_FacebookController extends Mage_Core_Controller_Front_Action
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

        if(empty($this->flag)){
            if (!$url = Mage::getSingleton("core/session")->getSocialLoginUrlReferer()){
                $url = Mage::getSingleton("core/session")->getSocialLoginCheckoutFlag() ? Mage::getUrl("onestepcheckout/index/index") : Mage::getUrl("customer/account/");
            } else {
                Mage::getSingleton("core/session")->setSocialLoginUrlReferer(null);
            }        
            echo '
                <script data-cfasync = "false" type = "text/javascript">
                    try {
                        window.opener.location.href = " ' . $url .' ";
                    } catch (e) {
                        window.opener.location.reload(true);
                    }
                    window.close();
            ';
        } else {
            echo '
                <script data-cfasync="false" type="text/javascript">
                    window.close();
                </script>
            ';
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

        if (!empty($this->referer)) {
            $this->_redirectUrl($this->referer);
        } else {
            Mage::helper('ajaxlogin')->redirect404($this);
        }
    }

    protected function _disconnectCallback(Mage_Customer_Model_Customer $customer)
    {
        $this->referer = Mage::getUrl('ajaxlogin/account/facebook');

        Mage::helper('ajaxlogin/facebook')->disconnect($customer);

        Mage::getSingleton('core/session')
            ->addSuccess(
                $this->__('You have successfully disconnected your %s account from our store account.', $this->__('Facebook'))
            );
    }

    protected function _connectCallback()
    {
        $errorCode = $this->getRequest()->getParam('error');
        $code = $this->getRequest()->getParam('code');
        $state = $this->getRequest()->getParam('state');
        if (!($errorCode || $code) && !$state) {
            // Direct route access - deny
            return;
        }

        $this->referer = Mage::getSingleton('core/session')
            ->getFacebookRedirect();

        if (!$state || $state != Mage::getSingleton('core/session')->getFacebookCsrf()) {
            //return;
        }

        if ($errorCode) {
            // Facebook API read light - abort
            if ($errorCode === 'access_denied') {
                $this->flag = "noaccess";
                echo '<script type="text/javascript">window.close();</script>';
            }
            return;
        }

        if ($code) {
            // Facebook API green light - proceed
            $client = Mage::getSingleton('ajaxlogin/facebook_client');

            $userInfo = $client->api('/me?fields=id,email,first_name,last_name');
            $token = $client->getAccessToken();

            $customersByFacebookId = Mage::helper('ajaxlogin/facebook')
                ->getCustomersByFacebookId($userInfo->id);

            if (Mage::getSingleton('customer/session')->isLoggedIn()) {
                // Logged in user
                if ($customersByFacebookId->count()) {
                    // Facebook account already connected to other account - deny
                    Mage::getSingleton('core/session')
                        ->addNotice(
                            $this->__('Your %s account is already connected to one of our store accounts.', $this->__('Facebook'))
                        );

                    return;
                }

                // Connect from account dashboard - attach
                $customer = Mage::getSingleton('customer/session')->getCustomer();

                Mage::helper('ajaxlogin/facebook')->connectByFacebookId(
                    $customer,
                    $userInfo->id,
                    $token
                );

                Mage::getSingleton('core/session')->addSuccess(
                    $this->__('Your %1$s account is now connected to your store account. You can now login using our %1$s Connect button or using store account credentials you will receive to your email address.', $this->__('Facebook'))
                );

                return;
            }

            if ($customersByFacebookId->count()) {
                // Existing connected user - login
                $customer = $customersByFacebookId->getFirstItem();

                Mage::helper('ajaxlogin/facebook')->loginByCustomer($customer);

                Mage::getSingleton('core/session')
                    ->addSuccess(
                        $this->__('You have successfully logged in using your %s account.', $this->__('Facebook'))
                    );

                return;
            }

            $customersByEmail = Mage::helper('ajaxlogin/facebook')
                ->getCustomersByEmail($userInfo->email);

            if ($customersByEmail->count()) {
                // Email account already exists - attach, login
                $customer = $customersByEmail->getFirstItem();

                Mage::helper('ajaxlogin/facebook')->connectByFacebookId(
                    $customer,
                    $userInfo->id,
                    $token
                );

                Mage::getSingleton('core/session')->addSuccess(
                    $this->__('We have discovered you already have an account at our store. Your %s account is now connected to your store account.', $this->__('Facebook'))
                );

                return;
            }

            // New connection - create, attach, login
            if (empty($userInfo->first_name)) {
                throw new Exception(
                    $this->__('Sorry, could not retrieve your %s first name. Please try again.', $this->__('Facebook'))
                );
            }

            if (empty($userInfo->last_name)) {
                throw new Exception(
                    $this->__('Sorry, could not retrieve your %s last name. Please try again.', $this->__('Facebook'))
                );
            }

            Mage::helper('ajaxlogin/facebook')->connectByCreatingAccount(
                $userInfo->email,
                $userInfo->first_name,
                $userInfo->last_name,
                $userInfo->id,
                $token
            );

            Mage::getSingleton('core/session')->addSuccess(
                $this->__('Your %1$s account is now connected to your new user accout at our store. Now you can login using our %1$s Connect button or using store account credentials you will receive to your email address.', $this->__('Facebook'))
            );
        }
    }

}