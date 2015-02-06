/**
 * ReCaptcha
 *
 * @module ls/recaptcha
 *
 * @license   GNU General Public License, version 2
 * @copyright 2013 OOO "ЛС-СОФТ" {@link http://livestreetcms.com}
 * @author    Denis Shakhov <denis.shakhov@gmail.com>
 */

(function ($) {
    "use strict";

    $.widget("livestreet.lsReCaptcha", $.livestreet.lsComponent, {
        /**
         * Дефолтные опции
         */
        options: {
            captchaName: null,
            name: null,
            key: null
        },

        /**
         * Конструктор
         *
         * @constructor
         * @private
         */
        _create: function () {
            this._super();

            if (window['grecaptcha']) {
                this.init();
            } else {
                if (!$.livestreet.lsReCaptcha.prototype.notReadyItems) {
                    $.livestreet.lsReCaptcha.prototype.notReadyItems = [];
                }
                $.livestreet.lsReCaptcha.prototype.notReadyItems.push(this);
            }
        },

        init: function () {
            this.grecaptcha=grecaptcha.render(this.element.get(0), {
                'sitekey': this.options.key,
                'callback': function(response) {
                    this.callbackResponse.call(this,response);
                }.bind(this)
            });
        },

        callbackResponse: function(response) {
            if (!this.input) {
                this.input=$('<input>').attr({
                    name: this.options.name,
                    type: 'hidden'
                });
                this.input.insertAfter(this.element);
            }
            this.input.val(response);
        },

        initNotReady: function () {
            if ($.livestreet.lsReCaptcha.prototype.notReadyItems) {
                $.each($.livestreet.lsReCaptcha.prototype.notReadyItems, function (k, v) {
                    v.init()
                });
                $.livestreet.lsReCaptcha.prototype.notReadyItems = [];
            }
        }
    });
})(jQuery);

function ___ls_grecaptcha_onload() {
    jQuery.livestreet.lsReCaptcha.prototype.initNotReady();
    $(window).trigger('___ls_grecaptcha_onload');
}
window['___grecaptcha_cfg'] = window['___grecaptcha_cfg'] || [];
window['___grecaptcha_cfg']['onload'] = '___ls_grecaptcha_onload';