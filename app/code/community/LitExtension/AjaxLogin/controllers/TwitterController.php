<?php
/**
 * @project     AjaxLogin
 * @package LitExtension_AjaxLogin
 * @author      LitExtension
 * @email       litextension@gmail.com
 */

class LitExtension_AjaxLogin_TwitterController extends Mage_Core_Controller_Front_Action
{
    protected $referer = null;
    protected $flag = null;

    public function requestAction()
    {
        $client = Mage::getSingleton('ajaxlogin/twitter_client');
        if(!($client->isEnabled())) {
            Mage::helper('ajaxlogin')->redirect404($this);
        }

        $client->fetchRequestToken();
    }

    public function connectAction()
    {
        try {
            $this->_connectCallback();
        } catch (Exception $e) {
            Mage::getSingleton('core/session')->addError($e->getMessage());
        }

        if(!empty($this->referer)) {
            if(empty($this->flag)){
                if (!$url = Mage::getSingleton("core/session")->getSocialLoginUrlReferer()){
                    $url = Mage::getSingleton("core/session")->getSocialLoginCheckoutFlag() ? Mage::getUrl("onestepcheckout/index/index") : Mage::getUrl("customer/account/");
                }
                echo '
                <script data-cfasync="false" type="text/javascript">
                    try{
                        window.opener.location.href="' . $url . '";
                    }
                    catch(e){
                        window.opener.location.reload(true);
                    }
                    window.close();
                </script>
                ';
            }else{
                echo '
                <script data-cfasync="false" type="text/javascript">
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
        $this->referer = Mage::getUrl('ajaxlogin/account/twitter');
        
        Mage::helper('ajaxlogin/twitter')->disconnect($customer);

        Mage::getSingleton('core/session')
            ->addSuccess(
                $this->__('You have successfully disconnected your %s account from our store account.', $this->__('Twitter'))
            );
    }     

    protected function _connectCallback() {
        if (!($params = $this->getRequest()->getParams())
            ||
            !($requestToken = unserialize(Mage::getSingleton('core/session')
                ->getTwitterRequestToken()))
            ) {
            // Direct route access - deny
            return;
        }

        $this->referer = Mage::getSingleton('core/session')->getTwitterRedirect();
        
        if(isset($params['denied'])) {
            unset($this->referer);
            $this->flag = "noaccess";
            echo '<script type="text/javascript">window.close();</script>';
            return;
        }       

        $client = Mage::getSingleton('ajaxlogin/twitter_client');

        $token = $client->getAccessToken();
        
        $userInfo = (object) array_merge(
                (array) ($userInfo = $client->api('/account/verify_credentials.json', 'GET', array('skip_status' => true))),
                array('email' => sprintf('%s@twitter-user.com', strtolower($userInfo->screen_name)))
        );

        $customersByTwitterId = Mage::helper('ajaxlogin/twitter')
            ->getCustomersByTwitterId($userInfo->id);

        if(Mage::getSingleton('customer/session')->isLoggedIn()) {
            // Logged in user
            if($customersByTwitterId->count()) {
                // Twitter account already connected to other account - deny
                Mage::getSingleton('core/session')
                    ->addNotice(
                        $this->__('Your %s account is already connected to one of our store accounts.', $this->__('Twitter'))
                    );

                return;
            }

            // Connect from account dashboard - attach
            $customer = Mage::getSingleton('customer/session')->getCustomer();

            Mage::helper('ajaxlogin/twitter')->connectByTwitterId(
                $customer,
                $userInfo->id,
                $token
            );

            Mage::getSingleton('core/session')->addSuccess(
                $this->__('Your %1$s account is now connected to your store account. You can now login using our %1$s Connect button or using store account credentials you will receive to your email address.', $this->__('Twitter'))
            );

            return;
        }

        if($customersByTwitterId->count()) {
            // Existing connected user - login
            $customer = $customersByTwitterId->getFirstItem();

            Mage::helper('ajaxlogin/twitter')->loginByCustomer($customer);

            Mage::getSingleton('core/session')
                ->addSuccess(
                    $this->__('You have successfully logged in using your %s account.', $this->__('Twitter'))
                );

            return;
        }

        $customersByEmail = Mage::helper('ajaxlogin/twitter')
            ->getCustomersByEmail($userInfo->email);

        if($customersByEmail->count()) {
            // Email account already exists - attach, login
            $customer = $customersByEmail->getFirstItem();

            Mage::helper('ajaxlogin/twitter')->connectByTwitterId(
                $customer,
                $userInfo->id,
                $token
            );

            Mage::getSingleton('core/session')->addSuccess(
                $this->__('We have discovered you already have an account at our store. Your %s account is now connected to your store account.', $this->__('Twitter'))
            );

            return;
        }

        // New connection - create, attach, login
        if(empty($userInfo->name)) {
            throw new Exception(
                $this->__('Sorry, could not retrieve your %s last name. Please try again.', $this->__('Twitter'))
            );
        }

        Mage::helper('ajaxlogin/twitter')->connectByCreatingAccount(
            $userInfo->email,
            $userInfo->name,
            $userInfo->id,
            $token
        );

        Mage::getSingleton('core/session')->addSuccess(
            $this->__('Your Twitter account is now connected to your new user accout at our store. Now you can login using our Twitter Connect button.')
        );        
        Mage::getSingleton('core/session')->addNotice(
            sprintf($this->__('Since Twitter doesn\'t support third-party access to your email address, we were unable to send you your store accout credentials. To be able to login using store account credentials you will need to update your email address and password using our <a href="%s">Edit Account Information</a>.'), Mage::getUrl('customer/account/edit'))
        );        
    }

}
