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
namespace Webkul\Product360view\Api\Data;

interface Product360viewInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    /**
     * @var String
     */
    const ID = 'id';

    /**
     * @var String
     */
    const ENTITYID = 'entity_id';

    /**
     * @var String
     */
    const STOREID = 'store_id';

    /**
     * @var String
     */
    const FILE = 'file';

    /**
     * @var String
     */
    const POSITION = 'position';

    /**
     * Get ID
     *
     * @return int|null
     */
    public function getId();

    /**
     * Set ID
     *
     * @param int $id
     * @return \Webkul\Product360view\Api\Data\Product360viewInterface
     */
    public function setId($id);

    /**
     * Get entity ID
     *
     * @return int|null
     */
    public function getEntityId();
    
    /**
     * Set Entity ID
     *
     * @param int $id
     * @return \Webkul\Product360view\Api\Data\Product360viewInterface
     */
    public function setEntityId($entityId);

    /**
     * Get Store ID
     *
     * @return int|null
     */
    public function getStoreId();
    
    /**
     * Set Store ID
     *
     * @param int $storeId
     * @return \Webkul\Product360view\Api\Data\Product360viewInterface
     */
    public function setStoreId($storeId);

    /**
     * Get File
     *
     * @return string|null
     */
    public function getFile();
    
    /**
     * Set File
     *
     * @param string $file
     * @return \Webkul\Product360view\Api\Data\Product360viewInterface
     */
    public function setFile($file);

    /**
     * Get Position
     *
     * @return int|null
     */
    public function getPosition();
    
    /**
     * Set Position
     *
     * @param int $position
     * @return \Webkul\Product360view\Api\Data\Product360viewInterface
     */
    public function setPosition($position);

    /**
     * Retrieve existing extension attributes object or create a new one.
     * @return \Webkul\Product360view\Api\Data\Product360viewExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     * @param \Webkul\Product360view\Api\Data\Product360viewExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Webkul\Product360view\Api\Data\Product360viewExtensionInterface $extensionAttributes
    );
}
