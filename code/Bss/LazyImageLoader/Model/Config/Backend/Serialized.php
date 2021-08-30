<?php
/**
 * BSS Commerce Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://bsscommerce.com/Bss-Commerce-License.txt
 *
 * @category   BSS
 * @package    Bss_LazyImageLoader
 * @author     Extension Team
 * @copyright  Copyright (c) 2018-2019 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */
namespace Bss\LazyImageLoader\Model\Config\Backend;

class Serialized extends \Magento\Framework\App\Config\Value
{
    /**
     * @return \Magento\Framework\App\Config\Value|void
     */
    protected function _afterLoad()
    {
        if (!is_array($this->getValue())) {
            $value = $this->getValue();
            $this->setValue(empty($value) ? false : unserialize($value));
        }
    }

    /**
     * @return \Magento\Framework\App\Config\Value
     */
    public function beforeSave()
    {
        $value = $this->getValue();
        if (is_array($value)) {
            unset($value['__empty']);
        }
        $this->setValue($value);
        
        if (is_array($this->getValue())) {
            $this->setValue(serialize($this->getValue()));
        }
        return parent::beforeSave();
    }
}
