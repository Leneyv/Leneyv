<?php

namespace Meetanshi\WhatsappContact\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class Animation implements ArrayInterface
{
    public function toOptionArray()
    {
        return [
            ['value' => 'none', 'label' => __('None')],
            ['value' => 'pulse-grow', 'label' => __('Pulse Grow')],
            ['value' => 'pulse-shrink', 'label' => __('Pulse Shrink')],
            ['value' => 'slideUpReturn', 'label' => __('Slide Up Return')],
            ['value' => 'slideDownReturn', 'label' => __('Slide Down Return')],
            ['value' => 'slideLeftReturn', 'label' => __('Slide Left Return')],
            ['value' => 'slideRightReturn', 'label' => __('Slide Right Return')],
            ['value' => 'spaceInUp', 'label' => __('Space In Up')],
            ['value' => 'spaceInDown', 'label' => __('Space In Down')],
            ['value' => 'spaceInLeft', 'label' => __('Space In Left')],
            ['value' => 'spaceInRight', 'label' => __('Space In Right')],
            ['value' => 'tinUpIn', 'label' => __('Tin Up In')],
            ['value' => 'tinDownIn', 'label' => __('Tin Down In')],
            ['value' => 'tinLeftIn', 'label' => __('Tin Left In')],
            ['value' => 'tinRightIn', 'label' => __('Tin Right In')],
            ['value' => 'wobble-horizontal', 'label' => __('Wobble Horizontal')],
            ['value' => 'wobble-vertical', 'label' => __('Wobble Vertical')]
        ];
    }
}
