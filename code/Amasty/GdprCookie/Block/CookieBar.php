<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_GdprCookie
 */


namespace Amasty\GdprCookie\Block;

use Amasty\GdprCookie\Model\ConfigProvider;
use Amasty\GdprCookie\Model\CookieGroupsProcessor;
use Magento\Cms\Model\Template\Filter as CmsTemplateFilter;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\View\Element\Template;

class CookieBar extends Template
{
    const HOMEPAGE_KEY = 'homepage';

    /**
     * @var string
     */
    protected $_template = 'Amasty_GdprCookie::cookiebar.phtml';

    /**
     * @var ConfigProvider
     */
    private $configProvider;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    private $urlInterface;
    /**
     * @var Json
     */
    private $jsonSerializer;
    /**
     * @var CmsTemplateFilter
     */
    private $cmsTemplateFilter;
    /**
     * @var CookieGroupsProcessor
     */
    private $cookieGroupsProcessor;

    public function __construct(
        ConfigProvider $configProvider,
        Template\Context $context,
        Json $jsonSerializer,
        CookieGroupsProcessor $cookieGroupsProcessor,
        CmsTemplateFilter $cmsTemplateFilter,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->configProvider = $configProvider;
        // backward compatibility with the Magento 2.1, to avoid compilation issue;
        $this->urlInterface = $context->getUrlBuilder() ?:
            \Magento\Framework\App\ObjectManager::getInstance()->get(\Magento\Framework\UrlInterface::class);
        $this->jsonSerializer = $jsonSerializer;
        $this->cmsTemplateFilter = $cmsTemplateFilter;
        $this->cookieGroupsProcessor = $cookieGroupsProcessor;
    }

    /**
     * @return int
     */
    public function isProcessFirstShow()
    {
        return $this->configProvider->getFirstVisitShow();
    }

    /**
     * @return string
     */
    public function getCookiePolicyText()
    {
        $text = $this->cmsTemplateFilter->filter($this->configProvider->getConfirmationBarText());

        return $this->jsonSerializer->serialize($text);
    }

    /**
     * @return string
     */
    public function getAllowLink()
    {
        return $this->_urlBuilder->getUrl('gdprcookie/cookie/allow');
    }

    /**
     * @return string
     */
    public function getSettingsLink()
    {
        return $this->getUrl($this->configProvider->getCookieSettingsPage());
    }

    /**
     * @return int
     */
    public function getNoticeType()
    {
        return (int)$this->configProvider->getCookiePrivacyBarType();
    }

    /**
     * @return int
     */
    public function isNotice()
    {
        return (bool)$this->configProvider->isCookieBarEnabled();
    }

    /**
     * @return int
     */
    public function getWebsiteInteraction()
    {
        $websiteInteraction = (int)$this->configProvider->getCookieWebsiteInteraction();

        if ($websiteInteraction && $this->isAllowedPage($this->urlInterface->getCurrentUrl())) {
            return 0;
        }

        return $websiteInteraction;
    }

    /**
     * @return null|string
     */
    public function getTextBarCollor()
    {
        if (!$this->hasData('text_color_cookies')) {
            $this->setData('text_color_cookies', $this->configProvider->getTextBarCollor());
        }

        return $this->getData('text_color_cookies');
    }

    /**
     * @return null|string
     */
    public function getBackgroundBarCollor()
    {
        if (!$this->hasData('background_color_cookies')) {
            $this->setData('background_color_cookies', $this->configProvider->getBackgroundBarCollor());
        }

        return $this->getData('background_color_cookies');
    }

    /**
     * @return null|string
     */
    public function getButtonBarCollor()
    {
        if (!$this->hasData('buttons_color_cookies')) {
            $this->setData('buttons_color_cookies', $this->configProvider->getButtonBarCollor());
        }

        return $this->getData('buttons_color_cookies');
    }

    /**
     * @return null|string
     */
    public function getButtonTextBarCollor()
    {
        if (!$this->hasData('buttons_text_color_cookies')) {
            $this->setData('buttons_text_color_cookies', $this->configProvider->getButtonTextBarCollor());
        }

        return $this->getData('buttons_text_color_cookies');
    }

    /**
     * @return null|string
     */
    public function getLinksBarCollor()
    {
        if (!$this->hasData('link_color_cookies')) {
            $this->setData('link_color_cookies', $this->configProvider->getLinksBarCollor());
        }

        return $this->getData('link_color_cookies');
    }

