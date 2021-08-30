<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_GdprCookie
 */


namespace Amasty\GdprCookie\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class ConfigProvider extends \Amasty\Base\Model\ConfigProviderAbstract
{
    /**#@+
     * Constants defined for xpath of system configuration
     */
    const COOKIE_POLICY_BAR = 'cookie_policy/bar';

    const COOKIE_POLICY_BAR_TYPE = 'cookie_bar_customisation/cookies_bar_style';

    const CONFIRMATION_BAR_TEXT = 'cookie_policy/confirmation_bar_text';

    const COOKIE_WEBSITE_INTERACTION = 'cookie_policy/website_interaction';

    const FIRST_VISIT_SHOW = 'cookie_policy/first_visit_show';

    const ALLOWED_URLS = 'cookie_policy/allowed_urls';

    const SETTINGS_PAGE = 'cookie_policy/cms_to_show';

    const AUTO_CLEAR_LOG_DAYS = 'cookie_policy/auto_cleaning_days';

    const BACKGROUND_BAR_COLLOR = 'cookie_bar_customisation/background_color_cookies';

    const BUTTONS_BAR_COLLOR = 'cookie_bar_customisation/buttons_color_cookies';

    const TEXT_BAR_COLLOR = 'cookie_bar_customisation/text_color_cookies';

    const LINK_BAR_COLLOR = 'cookie_bar_customisation/link_color_cookies';

    const BUTTONS_TEXT_BAR_COLLOR = 'cookie_bar_customisation/buttons_text_color_cookies';

    const COOKIE_BAR_LOCATION = 'cookie_bar_customisation/cookies_bar_location';

    const BUTTON_BAR_SAVE_COLOR = 'cookie_bar_customisation/buttons_color_cookies_save';

    const BUTTON_SAVE_BAR_TEXT_COLOR = 'cookie_bar_customisation/buttons_text_color_cookies_save';

    const BUTTON_SETTINGS_BAR_TEXT_COLOR = 'cookie_bar_customisation/buttons_text_color_cookies_settings';

    const BUTTON_BAR_SETTINGS_COLOR = 'cookie_bar_customisation/buttons_color_cookies_settings';

    const HEADER_TEXT_BAR_COLOR = 'cookie_bar_customisation/header_text_color_cookies';

    const DESCRIPTION_TEXT_BAR_COLOR = 'cookie_bar_customisation/description_text_color_cookies';
    /**#@-*/

    protected $pathPrefix = 'amasty_gdprcookie/';

    /**
     * @param null|string $scopeCode
     *
     * @return bool
     */
    public function isCookieBarEnabled($scopeCode = null)
    {
        return (bool)$this->getValue(self::COOKIE_POLICY_BAR, $scopeCode);
    }

    /**
     * @param null $scopeCode
     * @return int
     */
    public function getCookiePrivacyBarType($scopeCode = null)
    {
        return (int)$this->getValue(self::COOKIE_POLICY_BAR_TYPE, $scopeCode);
    }

    /**
     * @param null|string $scopeCode
     *
     * @return null|string
     */
    public function getConfirmationBarText($scopeCode = null)
    {
        return $this->getValue(self::CONFIRMATION_BAR_TEXT, $scopeCode);
    }

    /**
     * @param null|int $scopeCode
     *
     * @return int
     */
    public function getCookieWebsiteInteraction($scopeCode = null)
    {
        return (int)$this->getValue(self::COOKIE_WEBSITE_INTERACTION, $scopeCode);
    }

    /**
     * @param null|int $scopeCode
     *
     * @return int
     */
    public function getFirstVisitShow($scopeCode = null)
    {
        return (int)$this->getValue(self::FIRST_VISIT_SHOW, $scopeCode);
    }

    /**
     * @param null|int $scopeCode
     *
     * @return string|null
     */
    public function getBackgroundBarCollor($scopeCode = null)
    {
        return $this->getValue(self::BACKGROUND_BAR_COLLOR, $scopeCode);
    }

    /**
     * @param null|int $scopeCode
     *
     * @return string
     */
    public function getCookieSettingsPage($scopeCode = null)
    {
        return $this->getValue(self::SETTINGS_PAGE, $scopeCode);
    }

    /**
     * @param null|string $scopeCode
     *
     * @return null|string
     */
    public function getButtonBarCollor($scopeCode = null)
    {
        return $this->getValue(self::BUTTONS_BAR_COLLOR, $scopeCode);
    }

    /**
     * @param null|string $scopeCode
     *
     * @return null|string
     */
    public function getTextBarCollor($scopeCode = null)
    {
        return $this->getValue(self::TEXT_BAR_COLLOR, $scopeCode);
    }

    /**
     * @param null|string $scopeCode
     *
     * @return null|string
     */
    public function getLinksBarCollor($scopeCode = null)
    {
        return $this->getValue(self::LINK_BAR_COLLOR, $scopeCode);
    }

    /**
     * @param null|string $scopeCode
     *
     * @return null|string
     */
    public function getButtonTextBarCollor($scopeCode = null)
    {
        return $this->getValue(self::BUTTONS_TEXT_BAR_COLLOR, $scopeCode);
    }

    /**
     * @param null|string $scopeCode
     *
     * @return null|string
     */
    public function getBarLocation($scopeCode = null)
    {
        return $this->getValue(self::COOKIE_BAR_LOCATION, $scopeCode);
    }

    /**
     * @param null|string $scopeCode
     *
     * @return null|string
     */
    public function getAllowedUrls($scopeCode = null)
    {
        return $this->getValue(self::ALLOWED_URLS, $scopeCode);
    }

    /**
     * @param null|string $scopeCode
     *
     * @return null|string
     */
    public function getButtonSaveTextColor($scopeCode = null)
    {
        return $this->getValue(self::BUTTON_SAVE_BAR_TEXT_COLOR, $scopeCode);
    }

    /**
     * @param null|string $scopeCode
     *
     * @return null|string
     */
    public function getButtonSaveColor($scopeCode = null)
    {
        return $this->getValue(self::BUTTON_BAR_SAVE_COLOR, $scopeCode);
    }

    /**
     * @param null|string $scopeCode
     *
     * @return null|string
     */
    public function getButtonSettingsTextColor($scopeCode = null)
    {
        return $this->getValue(self::BUTTON_SETTINGS_BAR_TEXT_COLOR, $scopeCode);
    }

    /**
     * @param null|string $scopeCode
     *
     * @return null|string
     */
    public function getButtonSettingsColor($scopeCode = null)
    {
        return $this->getValue(self::BUTTON_BAR_SETTINGS_COLOR, $scopeCode);
    }

    /**
     * @param null|int $scopeCode
     *
     * @return int
     */
    public function getAutoCleaningDays($scopeCode = null)
    {
        return (int)$this->getValue(self::AUTO_CLEAR_LOG_DAYS, $scopeCode);
    }

    /**
     * @param null|string $scopeCode
     *
     * @return null|string
     */
    public function getHeaderTextBarColor($scopeCode = null)
    {
        return $this->getValue(self::HEADER_TEXT_BAR_COLOR, $scopeCode);
    }

    /**
     * @param null|string $scopeCode
     *
     * @return null|string
     */
    public function getDescriptionTextBarColor($scopeCode = null)
    {
        return $this->getValue(self::DESCRIPTION_TEXT_BAR_COLOR, $scopeCode);
    }
}
