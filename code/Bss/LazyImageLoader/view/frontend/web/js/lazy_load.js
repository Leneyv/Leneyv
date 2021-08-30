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
define([
    'jquery',
    'bss/unveil'
], function ($) {
    'use strict';
    $.widget('bss.bss_config', {
        _create: function () {
            var options = this.options;
            var threshold = parseInt(options.threshold);

            $(document).ready(function() {
                $("img.lazy").unveil(threshold);
            });

        }
    });
    return $.bss.bss_config;
});
