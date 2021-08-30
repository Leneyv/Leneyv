<?php
namespace Magecomp\Orderonwhatsapp\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    const ENABLE = 'orderonwhatsapp/general/enable';
    const ENABLEMOBILE = 'orderonwhatsapp/general/enableonlymobile';
    const DEFAULTTITLE = 'orderonwhatsapp/general/defaulttitle';
    const BUTTONSIZE = 'orderonwhatsapp/general/buttonsize';
    const ADMINMOBNUM = 'orderonwhatsapp/general/adminmobnumber';
    const ENABLEBITLY = 'orderonwhatsapp/bitly/enable';
    const BITLYLOGIN = 'orderonwhatsapp/bitly/loginname';
    const BITLYAPI = 'orderonwhatsapp/bitly/apikey';
    const BITLYFORMAT = 'orderonwhatsapp/bitly/format';

    protected $_storeManager;

    public function __construct(\Magento\Framework\App\Helper\Context $context, \Magento\Store\Model\StoreManagerInterface $storeManager)
    {
        $this->_storeManager = $storeManager;
        parent::__construct($context);
    }

    public function isEnabled()
    {
        return $this->scopeConfig->getValue(self::ENABLE,\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    public function isEnabledForMobile()
    {
        return $this->scopeConfig->getValue(self::ENABLEMOBILE,\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    public function getDefaultTitle()
    {
        return $this->scopeConfig->getValue(self::DEFAULTTITLE,\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    public function getSize()
    {
        return $this->scopeConfig->getValue(self::BUTTONSIZE,\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    public function getAdminNumber()
    {
        return $this->scopeConfig->getValue(self::ADMINMOBNUM,\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    public function getBitlyUrl($url)
    {
        $connectURL = $url;
        if($this->scopeConfig->getValue(self::ENABLEBITLY,\Magento\Store\Model\ScopeInterface::SCOPE_STORE))
        {
            $login = $this->scopeConfig->getValue(self::BITLYLOGIN,\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
            $appkey = $this->scopeConfig->getValue(self::BITLYAPI,\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
            $format = $this->scopeConfig->getValue(self::BITLYFORMAT,\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
            $connectURL = 'http://api.bit.ly/v3/shorten?login='.$login.'&apiKey='.$appkey.'&uri='.urlencode($url).'&format='.$format;
            return $this->curl_get_result($connectURL);
        }
        return $url;
    }

    public function curl_get_result($url)
    {
        $ch = curl_init();
        $timeout = 5;
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$timeout);
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }

}