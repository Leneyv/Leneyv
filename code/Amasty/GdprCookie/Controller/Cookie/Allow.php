<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_GdprCookie
 */


namespace Amasty\GdprCookie\Controller\Cookie;

use Amasty\GdprCookie\Model\CookieConsent;
use Amasty\GdprCookie\Model\CookieConsentLogger;
use Amasty\GdprCookie\Model\CookieManager;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Context;

class Allow extends \Magento\Framework\App\Action\Action
{
    /**
     * @var CookieManager
     */
    private $cookieManager;

    /**
     * @var Session
     */
    private $session;

    /**
     * @var CookieConsentLogger
     */
    private $consentLogger;

    public function __construct(
        Context $context,
        CookieManager $cookieManager,
        Session $session,
        CookieConsentLogger $consentLogger
    ) {
        parent::__construct($context);

        $this->cookieManager = $cookieManager;
        $this->session = $session;
        $this->consentLogger = $consentLogger;
    }

    public function execute()
    {
        $customerId = $this->session->getCustomerId();

        if ($customerId) {
            $this->consentLogger->logCookieConsent($customerId, __('All Allowed'));
        }
        $this->cookieManager->setIsAllowCookies(CookieManager::ALLOWED_ALL);
    }
}
