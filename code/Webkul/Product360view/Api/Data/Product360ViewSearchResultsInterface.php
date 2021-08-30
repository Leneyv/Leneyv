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

interface Product360ViewSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{

    /**
     * Get Product360view list.
     * @return \Webkul\Product360view\Api\Data\Product360viewInterface[]
     */
    public function getItems();

    /**
     * Set Product360view list.
     * @param \Webkul\Product360view\Api\Data\Product360viewInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
