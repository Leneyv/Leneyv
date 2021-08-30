<?php
namespace Magecomp\Orderonwhatsapp\Model;

class Size
{
	public function toOptionArray() 
	{
        return [
            ['value' => 'small', 'label' => __('Small')],
            ['value' => 'medium', 'label' => __('Medium')],
			['value' => 'large', 'label' => __('Large')]
        ];
    }
}