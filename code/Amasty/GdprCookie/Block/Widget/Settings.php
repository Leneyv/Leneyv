<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_GdprCookie
 */


namespace Amasty\GdprCookie\Block\Widget;

use Amasty\GdprCookie\Model\ConfigProvider;
use Amasty\GdprCookie\Model\CookieGroup;
use Amasty\GdprCookie\Model\CookieGroupLink;
use Amasty\GdprCookie\Model\CookieGroupsProcessor;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\View\Element\Template;
use Magento\Widget\Block\BlockInterface;

class Settings extends Template implements BlockInterface, IdentityInterface
{
    protected $_template = "widget/settings.phtml";

    /**
     * @var ConfigProvider
     */
    private $configProvider;
    /**
     * @var CookieGroupsProcessor
     */
    private $cookieGroupsProcessor;

    public function __construct(
        ConfigProvider $configProvider,
        CookieGroupsProcessor $cookieGroupsProcessor,
        Template\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->configProvider = $configProvider;
        $this->cookieGroupsProcessor = $cookieGroupsProcessor;
    }

    /**
     * @return array
     */
    public function getAllGroups()
    {
        $storeId = $this->_storeManager->getStore()->getId();
        $result = $this->cookieGroupsProcessor->getAllGroups($storeId);

        return $result;
    }

    public function isNeedToShow()
    {
        if (!$this->configProvider->isCookieBarEnabled()) {
            return false;
        }

        return true;
    }

    public function getIdentities()
    {
        return [CookieGroupLink::CACHE_TAG, CookieGroup::CACHE_TAG];
    }

    public function getCacheLifetime()
    {
        return null;
    }

    public function getCacheTags()
    {
        return $this->getIdentities();
    }
}
