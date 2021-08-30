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
namespace Bss\LazyImageLoader\Block;

class LazyLoad extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Bss\LazyImageLoader\Helper\Data
     */
    protected $helper;

    /**
     * LazyLoad constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Bss\LazyImageLoader\Helper\Data $helper
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Bss\LazyImageLoader\Helper\Data $helper,
        array $data = []
    ) {
        $this->helper = $helper;
        parent::__construct($context, $data);
    }

    /**
     * @return \Bss\LazyImageLoader\Helper\Data
     */
    public function getHelper()
    {
        return $this->helper;
    }
}
