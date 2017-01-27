<?php
/**
 * @project     AjaxLogin
 * @package LitExtension_AjaxLogin
 * @author      LitExtension
 * @email       litextension@gmail.com
 */

class LitExtension_AjaxLogin_Model_Amazon_Client
{
    const REDIRECT_URI_ROUTE = 'ajaxlogin/amazon/connect';

    const XML_PATH_ENABLED = 'ajaxlogin/amazon/enabled';
    const XML_PATH_CLIENT_ID = 'ajaxlogin/amazon/api_key';
    const XML_PATH_CLIENT_SECRET = 'ajaxlogin/amazon/secret';

    const OAUTH2_TOKEN_URI = 'https://api.amazon.com/auth/o2/token';
    const OAUTH2_AUTH_URI = 'https://www.amazon.com/ap/oa';
    const OAUTH2_SERVICE_URI = 'https://api.amazon.com/auth/o2';

    protected $clientId = null;
    protected $clientSecret = null;
    protected $redirectUri = null;
    protected $state = '';
    protected $scope = array('profile', 'profile:user_id');
    protected $field_select = array('user_id','name','email');
    protected $user_format = 'format=json';

    protected $token = null;

    public function __construct($params = array())
    {
        if(($this->isEnabled = $this->_isEnabled())) {
            $this->clientId = $this->_getClientId();
            $this->clientSecret = $this->_getClientSecret();
            $this->redirectUri = Mage::getModel('core/url')->sessionUrlVar(
                Mage::getUrl(self::REDIRECT_URI_ROUTE)
            );

            if(!empty($params['scope'])) {
                $this->scope = $params['scope'];
            }

            if(!empty($params['state'])) {
                $this->state = $params['state'];
            }
        }
    }

    public function isEnabled()
    {
        return (bool) $this->isEnabled;
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

    public function getScope()
    {
        return $this->scope;
    }

    public function getState()
    {
        return $this->state;
    }

    public function setState($state)
    {
        $this->state = $state;
    }

    public function setAccessToken($token)
    {
        $this->token = json_decode($token);
    }

    public function getAccessToken()
    {
        if(empty($this->token)) {
            $this->fetchAccessToken();
        }

        return json_encode($this->token);
    }

    public function createAuthUrl()
    {
        $url =
            self::OAUTH2_AUTH_URI.'?'.
            http_build_query(
                array(
                    'response_type'=> 'code',
                    'client_id' => $this->clientId,
                    'redirect_uri' => $this->redirectUri,
                    'state' => $this->state,
                    'scope' => implode(',', $this->scope)
                )
            );
        return $url;
    }

    public function api($endpoint, $method = 'GET', $params = array())
    {
        if(empty($this->token)) {
            $this->fetchAccessToken();
        }
        $select = ':('.implode(',',$this->field_select).')';
        $select .= '?'.$this->user_format;
        $url = self::OAUTH2_SERVICE_URI.$endpoint.$select;

        $method = strtoupper($method);

        $params = array_merge(array(
            'oauth2_access_token' => $this->token->access_token
        ), $params);

        $response = $this->_httpRequest($url, $method, $params);
        return $response;
    }

    protected function fetchAccessToken()
    {
        if(empty($_REQUEST['code'])) {
            throw new Exception(
                Mage::helper('ajaxlogin')
                    ->__('Unable to retrieve access code.')
            );
        }

        $response = $this->_httpRequest(
            self::OAUTH2_TOKEN_URI,
            'POST',
            array(
                'code' => $_REQUEST['code'],
                'redirect_uri' => $this->redirectUri,
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret,
                'grant_type' => 'authorization_code'
            )
        );

        $this->token = $response;
    }

    protected function _httpRequest($url, $method = 'GET', $params = array())
    {
        $client = new Zend_Http_Client($url, array('timeout' => 60));

        switch ($method) {
            case 'GET':
                $client->setParameterGet($params);
                break;
            case 'POST':
                $client->setParameterPost($params);
                break;
            case 'DELETE':
                $client->setParameterGet($params);
                break;
            default:
                throw new Exception(
                    Mage::helper('ajaxlogin')
                        ->__('Required HTTP method is not supported.')
                );
        }

        $response = $client->request($method);

        Mage::log($response->getStatus().' - '. $response->getBody());

        $decoded_response = json_decode($response->getBody());

        /*
         * Per http://tools.ietf.org/html/draft-ietf-oauth-v2-27#section-5.1
         * amazon should return data using the "application/json" media type.
         * amazon violates OAuth2 specification and returns string. If this
         * ever gets fixed, following condition will not be used anymore.
         */
        if(empty($decoded_response)) {
            $parsed_response = array();
            parse_str($response->getBody(), $parsed_response);

            $decoded_response = json_decode(json_encode($parsed_response));
        }

        if($response->isError()) {
            $status = $response->getStatus();
            if(($status == 400 || $status == 401)) {
                if(isset($decoded_response->error->message)) {
                    $message = $decoded_response->error->message;
                } else {
                    $message = Mage::helper('ajaxlogin')
                        ->__('Unspecified OAuth error occurred.');
                }

                throw new LitExtension_Ajaxlogin_AmazonOAuthException($message);
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

class LitExtension_Ajaxlogin_AmazonOAuthException extends Exception
{}