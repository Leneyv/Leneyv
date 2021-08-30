<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_GdprCookie
 */


declare(strict_types=1);

namespace Amasty\GdprCookie\Plugin\Framework\App\PageCache;

use Amasty\GdprCookie\Model\ConfigProvider;
use Amasty\GdprCookie\Model\CookieManager;
use Magento\Framework\App\PageCache\Identifier;
use Magento\Framework\App\RequestInterface;

class CacheIdentifierPlugin
{
    const CACHE_PREFIX = '_amasty_gdprcookie_allow_cookies';

    /**
     * @var CookieManager
     */
    private $cookieManager;

    /**
     * @var ConfigProvider
     */
    private $configProvider;

    /**
     * @var RequestInterface
     */
    private $request;

    public function __construct(
        CookieManager $cookieManager,
        ConfigProvider $configProvider,
        RequestInterface $request
    ) {
        $this->cookieManager = $cookieManager;
        $this->configProvider = $configProvider;
        $this->request = $request;
    }

    /**
     * @param Identifier $identifier
     * @param string $result
     * @return string
     */
    public function afterGetValue(Identifier $identifier, $result) : string
    {
        if ($this->configProvider->isCookieBarEnabled()
            && strpos($this->request->getRequestUri(), $this->configProvider->getCookieSettingsPage()) !== false
            && $this->cookieManager->getAllowCookies() !== null
        ) {
            $result .= self::CACHE_PREFIX . $this->cookieManager->getAllowCookies();
        }

        return $result;
    }
}
