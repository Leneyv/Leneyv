<?php
/**
 * BSS Commerce Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://bsscommerce.com/Bss-Commerce-License.txt
 *
 * @category   BSS
 * @package    Bss_LazyImageLoader
 * @author     Extension Team
 * @copyright  Copyright (c) 2018-2019 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */

namespace Bss\LazyImageLoader\Model\Observer;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;

class LazyImage implements ObserverInterface
{
    /**
     * @var \Bss\LazyImageLoader\Helper\Data
     */
    protected $helper;

    public function __construct(
        \Bss\LazyImageLoader\Helper\Data $helper
    ) {
        $this->helper = $helper;
    }

    /**
     * @param EventObserver $observer
     */
    public function execute(EventObserver $observer)
    {
        $request = $observer->getEvent()->getRequest();

        if ($request->isAjax()) {
            return;
        }
        
        $response = $observer->getEvent()->getResponse();
        if (!$response) {
            return;
        }

        $html = $response->getBody();
        if ($html == '') {
            return;
        }

        if (!$this->helper->isEnabled($html)) {
            return;
        }
        $html = $this->helper->lazyLoad($html);
        $response->setBody($html);
    }
}
