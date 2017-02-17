<?php
/**
 * @project     AjaxLogin
 * @package LitExtension_AjaxLogin
 * @author      LitExtension
 * @email       litextension@gmail.com
 */

class LitExtension_AjaxLogin_Model_Yahoo_Client
{
    const REDIRECT_URI_ROUTE = 'ajaxlogin/yahoo/connect';
    const REQUEST_TOKEN_URI_ROUTE = 'ajaxlogin/yahoo/request';

    const OAUTH_URI = 'https://api.login.yahoo.com/oauth/v2';
    const OAUTH2_SERVICE_URI = 'http://social.yahooapis.com/v1/user/';

    const XML_PATH_ENABLED = 'ajaxlogin/yahoo/enabled';
    const XML_PATH_CLIENT_ID = 'ajaxlogin/yahoo/api_key';
    const XML_PATH_CLIENT_SECRET = 'ajaxlogin/yahoo/secret';

    protected $clientId = null;
    protected $clientSecret = null;
    protected $redirectUri = null;
    protected $client = null;
    protected $token = null;

    public function __construct()
    {
        if(($this->isEnabled = $this->_isEnabled())) {
            $this->clientId = $this->_getClientId();
            $this->clientSecret = $this->_getClientSecret();
            $this->redirectUri = Mage::getModel('core/url')->sessionUrlVar(
                Mage::getUrl(self::REDIRECT_URI_ROUTE)
            );

            $this->client = new Zend_Oauth_Consumer(
                array(
                    'callbackUrl' => $this->redirectUri,
                    'siteUrl' => self::OAUTH_URI,
                    'authorizeUrl' => self::OAUTH_URI.'/request_auth',
                    'consumerKey' => $this->clientId,
                    'consumerSecret' => $this->clientSecret,
                    'requestTokenUrl' => 'https://api.login.yahoo.com/oauth/v2/get_request_token',
                    'userAuthorisationUrl' => 'https://api.login.yahoo.com/oauth/v2/request_auth',
                    'accessTokenUrl' => 'https://api.login.yahoo.com/oauth/v2/get_token',
                    'oauth_signature_method' => 'HMAC-SHA1',
                    'oauth_signature' => $this->clientId . '%26' . $this->clientSecret,
                )
            );
        }
    }

    public function isEnabled()
    {
        return (bool) $this->isEnabled;
    }

    public function getClient()
    {
        return $this->client;
    }

    public function getClientId()
    {
        return $this->clientId;
    }

    public function getClientSecret()
    {
        return $this->clientSecret;
    }

    public function getRedirectUri()
    {
        return $this->redirectUri;
    }

    public function setAccessToken($token)
    {
        $this->token = unserialize($token);
    }

    public function getAccessToken()
    {
        if(empty($this->token)) {
            $this->fetchAccessToken();
        }
        return unserialize($this->token);
    }

    public function getXoauthYahooGuid(){
        if(empty($this->token)) {
            $this->fetchAccessToken();
        }
        return $this->token->xoauth_yahoo_guid;
    }

    public function createAuthUrl()
    {
        return Mage::getUrl(self::REQUEST_TOKEN_URI_ROUTE);
    }

    public function fetchRequestToken()
    {
//        if(!($requestToken = $this->client->getRequestToken())) {
//            throw new Exception(
//                Mage::helper('ajaxlogin')
//                    ->__('Unable to retrieve request token.')
//            );
//        }

        try{
            $requestToken = $this->client->getRequestToken();
            if($requestToken == FALSE){
                echo "Connect to 'https://api.login.yahoo.com' is false ! <br />Error code :".$this->client->getLastResponse() ;
                return ;
            }
        }catch (Zend_Oauth_Exception $e){
            echo 'Connect to \'https://api.login.yahoo.com\' is false ! <br />Error code : '.$e->getMessage();
            return ;
        }

        Mage::getSingleton('core/session')
            ->setYahooRequestToken(serialize($requestToken));
        $this->client->redirect();
    }

