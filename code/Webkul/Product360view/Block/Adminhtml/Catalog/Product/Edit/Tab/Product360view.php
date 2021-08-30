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
namespace Webkul\Product360view\Block\Adminhtml\Catalog\Product\Edit\Tab;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget;
use Magento\Framework\Registry;
use Webkul\Product360view\Model\Product360viewFactory;
use Magento\Catalog\Model\ProductFactory;
use Magento\Framework\Data\Form\FormKey;
use \Magento\Framework\File\Size;
use Webkul\Product360view\Helper\Data as HelperData;

/**
 * Block for admin product edit form
 */
class Product360view extends Widget
{
    /**
     * @var string
     */
    protected $_template = 'product/edit/product360view.phtml';

    /**
     * @var Registry
     */
    protected $coreRegistry = null;

    /**
     * @var \Magento\Framework\File\Size
     */
    protected $fileSizeService;

    /**
     * @var ProductFactory
     */
    protected $productFactory;

    /**
     * @var Product360viewFactory
     */
    protected $product360viewFactory;

    /**
     * @var \Magento\Framework\Data\Form\FormKey
     */
    protected $formKey;

    /**
     * @var HelperData
     */
    protected $helper;
    
    /**
     * @param Context $context
     * @param Registry $registry
     * @param Size $fileSize
     * @param ProductFactory $productFactory
     * @param Product360viewFactory $product360viewFactory
     * @param FormKey $formKey
     * @param HelperData $helper
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        Size $fileSize,
        ProductFactory $productFactory,
        Product360viewFactory $product360viewFactory,
        FormKey $formKey,
        HelperData $helper,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->coreRegistry = $registry;
        $this->fileSizeService = $fileSize;
        $this->productFactory = $productFactory;
        $this->product360viewFactory = $product360viewFactory;
        $this->formKey = $formKey;
        $this->helper = $helper;
    }

    /**
     * Retrieve product id
     *
     * @return \Magento\Catalog\Model\Product
     */
    public function getProductId()
    {
        return $this->getRequest()->getParam("id");
    }

    /**
     * @return \Magento\Framework\File\Size
     */
    public function getFileSizeService()
    {
        return $this->fileSizeService;
    }

    /**
     * get form key
     *
     * @return string
     */
    public function getFormKey()
    {
         return $this->formKey->getFormKey();
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
