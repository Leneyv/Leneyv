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

use Webkul\Product360view\Model\ResourceModel\Product360view as ResourceProduct360view;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Webkul\Product360view\Api\Data\Product360ViewSearchResultsInterface;
use Webkul\Product360view\Model\ResourceModel\Product360view\CollectionFactory as Product360ViewCollectionFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Webkul\Product360view\Api\Product360viewRepositoryInterface;
use Webkul\Product360view\Api\Data\Product360viewInterfaceFactory;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Api\ExtensibleDataObjectConverter;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Store\Model\StoreManagerInterface;

class Product360viewRepository implements Product360viewRepositoryInterface
{
    /**
     * @var \Webkul\Product360view\Model\ResourceModel\Product360view
     */
    protected $resource;

    /**
     * @var \Webkul\Product360view\Model\Product360viewFactory
     */
    protected $product360viewFactory;
    
    /**
     * @var \Webkul\Product360view\Api\Data\Product360viewInterfaceFactory
     */
    protected $dataProduct360viewFactory;

    /**
     * @var \Webkul\Product360view\Model\ResourceModel\Product360view\CollectionFactory
     */
    protected $product360ViewCollectionFactory;

    /**
     * @var \Webkul\Product360view\Api\Data\Product360ViewSearchResultsInterface
     */
    protected $searchResultsFactory;

    /**
     * @var \Magento\Framework\Api\DataObjectHelper
     */
    protected $dataObjectHelper;

    /**
     * @var \Magento\Framework\Reflection\DataObjectProcessor
     */
    protected $dataObjectProcessor;
    
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface
     */
    protected $collectionProcessor;

    /**
     * @var \Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface
     */
    protected $extensionAttributesJoinProcessor;
    
    /**
     * @var \Magento\Framework\Api\ExtensibleDataObjectConverter
     */
    protected $extensibleDataObjectConverter;
    
    /**
     * @param ResourceProduct360view $resource
     * @param Product360viewFactory $product360viewFactory
     * @param Product360viewInterfaceFactory $dataProduct360viewFactory
     * @param Product360ViewCollectionFactory $product360ViewCollectionFactory
     * @param Product360ViewSearchResultsInterface $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     * @param CollectionProcessorInterface $collectionProcessor
     * @param JoinProcessorInterface $extensionAttributesJoinProcessor
     * @param ExtensibleDataObjectConverter $extensibleDataObjectConverter
     */
    public function __construct(
        ResourceProduct360view $resource,
        Product360viewFactory $product360viewFactory,
        Product360viewInterfaceFactory $dataProduct360viewFactory,
        Product360ViewCollectionFactory $product360ViewCollectionFactory,
        Product360ViewSearchResultsInterface $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager,
        CollectionProcessorInterface $collectionProcessor,
        JoinProcessorInterface $extensionAttributesJoinProcessor,
        ExtensibleDataObjectConverter $extensibleDataObjectConverter
    ) {
        $this->resource = $resource;
        $this->product360viewFactory = $product360viewFactory;
        $this->product360ViewCollectionFactory = $product360ViewCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataProduct360viewFactory = $dataProduct360viewFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->storeManager = $storeManager;
        $this->collectionProcessor = $collectionProcessor;
        $this->extensionAttributesJoinProcessor = $extensionAttributesJoinProcessor;
        $this->extensibleDataObjectConverter = $extensibleDataObjectConverter;
    }

    /**
     * {@inheritdoc}
     */
    public function save(
        \Webkul\Product360view\Api\Data\Product360viewInterface $product360view
    ) {
        $product360viewData = $this->extensibleDataObjectConverter->toNestedArray(
            $product360view,
            [],
            \Webkul\Product360view\Api\Data\Product360viewInterface::class
        );
        
        $product360viewModel = $this->product360viewFactory->create()->setData($product360viewData);
        
        try {
            $this->resource->save($product360viewModel);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the product 360 view data: %1',
                $exception->getMessage()
            ));
        }
        return $product360viewModel->getDataModel();
    }

    /**
     * {@inheritdoc}
     */
    public function getById($viewId)
    {
        $product360view = $this->product360viewFactory->create();
        $this->resource->load($product360view, $viewId);
        if (!$product360view->getId()) {
            throw new NoSuchEntityException(__('Product 360 view data with id "%1" does not exist.', $viewId));
        }
        return $product360view->getDataModel();
    }

    /**
     * {@inheritdoc}
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->product360ViewCollectionFactory->create();
        
        $this->extensionAttributesJoinProcessor->process(
            $collection,
            \Webkul\Product360view\Api\Data\Product360viewInterface::class
        );
        
        $this->collectionProcessor->process($criteria, $collection);
        
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);
        
        $items = [];
        foreach ($collection as $model) {
            $items[] = $model->getDataModel();
        }
        
        $searchResults->setItems($items);
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(
        \Webkul\Product360view\Api\Data\Product360viewInterface $product360view
    ) {
        try {
            $product360viewModel = $this->product360viewFactory->create();
            $this->resource->load($product360viewModel, $product360view->geId());
            $this->resource->delete($product360viewModel);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the Product 360 view: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($id)
    {
        return $this->delete($this->getById($id));
    }
}
