/**
 * Cookie bar logic
 */

define([
    'uiComponent',
    'jquery',
    'underscore',
    'mage/url',
    'Amasty_GdprCookie/js/model/cookie'
], function (Component, $, _, urlBuilder, cookieModel) {
    'use strict';

    return Component.extend({
        defaults: {
            noticeType: 1,
            isNotice: 0,
            template: 'Amasty_GdprCookie/cookiebar',
            settingsLink: '/',
            allowLink: '/',
            websiteInteraction: '0',
            firstShowProcess: '0',
            cookiesName: [],
            domainName: '',
            containerSelector: '[data-amcookie-js="container"]',
            barSelector: '[data-amcookie-js="bar"]',
            isScrollBottom: false,
            barLocation: null,
            groups: []
        },

        initialize: function () {
            this._super();

            cookieModel().getEssentialCookies(this.groups);
            cookieModel().analyticsCookie();

            return this;
        },

        initObservable: function () {
            this._super()
                .observe({
                    showDisallowButton: this.noticeType === 0,
                    isScrollBottom: false
                });

            return this;
        },

        setupCookies: function () {
            location.href = urlBuilder.build(this.settingsLink);
        },

        allowCookies: function () {
            cookieModel().allowAllCookies().done(function () {
                $(this.barSelector).remove();
                cookieModel().triggerAllow();
                if (this.websiteInteraction == 1) {
                    cookieModel().restoreInteraction();
                }
            }.bind(this));
        },

        detectScroll: function () {
            if (this.barLocation == 1) {
                return;
            }

            this.elementBar = $(this.barSelector);
            $(window).on('scroll', _.throttle(this.scrollBottom, 200).bind(this));
        },

        scrollBottom: function () {
            var scrollHeight = window.innerHeight + window.pageYOffset,
                pageHeight = document.documentElement.scrollHeight;

            if (scrollHeight >= pageHeight - this.elementBar.innerHeight()) {
                this.isScrollBottom(true);
                return;
            }

            this.isScrollBottom(false);
        },

        isShowNotificationBar: function () {
            return cookieModel()
                .isShowNotificationBar(
                    this.isNotice, this.websiteInteraction, this.settingsLink, this.firstShowProcess
                );
        }
    });
});
