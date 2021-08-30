<?php

namespace Meetanshi\WhatsappContact\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;

class Data extends AbstractHelper
{
    const CHAT_ENABLE = 'whatsappcontact/configuration/enable';
    const ENABLE_IN = 'whatsappcontact/configuration/enable_in';
    const DEFAULT_MESSAGE = 'whatsappcontact/configuration/default_message';
    const MOBILE_NUMBER = 'whatsappcontact/settings/mobile_number';
    const BACKGROUND_COLOR = 'whatsappcontact/settings/background_color';
    const ICON_COLOR = 'whatsappcontact/settings/icon_color';
    const TOP = 'whatsappcontact/settings/top';
    const RIGHT = 'whatsappcontact/settings/right';
    const BOTTOM = 'whatsappcontact/settings/bottom';
    const LEFT = 'whatsappcontact/settings/left';
    const ANIMATION = 'whatsappcontact/settings/animation';
    const BUTTON_TEXT = 'whatsappcontact/settings/text';
    const FROM_DATE = 'whatsappcontact/settings/fromdate';
    const TO_DATE = 'whatsappcontact/settings/todate';
    const CLOSE= 'whatsappcontact/settings/close';


    public function getConfigValue($storeId = null)
    {
        $data[]="";
        $data['chat_enable'] = $this->scopeConfig->getValue(static::CHAT_ENABLE, ScopeInterface::SCOPE_STORE, $storeId);
        $data['enable_in'] = $this->scopeConfig->getValue(static::ENABLE_IN, ScopeInterface::SCOPE_STORE, $storeId);
        $data['default_message'] = $this->scopeConfig->getValue(static::DEFAULT_MESSAGE, ScopeInterface::SCOPE_STORE, $storeId);
        $data['mobile_number'] = $this->scopeConfig->getValue(static::MOBILE_NUMBER, ScopeInterface::SCOPE_STORE, $storeId);
        $data['background_color'] = $this->scopeConfig->getValue(static::BACKGROUND_COLOR, ScopeInterface::SCOPE_STORE, $storeId);
        $data['icon_color'] = $this->scopeConfig->getValue(static::ICON_COLOR, ScopeInterface::SCOPE_STORE, $storeId);
        $data['top'] = $this->scopeConfig->getValue(static::TOP, ScopeInterface::SCOPE_STORE, $storeId);
        $data['right'] = $this->scopeConfig->getValue(static::RIGHT, ScopeInterface::SCOPE_STORE, $storeId);
        $data['bottom'] = $this->scopeConfig->getValue(static::BOTTOM, ScopeInterface::SCOPE_STORE, $storeId);
        $data['left'] = $this->scopeConfig->getValue(static::LEFT, ScopeInterface::SCOPE_STORE, $storeId);
        $data['animation'] = $this->scopeConfig->getValue(static::ANIMATION, ScopeInterface::SCOPE_STORE, $storeId);
        $data['text'] = $this->scopeConfig->getValue(static::BUTTON_TEXT, ScopeInterface::SCOPE_STORE, $storeId);
        $data['from'] = $this->scopeConfig->getValue(static::FROM_DATE, ScopeInterface::SCOPE_STORE, $storeId);
        $data['to'] = $this->scopeConfig->getValue(static::TO_DATE, ScopeInterface::SCOPE_STORE, $storeId);
        $data['close'] = $this->scopeConfig->getValue(static::CLOSE, ScopeInterface::SCOPE_STORE, $storeId);
        if ($data['top'] <= '0' || $data['top'] == '') :
            $data['top']='';
        endif;
        if ($data['right'] <= '0' || $data['right'] == '') :
            $data['right']='';
        endif;
        if ($data['bottom'] <= '0') :
            $data['bottom']='25';
        endif;
        if (($data['left'] <= '0') && ($data['right'] <= '0')) :
            $data['left']='25';
        endif;
        if ($data['text'] == '') :
            $data['text']= 'WhatsApp Contact';
        endif;
        $data['close_top'] = '';
        $data['close_right'] = '';
        $data['close_bottom'] = '';
        $data['close_left'] = '';
        $data['close_right_mobile'] = '';
        $data['title_top'] = '18';
        $data['title_left'] = '';
        $data['title_right'] = '';

        if ($data['close']) :
            if ($data['top']  != '' && $data['top'] > '0') :
                $data['close_top'] = intval($data['top'])-3;
                $data['close_bottom'] = '';
            else :
                $data['close_top'] = '';
                $data['close_bottom'] = intval($data['bottom'])+50;
            endif;
            if ($data['right'] != '' && $data['right'] > '0') :
                $data['close_right'] = intval($data['right'])-5;
                $data['close_right_mobile'] =  intval($data['right'])-10;
                $data['title_right'] = '55';
                $data['close_left'] = '';
            else :
                $data['close_left'] = intval($data['left'])+55;
                $data['title_left'] = '55';
            endif;
        endif;
        return $data;
    }
    public function printLog($log)
    {
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/WhatsappContact.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        if (is_array($log)) :
            $logger->info(print_r($log, true));
        else :
            $logger->info($log);
        endif;
    }
}
