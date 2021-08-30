define(
    [
        'jquery',
        'Magento_Checkout/js/view/payment/default',
        'Magento_Checkout/js/action/set-billing-address',
        'Tinggmagento_Tingg/js/action/set-payment-method',
        'Magento_Checkout/js/action/select-payment-method',
        'Magento_Checkout/js/model/payment/additional-validators'
    ],
    function ($, Component, setBillingAddressAction, setPaymentMethodAction, selectPaymentMethodAction, additionalValidators) {
        'use strict';
        return Component.extend({
            defaults: {
                template: 'Tinggmagento_Tingg/payment/tingg'
            },

            continueToTingg: function () {
                if (this.validate() && additionalValidators.validate()) {
                    this.selectPaymentMethod();
                    var setBillingInfo = setBillingAddressAction();
                    setBillingInfo.done(function() {
                        setPaymentMethodAction();
                    });
                    return false;
                }
            }
        });
    }
);
