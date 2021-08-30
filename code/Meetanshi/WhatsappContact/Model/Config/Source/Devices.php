<?php

namespace Meetanshi\WhatsappContact\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class Devices implements ArrayInterface
{
    public function toOptionArray()
    {
        return [
            ['value' => '0', 'label' => __('Desktop')],
            ['value' => '1', 'label' => __('Mobile')],
            ['value' => '2', 'label' => __('Both')]
        ];
    }
}
