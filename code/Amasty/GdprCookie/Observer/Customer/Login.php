<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_GdprCookie
 */


namespace Amasty\GdprCookie\Observer\Customer;

use Amasty\GdprCookie\Model\CookieConsent;
use Amasty\GdprCookie\Model\CookieConsentLogger;
use Amasty\GdprCookie\Model\CookieManager;
use Amasty\GdprCookie\Model\Repository\CookieGroupsRepository;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class Login implements ObserverInterface
{
    /**
     * @var CookieManager
     */
    private $cookieManager;

    /**
     * @var CookieConsentLogger
     */
    private $consentLogger;

    /**
     * @var CookieGroupsRepository
     */
    private $groupsRepository;

    public function __construct(
        CookieManager $cookieManager,
        CookieConsentLogger $consentLogger,
        CookieGroupsRepository $groupsRepository
    ) {
        $this->cookieManager = $cookieManager;
        $this->consentLogger = $consentLogger;
        $this->groupsRepository = $groupsRepository;
    }

    /**
     * @param Observer $observer
     *
     * @return $this|void
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute(Observer $observer)
    {
        $cookies = $this->cookieManager->getAllowCookies();

        if ($cookies === null) {
            return;
        }

        if ($cookies !== CookieManager::ALLOWED_ALL && $cookies !== CookieManager::ALLOWED_NONE) {
            $status = '';
            $groups = explode(',', $cookies);

            foreach ($groups as $group) {
                $groupName = $this->groupsRepository->getById($group)->getName();
                $status .= '<strong>' . $groupName . ':</strong> Allowed<br/>';
            }
        } elseif ($cookies === CookieManager::ALLOWED_NONE) {
            $status = __('None cookies allowed');
        } else {
            $status = __('All Allowed');
        }
        $customerId = $observer->getData('customer')->getData('entity_id');

        $this->consentLogger->logCookieConsent($customerId, $status);
    }
}
