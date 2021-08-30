<?php

namespace Meetanshi\WhatsappContact\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Meetanshi\WhatsappContact\Helper\Data;
use Magento\Framework\Registry;

class WhatsappContact extends Template
{
    protected $registry;
    protected $helper;
    protected $priceHelper;
    public function __construct(
        Context $context,
        Registry $registry,
        Data $helper,
        array $data = []
    ) {
        $this->helper    = $helper;
        $this->registry = $registry;
        parent::__construct($context, $data);
    }
    public function getChat()
    {
        try {
            $chat = $this->helper->getConfigValue();
            $data= $chat['mobile_number'];
            $data .="&text=".$chat['default_message'];
            return $data;
        } catch (\Exception $e) {
            $this->helper->printLog($e->getMessage());
            return "";
        }
    }
    public function checkDevices()
    {
        if (preg_match("/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", $_SERVER["HTTP_USER_AGENT"])) :
            $flag = "mobile";
        else :
            $flag = "web";
        endif;
        return $flag;
    }
    public function getWhatsappChat()
    {
        return $this->helper->getConfigValue();
    }
}
