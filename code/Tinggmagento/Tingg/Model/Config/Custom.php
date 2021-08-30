<?php
namespace Tinggmagento\Tingg\Model\Config;
 
class Custom implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => "ke", 'label' => __('Kenya')],
            ['value' => "tz", 'label' => __('Tanzania')],
            // ['value' => "tg", 'label' => __('Togo')],
            ['value' => "ug", 'label' => __('Uganda')]
           
        ];
    }
}

?>