/**
 * Cookie settings logic
 */
define([
    'jquery',
    'Amasty_GdprCookie/js/model/cookie'
], function ($, cookieModel) {
    'use strict';

    $.widget('mage.settingsPage', {
        options: {
            buttonSave: '[data-amcookie-js="settings-save"]',
            buttonAllow: '[data-amcookie-js="settings-allow"]'
        },

        _create: function () {
            this.addEvents();
        },

        addEvents: function () {
            $(this.options.buttonSave).on('click', this.saveCookie);
            $(this.options.buttonAllow).on('click', this.allowAllCookies);
        },

        saveCookie: function (event) {
            event.preventDefault();
            cookieModel().saveCookie().done(function () {
                cookieModel().analyticsCookie();
                cookieModel().triggerSave();
            });
        },

        allowAllCookies: function (event) {
            event.preventDefault();
            cookieModel().allowAllCookies().done(function () {
                cookieModel().triggerAllow();
                location.reload();
            });
        }
    });

    return $.mage.settingsPage;
});
