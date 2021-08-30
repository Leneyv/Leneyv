<?php
namespace Codedecorator\Custom\Plugin\Model;

use Magento\Store\Model\StoreManagerInterface;

class Config  {


    protected $_storeManager;

    public function __construct(
        StoreManagerInterface $storeManager
    ) {
        $this->_storeManager = $storeManager;
    }


    public function afterGetAttributeUsedForSortByArray(\Magento\Catalog\Model\Config $catalogConfig, $options)
    {
        $store = $this->_storeManager->getStore();
        $currencySymbol = $store->getCurrentCurrency()->getCurrencySymbol();

        // Remove specific default sorting options
        $default_options = [];
        $default_options['name'] = $options['name'];

        unset($options['position']);
        unset($options['name']);

        //New sorting options
        $options['created_at'] = 'New';
        $options['updated_at'] = 'Old';


        //Merge default sorting options with custom options
        //$options = array_merge($customOption, $options);
        \Magento\Framework\App\ObjectManager::getInstance()->get('Psr\Log\LoggerInterface')->info("===============");
        \Magento\Framework\App\ObjectManager::getInstance()->get('Psr\Log\LoggerInterface')->info(print_r($options,true));
        return $options;
    }
}