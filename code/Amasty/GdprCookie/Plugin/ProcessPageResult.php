<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_GdprCookie
 */


namespace Amasty\GdprCookie\Plugin;

use Amasty\GdprCookie\Model\ConfigProvider;
use Amasty\GdprCookie\Model\CookieManager;
use Amasty\GdprCookie\Model\Repository\CookieRepository;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;

class ProcessPageResult
{
    const GOOGLE_TAG_MANAGER_REG = '/\'https:\/\/www\.googletagmanager\.com\/gtm\.js\?id=.*?;/is';
    const COOKIE_GA = '_ga';
    /**
     * @var ConfigProvider
     */
    private $configProvider;
    /**
     * @var CookieManager
     */
    private $cookieManager;
    /**
     * @var CookieRepository
     */
    private $cookieRepository;

    public function __construct(
        ConfigProvider $configProvider,
        CookieManager $cookieManager,
        CookieRepository $cookieRepository
    ) {
        $this->configProvider = $configProvider;
        $this->cookieManager = $cookieManager;
        $this->cookieRepository = $cookieRepository;
    }

    public function aroundRenderResult(ResultInterface $subject, \Closure $proceed, ResponseInterface $response)
    {
        /** @var ResultInterface $result */
        $result = $proceed($response);
        $essentialCookieNames = $this->cookieRepository->getEssentialCookieNames();
        $allowedGroups = $this->cookieManager->getAllowCookies();
        $replaceGa = false;
        $isGaEssential = in_array(self::COOKIE_GA, $essentialCookieNames);

        if (!$this->configProvider->isCookieBarEnabled() || $allowedGroups === CookieManager::ALLOWED_ALL) {
            return $result;
        }

        if ((!$allowedGroups || $allowedGroups === CookieManager::ALLOWED_NONE) && !$isGaEssential) {
            $replaceGa = true;
        }

        if ($allowedGroups) {
            $rejectedCookies = $this->cookieManager->getDisallowedCookieNames($allowedGroups);

            if (in_array(self::COOKIE_GA, $rejectedCookies)) {
                $replaceGa = true;
            }
        }

        if ($replaceGa) {
            $output = $response->getBody();

            if (preg_match(self::GOOGLE_TAG_MANAGER_REG, $output, $match)) {
                $output = preg_replace(self::GOOGLE_TAG_MANAGER_REG, "'';", $output);
                $response->setBody($output);
            }
        }

        return $result;
    }
}
