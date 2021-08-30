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
namespace Webkul\Product360view\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Escaper;
use Webkul\Product360view\Model\Product360viewFactory;
use Magento\Framework\Serialize\Serializer\Json;

/**
 * Product360view data helper.
 */
class Data extends AbstractHelper
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Framework\Escaper
     */
    protected $escaper;

    /**
     * @var ScopeConfig
     */
    protected $scopeConfig;

    /**
     * @var Product360viewFactory
     */
    protected $product360viewFactory;

    /**
     * @var Json
     */
    protected $json;

    /**
     * @param Context $context
     * @param StoreManagerInterface $storeManager
     * @param Escaper $escaper
     * @param Product360viewFactory $product360viewFactory
     * @param Json $json
     */
    public function __construct(
        Context $context,
        StoreManagerInterface $storeManager,
        Escaper $escaper,
        Product360viewFactory $product360viewFactory,
        Json $json
    ) {
        $this->storeManager = $storeManager;
        $this->escaper = $escaper;
        $this->scopeConfig = $context->getScopeConfig();
        $this->product360viewFactory = $product360viewFactory;
        $this->json = $json;
        parent::__construct($context);
    }

    /**
     * Get media url
     *
     * @return string
     */
    public function getmediaUrl()
    {
        return $this->storeManager->getStore()->getBaseUrl(
            \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
        ).'product360view';
    }

    /**
     * Get Product 360 Image Configuration Data
     *
     * @param string $field
     * @return string
     */
    public function getConfigData($field)
    {
        return $this->escaper->escapeHtml($this->scopeConfig->getValue(
            'product360view/general_settings/'.$field,
            ScopeInterface::SCOPE_STORE
        ));
    }

    /**
     * Get product 360 image data.
     *
     * @param int $productId
     * @param boolean $file
     * @return array
     */
    public function getProductImagesJson($productId, $file = false)
    {
        $productId = $productId;
        $mediaUrl = $this->storeManager->getStore()->getBaseUrl(
            \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
        );
        $mediaData = $this->product360viewFactory->create()
            ->getCollection()
            ->addFieldToFilter('entity_id', ['eq' => $productId])
            ->getData();
        $productImages = [];
        $productImage = [];
        
        foreach ($mediaData as $data) {
            if ($file) {
                $productImage['url'] = $mediaUrl.'product360view'.$data['file'];
                $productImage['file'] = $data['file'];
                $pos = strrpos($data['file'], "/");
                $productImage['filename'] = substr($data['file'], $pos+1);
                array_push($productImages, $productImage);
            } else {
                array_push($productImages, $mediaUrl.'product360view'.$data['file']);
            }
        }
        return $this->json->serialize($productImages);
    }
}
