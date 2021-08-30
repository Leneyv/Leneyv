
<?php


use Magento\Framework\ObjectManagerInterface;
use \Magento\Framework\App\Action\Action;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    protected $_customerFactory;

    protected $objectManager;

    /**
     * Initialize dependencies.
     *
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        ObjectManagerInterface $objectManager
    ) {
        $this->_customerFactory = $customerFactory;
        $this->objectManager = $objectManager;
        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomerAttributeValue($customerId, $attributeCode)
    {
        $customerObject = $this->_customerFactory->create()->load($customerId);
        return $attribute = ($customerObject->getData($attributeCode)) ? $customerObject->getData($attributeCode): false;
    }
}