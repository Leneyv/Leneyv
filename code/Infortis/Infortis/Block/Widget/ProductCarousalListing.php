<?php
namespace Infortis\Infortis\Block\Widget;
use Magento\Framework\View\Element\Template;
use Magento\Widget\Block\BlockInterface; 

class ProductCarousalListing extends Template implements BlockInterface
{
    //templates in infortis/view/frontend/template/widget 
    protected $_template = "widget/ProductCarousalListing.phtml";
    protected $_objectManager = null;
    protected $_categoryFactory;
    protected $_category;
    protected $_productCollectionFactory; 
    protected $_price_helper;
    protected $_scopeConfig;
    protected $_image_helper;
    protected $_categoryRepository;
        public function __construct( 
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory, 
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Catalog\Model\CategoryRepository $categoryRepository
         )
        {   
            $this->_productCollectionFactory = $productCollectionFactory;
            $this->_objectManager = $objectManager;
            $this->_categoryFactory = $categoryFactory; 
            $this->_scopeConfig = $scopeConfig;
            $priceHelper = $objectManager->getInstance()->create('Magento\Framework\Pricing\Helper\Data');  
            $this->_price_helper = $priceHelper;
            $imageHelper = $objectManager->getInstance()->create('Magento\Catalog\Helper\Image');
            $this->_image_helper = $imageHelper; 
        
             $this->_categoryRepository = $categoryRepository;
             parent::__construct($context);
        }
        public function get_prodimage($product)
        {
            $imageUrl = $this->_image_helper->init($product, 'product_page_image_small')
                ->setImageFile($product->getSmallImage())
                ->resize(380)
                ->getUrl();
            return $imageUrl;
        }
        public function get_categData($category_id)
        {
            $categ_data = [];
            $catg_data_set = $this->_categoryRepository->get($category_id);
            foreach($catg_data_set as $categ)
            {
                $categ_data['name'] = $catg_data_set->getName();
                $categ_data['url'] = $catg_data_set->getUrl(); 
            }   
            return $categ_data;
        } 
        public function getPlaceholderImage()
        {
            return $this->_scopeConfig->getValue('catalog/placeholder/image_placeholder');  
        }
        public function getCurrentCategory()
        {
            $category = $this->_objectManager->get('Magento\Framework\Registry')->registry('current_category');
            return $category;
        } 
        public function getCategory($categoryId)
        {
            $this->_category = $this->_categoryFactory->create();
            $this->_category->load($categoryId);
            return $this->_category;
        } 
        public function category_data($categoryId)
        {
             $categor = $this->_categoryFactory->create();
             $categor->load($categoryId);
            return $category->getData(); 
        }
        public function getAllChildren($asArray = false, $categoryId = false)
        {
            if ($this->_category) { 
                return $this->_category->getAllChildren($asArray);
            } else {
                return $this->getCategory($categoryId)->getAllChildren($asArray);
            }
        }
        public function getProductCollection($category_id_array, $prod_num)
        {
            $collection = $this->_productCollectionFactory->create();
            $collection->addAttributeToSelect('*');
            $collection->addCategoriesFilter(['in' => $category_id_array]);
            $collection->addAttributeToFilter('visibility', 4);
            $collection->addAttributeToFilter('status', 1); 
            $collection->addAttributeToFilter('is_featured', 1); 
            $collection->setPageSize($prod_num); 
            return $collection;
        }
        public function getis_MenuProductCollection($category_id_array, $prod_num)
        { 
            $collection = $this->_productCollectionFactory->create();
            $collection->addAttributeToSelect('*');
            $collection->addCategoriesFilter(['in' => $category_id_array]);
            $collection->addAttributeToFilter('visibility', 4);
            $collection->addAttributeToFilter('status', 1); 
            $collection->addAttributeToFilter('is_menu_product', 1); 
            $collection->setPageSize($prod_num); 
            return $collection;
        }
        public function get_prod_price($price)
        { 
            return $this->_price_helper->currency($price, true, false); 
        }
    
}