    /**
     * @return null|string
     */
    public function getBarLocation()
    {
        if (!$this->hasData('cookies_bar_location')) {
            $this->setData('cookies_bar_location', $this->configProvider->getBarLocation());
        }

        return $this->getData('cookies_bar_location');
    }

    /**
     * @return bool|false|string
     */
    public function getAllGroups()
    {
        $storeId = $this->_storeManager->getStore()->getId();
        $groups = $this->cookieGroupsProcessor->getAllGroups($storeId);

        return $this->jsonSerializer->serialize($groups);
    }

    /**
     * Convert string to array
     *
     * @param string $string
     * @return array|false
     */
    protected function stringValidationAndConvertToArray($string)
    {
        $validate = function ($urls) {
            return preg_split('|\s*[\r\n]+\s*|', $urls, -1, PREG_SPLIT_NO_EMPTY);
        };

        return $validate($string);
    }

    /**
     * Check if current page is allowed for interaction
     *
     * @param string $currentUrl
     *
     * @return bool
     */
    protected function isAllowedPage($currentUrl)
    {
        $urls = trim($this->configProvider->getAllowedUrls());
        $urls = $urls ? $this->stringValidationAndConvertToArray($urls) : [];

        if (in_array(self::HOMEPAGE_KEY, $urls)
            && $this->isHomePage()
        ) {
            return true;
        }

        foreach ($urls as $url) {
            if (false !== strpos($currentUrl, $url)) {
                return true;
            }
        }

        return false;
    }

    private function isHomePage()
    {
        $currentUrl = $this->getUrl('', ['_current' => true]);
        $urlRewrite = $this->getUrl('*/*/*', ['_current' => true, '_use_rewrite' => true]);

        return $currentUrl == $urlRewrite;
    }

    /**
     * @return null|string
     */
    public function getButtonSettingsColor()
    {
        if (!$this->hasData(ConfigProvider::BUTTON_BAR_SETTINGS_COLOR)) {
            $this->setData(ConfigProvider::BUTTON_BAR_SETTINGS_COLOR, $this->configProvider->getButtonSettingsColor());
        }

        return $this->getDataByKey(ConfigProvider::BUTTON_BAR_SETTINGS_COLOR);
    }

    /**
     * @return null|string
     */
    public function getButtonSettingsTextColor()
    {
        if (!$this->hasData(ConfigProvider::BUTTON_SETTINGS_BAR_TEXT_COLOR)) {
            $this->setData(
                ConfigProvider::BUTTON_SETTINGS_BAR_TEXT_COLOR,
                $this->configProvider->getButtonSettingsTextColor()
            );
        }

        return $this->getDataByKey(ConfigProvider::BUTTON_SETTINGS_BAR_TEXT_COLOR);
    }

    /**
     * @return null|string
     */
    public function getButtonSaveColor()
    {
        if (!$this->hasData(ConfigProvider::BUTTON_BAR_SAVE_COLOR)) {
            $this->setData(ConfigProvider::BUTTON_BAR_SAVE_COLOR, $this->configProvider->getButtonSaveColor());
        }

        return $this->getDataByKey(ConfigProvider::BUTTON_BAR_SAVE_COLOR);
    }

    /**
     * @return null|string
     */
    public function getButtonSaveTextColor()
    {
        if (!$this->hasData(ConfigProvider::BUTTON_SAVE_BAR_TEXT_COLOR)) {
            $this->setData(
                ConfigProvider::BUTTON_SAVE_BAR_TEXT_COLOR,
                $this->configProvider->getButtonSaveTextColor()
            );
        }

        return $this->getDataByKey(ConfigProvider::BUTTON_SAVE_BAR_TEXT_COLOR);
    }

    /**
     * @return null|string
     */
    public function getHeaderTextBarColor()
    {
        if (!$this->hasData(ConfigProvider::HEADER_TEXT_BAR_COLOR)) {
            $this->setData(
                ConfigProvider::HEADER_TEXT_BAR_COLOR,
                $this->configProvider->getHeaderTextBarColor()
            );
        }

        return $this->getDataByKey(ConfigProvider::HEADER_TEXT_BAR_COLOR);
    }

    /**
     * @return null|string
     */
    public function getDescriptionTextBarColor()
    {
        if (!$this->hasData(ConfigProvider::DESCRIPTION_TEXT_BAR_COLOR)) {
            $this->setData(
                ConfigProvider::DESCRIPTION_TEXT_BAR_COLOR,
                $this->configProvider->getDescriptionTextBarColor()
            );
        }

        return $this->getDataByKey(ConfigProvider::DESCRIPTION_TEXT_BAR_COLOR);
    }
}
