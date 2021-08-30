<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_GdprCookie
 */


namespace Amasty\GdprCookie\Model;

use Amasty\GdprCookie\Model\Repository\CookieRepository;
use Amasty\GdprCookie\Model\ResourceModel\Cookie\CollectionFactory as CookieCollectionFactory;
use Amasty\GdprCookie\Model\ResourceModel\CookieGroupLink\Collection as LinkCollection;
use Amasty\GdprCookie\Model\ResourceModel\CookieGroupLink\CollectionFactory as LinkCollectionFactory;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Indexer\CacheContext;
use Magento\Framework\Stdlib\Cookie\CookieMetadataFactory;
use Magento\Framework\Stdlib\CookieManagerInterface;
use Magento\Framework\Session\SessionManagerInterface;

class CookieManager
{
    /**#@+*/
    const ALLOW_COOKIES = 'amcookie_allowed';
    const DISALLOWED_COOKIE_NAMES = 'amcookie_disallowed';

    const ALLOWED_NONE = '-1';
    const ALLOWED_ALL = '0';

    /**#@-*/

    /**
     * @var CookieManagerInterface
     */
    private $cookieManager;

    /**
     * @var CookieMetadataFactory
     */
    private $cookieMetadataFactory;

    /**
     * @var SessionManagerInterface
     */
    private $sessionManager;

    /**
     * @var LinkCollectionFactory
     */
    private $linkCollectionFactory;

    /**
     * @var CookieCollectionFactory
     */
    private $cookieCollectionFactory;

    /**
     * @var CacheContext
     */
    private $cacheContext;

    /**
     * @var ManagerInterface
     */
    private $eventManager;

    /**
     * Storage for essential cookies. Must not delete them even if no decision was taken
     *
     * @var array
     */
    private $essentialCookies;

    public function __construct(
        CookieManagerInterface $cookieManager,
        CookieMetadataFactory $cookieMetadataFactory,
        SessionManagerInterface $sessionManager,
        CacheContext $cacheContext,
        ManagerInterface $eventManager,
        LinkCollectionFactory $linkCollectionFactory,
        CookieCollectionFactory $cookieCollectionFactory,
        CookieRepository $cookieRepository
    ) {
        $this->cookieManager = $cookieManager;
        $this->cookieMetadataFactory = $cookieMetadataFactory;
        $this->sessionManager = $sessionManager;
        $this->linkCollectionFactory = $linkCollectionFactory;
        $this->cookieCollectionFactory = $cookieCollectionFactory;
        $this->cacheContext = $cacheContext;
        $this->eventManager = $eventManager;
        $this->essentialCookies = $cookieRepository->getEssentialCookieNames();
    }

    public function setIsAllowCookies($cookies)
    {
        $cookieMetadata = $this->cookieMetadataFactory->createPublicCookieMetadata()
            ->setPath($this->sessionManager->getCookiePath())
            ->setDomain($this->sessionManager->getCookieDomain())
            ->setDurationOneYear();

        try {
            $this->cookieManager->setPublicCookie(self::ALLOW_COOKIES, $cookies, $cookieMetadata);
            $this->cookieManager->setPublicCookie(
                self::DISALLOWED_COOKIE_NAMES,
                implode(',', $this->getDisallowedCookieNames($cookies)),
                $cookieMetadata
            );
            $this->cacheContext->registerTags([CookieGroupLink::CACHE_TAG, CookieGroup::CACHE_TAG]);
            $this->eventManager->dispatch('clean_cache_by_tags', ['object' => $this->cacheContext]);
        } catch (\Exception $e) {
            null;
        }
    }

    /**
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Stdlib\Cookie\CookieSizeLimitReachedException
     * @throws \Magento\Framework\Stdlib\Cookie\FailureToSendException
     */
    public function unsetIsAllowCookies()
    {
        $this->cookieManager->deleteCookie(
            self::ALLOW_COOKIES,
            $this->cookieMetadataFactory
                ->createPublicCookieMetadata()
                ->setPath($this->sessionManager->getCookiePath())
                ->setDomain($this->sessionManager->getCookieDomain())
        );
    }

    /**
     * @return string|null
     */
    public function getAllowCookies()
    {
        return $this->cookieManager->getCookie(self::ALLOW_COOKIES);
    }

    /**
     * @param array $cookies
     */
    public function deleteCookies($cookies)
    {
        try {
            foreach ($cookies as $cookie) {
                if (in_array($this->essentialCookies, $cookie)) {
                    continue;
                }

                if ($this->cookieManager->getCookie($cookie)) {
                    $cookieMetadata = $this->cookieMetadataFactory
                        ->createPublicCookieMetadata()
                        ->setPath($this->sessionManager->getCookiePath())
                        ->setDomain($this->sessionManager->getCookieDomain());
                    $this->cookieManager->deleteCookie($cookie, $cookieMetadata);
                }
            }
        } catch (\Exception $e) {
            null;
        }
    }

    public function getDisallowedCookieNames(string $allowedGroupIds): array
    {
        if ($allowedGroupIds === self::ALLOWED_ALL) {
            return [];
        }

        /** @var LinkCollection $linkCollection */
        $linkCollection = $this->linkCollectionFactory->create();
        $linkCollection->addFieldToFilter('group_id', ['nin' => $allowedGroupIds]);
        $disallowedCookieIds = $linkCollection->getColumnValues('cookie_id');
        $cookieCollection = $this->cookieCollectionFactory->create();
        $cookieCollection->addFieldToFilter('id', ['in' => $disallowedCookieIds]);

        return $cookieCollection->getColumnValues('name');
    }
}
