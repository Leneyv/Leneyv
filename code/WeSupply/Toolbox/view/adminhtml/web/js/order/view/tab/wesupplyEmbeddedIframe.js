define([
    'jquery'
], function ($) {
    'use strict';

    var createIframe = function (iframeUrl, iframeId) {
        return $('<iframe />', {
            id: iframeId,
            class: 'embedded-iframe',
            src: iframeUrl,
            width: '100%',
            height: '100%',
            allowfullscreen: true,
            frameborder: 0,
            allow: 'geolocation',
            scrolling: 'yes'
        });
    };

    var getHeight = function() {
        return $(window).height() - $('.page-main-actions').height();
    };

    return {
        loadIframe: function(config)
        {
            if (!config.viewContainer.hasClass('loaded')) {
                setTimeout(function() {
                    config.viewContainer
                        .html(createIframe(config.iframeUrl, config.iframeId))
                        .height(getHeight());

                    $('#' + config.iframeId).on('load', function () {
                        config.loadingContainer.hide();
                        config.viewContainer.addClass('loaded').show();
                    });
                }, 1000);
            }
        }
    }
});
