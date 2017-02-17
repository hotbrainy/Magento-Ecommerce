<?php
/**
 * @project     AjaxLogin
 * @package LitExtension_AjaxLogin
 * @author      LitExtension
 * @email       litextension@gmail.com
 */

class LitExtension_AjaxLogin_Model_Validator extends Varien_Object
{    
    protected $_userEmail;
    
    protected $_userPassword;
    
    protected $_userFirstName;
    
    protected $_userLastName;
    
    protected $_userNewsletter;
    
    protected $_userId;
    
    protected $_result;
    
    public function _construct() 
    {
        parent::_construct();
    }
    
    protected function setEmail($email = '')
    {
        if (!Zend_Validate::is($email, 'EmailAddress'))
        {
            $this->_result .= 'wrongemail,';
        }
        else
        {
            $this->_userEmail = $email;
        }
    }
    
    protected function setSinglePassword($password){
        $sanitizedPassword = str_replace(array('\'', '%', '\\', '/', ' '), '', $password);
        
        if (strlen($sanitizedPassword) > 16 || $sanitizedPassword != trim($password))
        {
            $this->_result .= 'wrongemail,';
        }
        
        $this->_userPassword = $sanitizedPassword;
    }
    
    protected function setPassword($password = '', $confirmation = '')
    {        
        $sanitizedPassword = str_replace(array('\'', '%', '\\', '/', ' '), '', $password);
        
        if ($password != $sanitizedPassword)
        {
            $this->_result .= 'dirtypassword,';
            return true;
        }
        
        if (strlen($sanitizedPassword) < 6)
        {
            $this->_result .= 'shortpassword,';
            return true;
        }
        
        if (strlen($sanitizedPassword) > 16)
        {
            $this->_result .= 'longpassword,';
            return true;
        }
        
        if ($sanitizedPassword != $confirmation)
        {
            $this->_result .= 'notsamepasswords,';
            return true;
        }
        
        $this->_userPassword = $sanitizedPassword;
    }
    
    protected function setName($firstname = '', $lastname = '')
    {
        $firstname = trim($firstname);
        $lastname = trim($lastname);
        
        $stop = false;
        
        if ($firstname == '')
        {
            $this->_result .= 'nofirstname,';
            $stop = true;
        }
        
        if ($lastname == '')
        {
            $this->_result .= 'nolastname,';
            $stop = true;
        }
        
        if ($stop == true)
        {
            return true;
        }
        
        $sanitizedFname = str_replace(array('\'', '%', '\\', '/'), '', $firstname);
        
        if ($sanitizedFname != $firstname)
        {
            $this->_result .= 'dirtyfirstname,';
            $stop = true;
        }
        
        $sanitizedLname = str_replace(array('\'', '%', '\\', '/'), '', $lastname);
        
        if ($sanitizedLname != $lastname)
        {
            $this->_result .= 'dirtylastname,';
            $stop = true;
        }
        
        if ($stop != true)
        {
            $this->_userFirstName = $firstname;
            $this->_userLastName = $lastname;
        }
    }
    
    protected function isEmailExist()
    {
        $customer = Mage::getModel('customer/customer');
        
        $customer->setWebsiteId(Mage::app()->getWebsite()->getId());
        $customer->loadByEmail($this->_userEmail);
        
        if($customer->getId())
        {
            return true;
        }

        return false;
    }

    protected function isnotEmail($email){
        $as = explode('.',strstr(strstr($email, '@'),'.'));
        $email = $as[count(explode('.',strstr(strstr($email, '@'),'.'))) - 1];
        $arrE = array(
            'ac', 'ad', 'ae', 'aero', 'af', 'ag', 'ai', 'al', 'am', 'an', 'ao', 'aq', 'ar', 'arpa',
            'as', 'asia', 'at', 'au', 'aw', 'ax', 'az', 'ba', 'bb', 'bd', 'be', 'bf', 'bg', 'bh', 'bi',
            'biz', 'bj', 'bm', 'bn', 'bo', 'br', 'bs', 'bt', 'bv', 'bw', 'by', 'bz', 'ca', 'cat', 'cc',
            'cd', 'cf', 'cg', 'ch', 'ci', 'ck', 'cl', 'cm', 'cn', 'co', 'com', 'coop', 'cr', 'cu',
            'cv', 'cx', 'cy', 'cz', 'de', 'dj', 'dk', 'dm', 'do', 'dz', 'ec', 'edu', 'ee', 'eg', 'er',
            'es', 'et', 'eu', 'fi', 'fj', 'fk', 'fm', 'fo', 'fr', 'ga', 'gb', 'gd', 'ge', 'gf', 'gg',
            'gh', 'gi', 'gl', 'gm', 'gn', 'gov', 'gp', 'gq', 'gr', 'gs', 'gt', 'gu', 'gw', 'gy', 'hk',
            'hm', 'hn', 'hr', 'ht', 'hu', 'id', 'ie', 'il', 'im', 'in', 'info', 'int', 'io', 'iq',
            'ir', 'is', 'it', 'je', 'jm', 'jo', 'jobs', 'jp', 'ke', 'kg', 'kh', 'ki', 'km', 'kn', 'kp',
            'kr', 'kw', 'ky', 'kz', 'la', 'lb', 'lc', 'li', 'lk', 'lr', 'ls', 'lt', 'lu', 'lv', 'ly',
            'ma', 'mc', 'md', 'me', 'mg', 'mh', 'mil', 'mk', 'ml', 'mm', 'mn', 'mo', 'mobi', 'mp',
            'mq', 'mr', 'ms', 'mt', 'mu', 'museum', 'mv', 'mw', 'mx', 'my', 'mz', 'na', 'name', 'nc',
            'ne', 'net', 'nf', 'ng', 'ni', 'nl', 'no', 'np', 'nr', 'nu', 'nz', 'om', 'org', 'pa', 'pe',
            'pf', 'pg', 'ph', 'pk', 'pl', 'pm', 'pn', 'pr', 'pro', 'ps', 'pt', 'pw', 'py', 'qa', 're',
            'ro', 'rs', 'ru', 'rw', 'sa', 'sb', 'sc', 'sd', 'se', 'sg', 'sh', 'si', 'sj', 'sk', 'sl',
            'sm', 'sn', 'so', 'sr', 'st', 'su', 'sv', 'sy', 'sz', 'tc', 'td', 'tel', 'tf', 'tg', 'th',
            'tj', 'tk', 'tl', 'tm', 'tn', 'to', 'tp', 'tr', 'travel', 'tt', 'tv', 'tw', 'tz', 'ua',
            'ug', 'uk', 'um', 'us', 'uy', 'uz', 'va', 'vc', 've', 'vg', 'vi', 'vn', 'vu', 'wf', 'ws',
            'ye', 'yt', 'yu', 'za', 'zm', 'zw'
        );
        if (!in_array($email, $arrE)) {
            return true;
        }else{
            return false;
        }
    }
    
    public function getResult()
    {
        return $this->_result;
    }
}
?>