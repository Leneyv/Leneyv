<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace WeSupply\Toolbox\Block\Adminhtml\Order\View\Tab;

use Magento\Framework\Phrase;

/**
 * Class WeSupply
 * @package WeSupply\Toolbox\Block\Adminhtml\Order\View\Tab\WeSupply
 */
class ReturnsView extends WeSupplyDashboard
{
    /**
     * @var string
     */
    protected $_template = 'order/view/tab/wesupply/embedded/wesupply_returns_view.phtml';

    /**
     * @return Phrase|string
     */
    public function getTabLabel()
    {
        return __($this->tabLabel . ' Returns List');
    }

    /**
     * @return Phrase|string
     */
    public function getTabTitle()
    {
        return __($this->tabLabel . ' Returns List');
    }

    /**
     * @return bool
     */
    public function canShowTab()
    {
        if (
            $this->helper->getWeSupplyEnabled() &&
            $this->helper->getEnableWeSupplyAdminReturns()
        ) {
            return TRUE;
        }

        return FALSE;
    }

    /**
     * @return bool
     */
    public function isHidden()
    {
        if (
            $this->helper->getWeSupplyEnabled() &&
            $this->helper->getEnableWeSupplyAdminReturns()
        ) {
            return FALSE;
        }

        return TRUE;
    }

    /**
     * @return string
     */
    public function getReturnsListIframeUrl()
    {
        return  $this->helper->getWesupplyFullDomain() . $this->getWsAdminKey() . '/admin?view=returns&platformType=' . $this->getPlatformType() . '&orderid=' . $this->getOrderIncrementId();
    }
}
