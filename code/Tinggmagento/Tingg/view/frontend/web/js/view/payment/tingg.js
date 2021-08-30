define(
    [
        'uiComponent',
        'Magento_Checkout/js/model/payment/renderer-list'
    ],
    function (
        Component,
        rendererList
    ) {
        'use strict';
        rendererList.push(
            {
                type: 'Tingg_payment',
                component: 'Tinggmagento_Tingg/js/view/payment/method-renderer/tingg-checkout'
            }
        );
        return Component.extend({});
    }
 );
