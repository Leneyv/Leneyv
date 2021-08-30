<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_Product360view
 * @author    Webkul
 * @copyright Copyright (c)  Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\Product360view\Block;

use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\Registry;
use Webkul\Product360view\Model\Product360viewFactory;
use Magento\Catalog\Model\ProductFactory;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use Webkul\Product360view\Helper\Data as HelperData;

class Productpage extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var Registry
     */
    protected $coreRegistry = null;

    /**
     * @var ProductFactory
     */
    protected $productFactory;

    /**
     * @var Product360viewFactory
     */
    protected $product360viewFactory;

    /**
     * @var HelperData
     */
    protected $helper;
    
    /**
     * @param Context $context
     * @param StoreManagerInterface $storeManager
     * @param Registry $registry
     * @param ProductFactory $productFactory
     * @param Product360viewFactory $product360viewFactory
     * @param HelperData $helper
     * @param array $data
     */
    public function __construct(
        Context $context,
        StoreManagerInterface $storeManager,
        Registry $registry,
        ProductFactory $productFactory,
        Product360viewFactory $product360viewFactory,
        HelperData $helper,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->storeManager = $storeManager;
        $this->coreRegistry = $registry;
        $this->productFactory = $productFactory;
        $this->helper = $helper;
        $this->product360viewFactory = $product360viewFactory;
    }

    /**
     * Get Product Entity Id
     *
     * @return Entity Id
     */
    public function getProductId()
    {
        return $this->coreRegistry->registry('product')->getEntityId();
    }

     /**
      * Count Media Images
      *
      * @return Entity Id
      */
    public function countMediaImages()
    {
        return $this->productFactory->create()->load($this->getProductId())->getMediaGalleryImages()->getSize();
    }

    /**
     * Get Product 360 view status
     *
     * @return boolean
     */
    public function canShowThumbnail()
    {
        $productType = $this->coreRegistry->registry('product')->getTypeId();
        if ($productType == 'virtual' || $productType == 'downloadable') {
            return false;
        }
        return $this->productFactory->create()->load($this->getProductId())->getProduct360ViewShow();
    }

    /**
     * Get Helper Object
     *
     * @return object
     */
    public function getHelperObject()
    {
        return $this->helper;
    }
}
