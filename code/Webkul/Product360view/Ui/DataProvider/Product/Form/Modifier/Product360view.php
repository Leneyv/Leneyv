<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_Product360view
 * @author    Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\Product360view\Ui\DataProvider\Product\Form\Modifier;

use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier;
use Magento\Downloadable\Api\Data\ProductAttributeInterface;
use Magento\Framework\View\LayoutFactory;

class Product360view extends AbstractModifier
{
    /**
     * Field value
     */
    const FIELD_PRODUCT360VIEW_AVAILABLE = 'product_360_view_show';

    /**
     * @var LayoutFactory
     */
    private $layoutFactory;

    /**
     * @param LayoutFactory $layoutFactory
     */
    public function __construct(
        LayoutFactory $layoutFactory
    ) {
        $this->layoutFactory = $layoutFactory;
    }

    /**
     * @param array $meta
     * @return void
     */
    public function modifyMeta(array $meta)
    {
        $meta["product-360-view"] = [
            "children" => [
                "product360view_container" => [
                    "arguments" => [
                        "data" => [
                            "config" => [
                                "formElement" => "container",
                                "componentType" => "container",
                                'component' => 'Magento_Ui/js/form/components/html',
                                "required" => 0,
                                "sortOrder" => 2,
                                "content" => $this->layoutFactory->create()->createBlock(
                                    'Webkul\Product360view\Block\Adminhtml\Catalog\Product\Edit\Tab\Product360view'
                                )->toHtml(),
                            ]
                        ]
                    ]
                ],
                "product_360_view_show" => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'label' => __('Enable'),
                                'formElement' => 'checkbox',
                                'componentType' => 'input',
                                'dataScope' => 'data.product.'.static::FIELD_PRODUCT360VIEW_AVAILABLE,
                                'dataType' => 'number',
                                'sortOrder' => 1,
                                'component' => 'Magento_Ui/js/form/element/single-checkbox',
                                'prefer' => 'toggle',
                                'valueMap' => ['true'=>'1', 'false'=>'0']
                            ]
                        ]
                    ]
                ]
            ],
            "arguments" => [
                "data" => [
                    "config" => [
                        "componentType" => "fieldset",
                        "label" => __("Product 360 View"),
                        "collapsible" => true,
                        "sortOrder" => 10,
                        'opened' => true,
                        'canShow' => true,
                        'visible' => false,
                        'imports' => [
                            'visible' => '${$.provider}:' . self::DATA_SCOPE_PRODUCT . '.'
                                . ProductAttributeInterface::CODE_HAS_WEIGHT
                        ],
                    ]
                ]
            ]
        ];
        return $meta;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyData(array $data)
    {
        return $data;
    }
}