    protected function fetchAccessToken()
    {
        if (!($params = Mage::app()->getFrontController()->getRequest()->getParams())
            ||
            !($requestToken = Mage::getSingleton('core/session')
                ->getYahooRequestToken())
        ) {
            throw new Exception(
                Mage::helper('ajaxlogin')
                    ->__('Unable to retrieve access code.')
            );
        }

        if(!($token = $this->client->getAccessToken(
            $params,
            unserialize($requestToken)
        )
        )
        ) {
            throw new Exception(
                Mage::helper('ajaxlogin')
                    ->__('Unable to retrieve access token.')
            );
        }

        Mage::getSingleton('core/session')->unsYahooRequestToken();

        return $this->token = $token;
    }

    public function api($endpoint, $method = 'GET', $params = array())
    {
        if(empty($this->token)) {
            throw new Exception(
                Mage::helper('ajaxlogin')
                    ->__('Unable to proceeed without an access token.')
            );
        }

        $url = self::OAUTH2_SERVICE_URI.$this->token->xoauth_yahoo_guid.'/profile';
        $response = $this->_httpRequest($url, strtoupper($method), $params);
        return $response;
    }

    protected function _httpRequest($url, $method = 'GET', $params = array())
    {
        $client = $this->token->getHttpClient(
            array(
                'callbackUrl' => $this->redirectUri,
                'siteUrl'=>self::OAUTH_URI,
                'authorizeUrl' => self::OAUTH_URI.'/request_auth',
                'consumerKey' => $this->clientId,
                'consumerSecret' => $this->clientSecret,
                'oauth_signature_method' => 'HMAC-SHA1',
                'requestTokenUrl' => 'https://api.login.yahoo.com/oauth/v2/get_request_token',
                'userAuthorisationUrl' => 'https://api.login.yahoo.com/oauth/v2/request_auth',
                'accessTokenUrl' => 'https://api.login.yahoo.com/oauth/v2/get_token',
                'oauth_signature' => $this->clientSecret,
            )
        );
        $client->setUri($url);
        switch ($method) {
            case 'GET':
                $client->setMethod(Zend_Http_Client::GET);
                $client->setParameterGet(array('format' => 'json'));
                break;
            case 'POST':
                $client->setMethod(Zend_Http_Client::POST);
                $client->setParameterPost($params);
                break;
            case 'DELETE':
                $client->setMethod(Zend_Http_Client::DELETE);
                break;
            default:
                throw new Exception(
                    Mage::helper('ajaxlogin')
                        ->__('Required HTTP method is not supported.')
                );
        }

        $response = $client->request();
        Mage::log($response->getStatus().' - '. $response->getBody());

        $decoded_response = json_decode($response->getBody());
        if($response->isError()) {
            $status = $response->getStatus();
            if(($status == 400 || $status == 401 || $status == 429)) {
                if(isset($decoded_response->error->message)) {
                    $message = $decoded_response->error->message;
                } else {
                    $message = Mage::helper('ajaxlogin')
                        ->__('Unspecified OAuth error occurred.');
                }
                throw new LitExtension_Ajaxlogin_YahooOAuthException($message);
            } else {
                $message = sprintf(
                    Mage::helper('ajaxlogin')
                        ->__('HTTP error %d occurred while issuing request.'),
                    $status
                );

                throw new Exception($message);
            }
        }
        return $decoded_response;
    }

    protected function _isEnabled()
    {
        return $this->_getStoreConfig(self::XML_PATH_ENABLED);
    }

    protected function _getClientId()
    {
        return $this->_getStoreConfig(self::XML_PATH_CLIENT_ID);
    }

    protected function _getClientSecret()
    {
        return $this->_getStoreConfig(self::XML_PATH_CLIENT_SECRET);
    }

    protected function _getStoreConfig($xmlPath)
    {
        return Mage::getStoreConfig($xmlPath, Mage::app()->getStore()->getId());
    }

}

class LitExtension_Ajaxlogin_YahooOAuthException extends Exception
{}
