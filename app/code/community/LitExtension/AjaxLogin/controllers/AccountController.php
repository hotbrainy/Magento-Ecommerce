<?php
/**
 * @project     AjaxLogin
 * @package LitExtension_AjaxLogin
 * @author      LitExtension
 * @email       litextension@gmail.com
 */

require_once Mage::getModuleDir('controllers', 'Mage_Customer') . DS . 'AccountController.php';

class LitExtension_AjaxLogin_AccountController extends Mage_Customer_AccountController
{
    private $_url;

    public function preDispatch()
    {
        $this->_url = Mage::getBaseUrl() . '?leregister';

        parent::preDispatch();
    }

    public function loginAction()
    {
        $this->setLocation();
    }

    public function createAction()
    {
        $this->setLocation();
    }

    private function setLocation()
    {
        Mage::app()->getFrontController()->getResponse()->setRedirect($this->_url);
    }

    public function createPostAction()
    {
        $session = $this->_getSession();
        $coreSession = Mage::getSingleton('core/session');
        $redirectPostAuth = $coreSession->getPostAuthRedirect();
        if ($session->isLoggedIn()) {
            $this->_redirect('*/*/');
            return;
        }
        $session->setEscapeMessages(true); // prevent XSS injection in user input
        if ($this->getRequest()->isPost()) {
            $errors = array();

            if (!$customer = Mage::registry('current_customer')) {
                $customer = Mage::getModel('customer/customer')->setId(null);
            }

            /* @var $customerForm Mage_Customer_Model_Form */
            $customerForm = Mage::getModel('customer/form');
            $customerForm->setFormCode('customer_account_create')
                ->setEntity($customer);

            $customerData = $customerForm->extractData($this->getRequest());

            if ($this->getRequest()->getParam('is_subscribed', false)) {
                $customer->setIsSubscribed(1);
            }

            /**
             * Initialize customer group id
             */
            $customer->getGroupId();

            if ($this->getRequest()->getPost('create_address')) {
                /* @var $address Mage_Customer_Model_Address */
                $address = Mage::getModel('customer/address');
                /* @var $addressForm Mage_Customer_Model_Form */
                $addressForm = Mage::getModel('customer/form');
                $addressForm->setFormCode('customer_register_address')
                    ->setEntity($address);

                $addressData    = $addressForm->extractData($this->getRequest(), 'address', false);
                $addressErrors  = $addressForm->validateData($addressData);
                if ($addressErrors === true) {
                    $address->setId(null)
                        ->setIsDefaultBilling($this->getRequest()->getParam('default_billing', false))
                        ->setIsDefaultShipping($this->getRequest()->getParam('default_shipping', false));
                    $addressForm->compactData($addressData);
                    $customer->addAddress($address);

                    $addressErrors = $address->validate();
                    if (is_array($addressErrors)) {
                        $errors = array_merge($errors, $addressErrors);
                    }
                } else {
                    $errors = array_merge($errors, $addressErrors);
                }
            }

            try {
                $customerErrors = $customerForm->validateData($customerData);
                if ($customerErrors !== true) {
                    $errors = array_merge($customerErrors, $errors);
                } else {
                    $customerForm->compactData($customerData);
                    $customer->setPassword($this->getRequest()->getPost('password'));
                    $customer->setPasswordConfirmation($this->getRequest()->getPost('password_confirmation'));
                    $customerErrors = $customer->validate();
                    if (is_array($customerErrors)) {
                        $errors = array_merge($customerErrors, $errors);
                    }
                }

                $validationResult = count($errors) == 0;

                if (true === $validationResult) {
                    $customer->save();

                    Mage::dispatchEvent('customer_register_success',
                        array('account_controller' => $this, 'customer' => $customer)
                    );
                    if ($customer->isConfirmationRequired()) {
                        $customer->sendNewAccountEmail(
                            'confirmation',
                            $session->getBeforeAuthUrl(),
                            Mage::app()->getStore()->getId()
                        );
                        $session->addSuccess($this->__('Account confirmation is required. Please, check your email for the confirmation link. To resend the confirmation email please <a href="%s">click here</a>.', Mage::helper('customer')->getEmailConfirmationUrl($customer->getEmail())));
                        if($redirectPostAuth){
                            $this->_redirectUrl($redirectPostAuth);
                        }else{
                            $this->_redirect(Mage::getUrl('*/*/index', array('_secure'=>true)));
                        }
                        return;
                    } else {
                        $session->setCustomerAsLoggedIn($customer);
                        if($redirectPostAuth){
                            $this->_redirectUrl($redirectPostAuth);
                        }else{
                            $this->_redirect(Mage::getUrl('customer/account', array('_secure'=>true)));
                        }
                        return;
                    }
                } else {
                    $session->setCustomerFormData($this->getRequest()->getPost());
                    if (is_array($errors)) {
                        foreach ($errors as $errorMessage) {
                            $coreSession->addError($errorMessage);
                        }
                    } else {
                        $coreSession->addError($this->__('Invalid customer data'));
                    }
                }
            } catch (Mage_Core_Exception $e) {
                $coreSession->setCustomerFormData($this->getRequest()->getPost());
                if ($e->getCode() === Mage_Customer_Model_Customer::EXCEPTION_EMAIL_EXISTS) {
                    $url = Mage::getUrl('customer/account/forgotpassword');
                    $message = $this->__('There is already an account with this email address. If you are sure that it is your email address, <a href="%s">click here</a> to get your password and access your account.', $url);
                    $coreSession->setEscapeMessages(false);
                } else {
                    $message = $e->getMessage();
                }
                $coreSession->addError($message);
            } catch (Exception $e) {
                $coreSession->setCustomerFormData($this->getRequest()->getPost())
                    ->addException($e, $this->__('Cannot save the customer.'));
            }
        }

        $this->_redirectError(Mage::getUrl('*/*/create', array('_secure' => true)));
    }

    public function facebookAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    public function googleAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    public function twitterAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    public function forgotPasswordPostAction(){
        $params = $this->getRequest()->getParams();
        if(isset($params['le_ajaxlogin']) && $params['le_ajaxlogin']){
            $email = (string) $this->getRequest()->getPost('email');
            if ($email) {
                $success = false;
                $message ='';
                $error_type = '';
                $error = false;
                if($error == false){
                $customer = Mage::getModel('customer/customer')
                    ->setWebsiteId(Mage::app()->getStore()->getWebsiteId())
                    ->loadByEmail($email);

                if ($customer->getId()) {
                    try {
                        $newResetPasswordLinkToken = Mage::helper('customer')->generateResetPasswordLinkToken();
                        $customer->changeResetPasswordLinkToken($newResetPasswordLinkToken);
                        $customer->sendPasswordResetConfirmationEmail();
                        $success = true;
                        $message = Mage::helper('customer')->__('If there is an account associated with %s you will receive an email with a link to reset your password.', Mage::helper('customer')->htmlEscape($email));
                    } catch (Exception $exception) {
                        $message = $exception->getMessage();
                    }
                } else{
                    $message = Mage::helper('customer')->__('If there is an account associated with %s you will receive an email with a link to reset your password.', Mage::helper('customer')->htmlEscape($email));
                    $error_type = 'email';
                }
                }
                $this->getResponse()->setBody(Mage::helper('core')->jsonEncode(array(
                            'success' => $success,
                            'message' => $message,
                            'error_type'=>$error_type,
                        )
                    )
                );
            }
        } else{
            return parent::forgotPasswordPostAction();
        }
    }
}

?>
