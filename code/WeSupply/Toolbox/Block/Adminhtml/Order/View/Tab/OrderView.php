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
class OrderView extends WeSupplyDashboard
{
    /**
     * @var string
     */
    protected $_template = 'order/view/tab/wesupply/embedded/wesupply_order_view.phtml';

    /**
     * @return Phrase|string
     */
    public function getTabLabel()
    {
        return __($this->tabLabel . ' Order View');
    }

    /**
     * @return Phrase|string
     */
    public function getTabTitle()
    {
        return __($this->tabLabel . ' Order View');
    }

    /**
     * @return bool
     */
    public function canShowTab()
    {
        if (
            $this->helper->getWeSupplyEnabled() &&
            $this->helper->getEnableWeSupplyAdminOrder()
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
            $this->helper->getEnableWeSupplyAdminOrder()
        ) {
            return FALSE;
        }

        return TRUE;
    }

    /**
     * @return string
     */
    public function getOrderViewIframeUrl()
    {
        return  $this->helper->getWesupplyFullDomain() .
            $this->getWsAdminKey() .
            '/admin/order/mage_' . $this->getOrderId() .
            '?platformType=' . $this->getPlatformType() .
            '&request=provideradmin';
    }
}
