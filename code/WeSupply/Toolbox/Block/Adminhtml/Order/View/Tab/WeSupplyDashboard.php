<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace WeSupply\Toolbox\Block\Adminhtml\Order\View\Tab;

use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Backend\Model\Auth\Session as AuthSession;
use Magento\Framework\Registry;
use Magento\User\Model\User;
use WeSupply\Toolbox\Helper\Data as WeSupplyHelper;
use WeSupply\Toolbox\Logger\Logger;

class WeSupplyDashboard extends Template implements TabInterface
{
    /**
     * @var Registry
     */
    protected $_coreRegistry;

    /**
     * @var AuthSession
     */
    protected $authSession;

    /**
     * @var WeSupplyHelper
     */
    protected $helper;

    /**
     * @var Logger
     */
    protected $logger;

    /**
     * @var string
     */
    protected $tabLabel = 'WeSupply';

    /**
     * @var string
     */
    protected $tabTitle = 'WeSupply';

    /**
     * WeSupplyBase constructor.
     * @param Context $context
     * @param Registry $registry
     * @param AuthSession $authSession
     * @param WeSupplyHelper $helper
     * @param Logger $logger
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        AuthSession $authSession,
        WeSupplyHelper $helper,
        Logger $logger,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        $this->authSession = $authSession;
        $this->helper = $helper;
        $this->logger = $logger;

        parent::__construct($context, $data);
    }

    /**
     * @return bool
     */
    public function canShowTab()
    {
        if ($this->helper->getWeSupplyEnabled()) {
            return TRUE;
        }

        return FALSE;
    }

    /**
     * @return bool
     */
    public function isHidden()
    {
        if ($this->helper->getWeSupplyEnabled()) {
            return FALSE;
        }

        return TRUE;
    }

    /**
     * @return string
     */
    public function getTabLabel()
    {
        return $this->tabLabel;
    }

    /**
     * @return string
     */
    public function getTabTitle()
    {
        return $this->tabTitle;
    }

    public function isWsAdminLoggedIn()
    {
        return $this->authSession->getWsLoggedIn();
    }

    /**
     * @return mixed|string|null
     */
    public function getAdminEmail()
    {
        return $this->getAdminUser()->getEmail();
    }

    /**
     * @return string
     */
    public function getWeSupplyAdminLoginUrl()
    {
        return $this->loadIframe() ?
            $this->helper->getWesupplyFullDomain()
            . 'admin/auto-login?adminEmail=' . urlencode($this->getAdminEmail())
            . '&authBy=' . 'client_secret'
            . '&hash=' . $this->getWsAuthHash() : '';
    }

    /**
     * Check if should load auto-login iframe
     * @return bool
     */
    public function loadIframe()
    {
        return $this->helper->getWeSupplyEnabled() && !empty($this->helper->getWesupplyFullDomain()) &&
            ($this->helper->getEnableWeSupplyAdminOrder() || $this->helper->getEnableWeSupplyAdminReturns());
    }

        /**
     * @return mixed
     */
    protected function getWsAuthHash()
    {
        return $this->authSession->getWsAuthHash();
    }

    /**
     * @return mixed
     */
    protected function getWsAdminKey()
    {
        return $this->authSession->getWsAdminKey();
    }

    /**
     * @return string
     */
    protected function getPlatformType()
    {
        return $this->helper->getPlatform();
    }

    /**
     * @return mixed
     */
    protected function getOrderIncrementId()
    {
        return $this->getOrder()->getIncrementId();
    }

    /**
     * @return mixed
     */
    protected function getOrderId()
    {
        return $this->getOrder()->getEntityId();
    }

    /**
     * @return mixed
     */
    private function getOrder()
    {
        return $this->_coreRegistry->registry('current_order');
    }

    /**
     * @return User|null
     */
    private function getAdminUser()
    {
        return $this->authSession->getUser();
    }
}
