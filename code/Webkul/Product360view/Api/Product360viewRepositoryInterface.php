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
namespace Webkul\Product360view\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface Product360viewRepositoryInterface
{

    /**
     * Save Product 360 View
     * @param \Webkul\Product360view\Api\Data\Product360viewInterface $product
     * @return \Webkul\Product360view\Api\Data\Product360viewInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \Webkul\Product360view\Api\Data\Product360viewInterface $product
    );

    /**
     * Retrieve Product 360 View
     * @param string $id
     * @return \Webkul\Product360view\Api\Data\Product360viewInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($id);

    /**
     * Retrieve Product 360 View matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Webkul\Product360view\Api\Data\Product360ViewSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete Product 360 View
     * @param \Webkul\Product360view\Api\Data\Product360viewInterface $product
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \Webkul\Product360view\Api\Data\Product360viewInterface $product
    );

    /**
     * Delete Product 360 View by ID
     * @param string $id
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($id);
}
