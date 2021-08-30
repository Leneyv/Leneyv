/**
 * Cookie modal logic
 */
define([
    'uiComponent',
    'jquery',
    'underscore',
    'Magento_Ui/js/modal/modal',
    'Amasty_GdprCookie/js/model/cookie',
], function (Component, $, _, modal, cookieModel) {
    'use strict';

    return Component.extend({
        defaults: {
            template: {
                name: 'Amasty_GdprCookie/modal',
                afterRender: function (node, data) {
                    data.setModalHeight();
                }
            },
            timeout: null,
            isNotice: null,
            groups: [],
            cookieModal: {},
            websiteInteraction: '',
            settingsLink: '/',
            firstShowProcess: '',
            isShowModal: false,
            element: {
                modal: '[data-amgdpr-js="modal"]',
                form: '[data-amcookie-js="form-cookie"]',
                container: '[data-role="gdpr-cookie-container"]',
                field: '[data-amcookie-js="field"]',
                groups: '[data-amcookie-js="groups"]',
                policy: '[data-amcookie-js="policy"]'
            }
        },

        initialize: function () {
            this._super();

            var settingsUrl = window.location.origin + window.location.pathname + '/';

            if (cookieModel().isShowNotificationBar(
                this.isNotice, this.websiteInteraction, this.settingsLink, this.firstShowProcess
            ) && settingsUrl !== this.settingsLink) {
                this.initModal();
            }

            cookieModel().getEssentialCookies(this.groups());
            cookieModel().analyticsCookie();

            return this;
        },

        initObservable: function () {
            this._super()
                .observe(['groups', 'isShowModal']);

            return this;
        },

        closeModal: function () {
            this.cookieModal.closeModal();

            if (this.websiteInteraction != 1) {
                return;
            }

            cookieModel().restoreInteraction();
        },

        allowCookies: function () {
            cookieModel().allowAllCookies().done(function () {
                this.closeModal();
                cookieModel().triggerAllow();
            }.bind(this));
        },

        saveCookie: function () {
            var disabledFields = $(this.element.field + ':disabled');

            disabledFields.removeAttr('disabled');
            cookieModel().saveCookie().done(function () {
                disabledFields.attr('disabled', true);
                this.closeModal();
                cookieModel().triggerSave();
            }.bind(this));
        },

        initModal: function () {
            var options = {
                type: 'popup',
                responsive: true,
                modalClass: 'amgdprcookie-modal-container',
                buttons: []
            };

            this.isShowModal(true);

            if (this.websiteInteraction == 1) {
                options.clickableOverlay = false;
                options.keyEventHandlers = {
                    escapeKey: function () { }
                };

                options.opened = function () {
                    $('.modal-header button.action-close').hide();
                };
            }

            this.cookieModal = modal(options, this.element.modal);

            this.cookieModal.element.html($(this.element.container));
            this.addResizeEvent();
            this.cookieModal.openModal().on('modalclosed', function () {
                this.cookieModal.element.html('');
                $(window).off('resize', this.resizeFunc);
            }.bind(this));
        },

        addResizeEvent: function () {
            this.resizeFunc = _.throttle(this.setModalHeight, 150).bind(this);
            $(window).on('resize', this.resizeFunc);
        },

        setModalHeight: function () {
            var policyHeight = $(this.element.policy).innerHeight(),
                windowHeight = window.innerHeight,
                groupsContainer = $(this.element.groups);

            if (policyHeight / windowHeight > 0.6) {
                policyHeight /= 2;
            }

            groupsContainer.height(windowHeight - policyHeight + 'px');
        }
    });
});
