<?php
namespace W3speedup\Optimization\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\ScopeInterface;

class Data extends AbstractHelper
{
    public function __construct(Context $context)
    {
        $this->scopeConfig = $context->getScopeConfig();
        parent::__construct($context);
    }
    public function getLicenseKey()
    {   
        $settings = array();
        $settings1 = $this->scopeConfig->getValue('general_setting/general', ScopeInterface::SCOPE_STORE);  
        if($settings1){
            $settings = $settings1;
        }      
		$settings2 = $this->scopeConfig->getValue('css_opt_settings/general', ScopeInterface::SCOPE_STORE);
        if($settings2){
            $settings = array_merge($settings,$settings2);
        }
		$settings3 = $this->scopeConfig->getValue('javascript_opt_settings/general', ScopeInterface::SCOPE_STORE);
        if($settings3){
            $settings = array_merge($settings,$settings3);
        }
		if ($settings) {
            return $settings;
        }
        return false;
    }
	public function getHooksBeforeOpt()
    {           
		return $this->scopeConfig->getValue('hooks_opt_settings/general', ScopeInterface::SCOPE_STORE);
    }

}
