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
namespace Webkul\Product360view\Model;

use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use Webkul\Product360view\Api\Data\Product360viewInterfaceFactory;
use Magento\Framework\Api\DataObjectHelper;
use Webkul\Product360view\Model\ResourceModel\Product360view as ResourceProduct360view;
use Webkul\Product360view\Model\ResourceModel\Product360view\Collection;

class Product360view extends AbstractModel
{
    /**
     * @var \Webkul\Product360view\Api\Data\Product360viewInterfaceFactory
     */
    protected $product360viewInterfaceFactory;

    /**
     * @var \Magento\Framework\Api\DataObjectHelper
     */
    protected $dataObjectHelper;
    
    /**
     * @var string $_eventPrefix
     */
    protected $_eventPrefix = 'product360_view';

    /**
     * @param Context $context
     * @param Registry $registry
     * @param Product360viewInterfaceFactory $product360viewInterfaceFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param ResourceProduct360view $resource
     * @param Collection $resourceCollection
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        Product360viewInterfaceFactory $product360viewInterfaceFactory,
        DataObjectHelper $dataObjectHelper,
        ResourceProduct360view $resource,
        Collection $resourceCollection,
        array $data = []
    ) {
        $this->product360viewInterfaceFactory = $product360viewInterfaceFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Retrieve application model with application data
     * @return ApplicationInterface
     */
    public function getDataModel()
    {
        $product360viewData = $this->getData();
        
        $product360viewDataObject = $this->product360viewInterfaceFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $product360viewDataObject,
            $product360viewData,
            ApplicationInterface::class
        );
        
        return $product360viewDataObject;
    }
}
