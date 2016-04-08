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

    var notReadyItems = [];

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
                notReadyItems.push(this);
            }
        },

        init: function () {
            var el = this.element.get(0);

            this.grecaptcha = grecaptcha.render(el, {
                sitekey: this.options.key,
                callback: this.callbackResponse.bind(this)
            });

            // Поле для результата валидации
            this.input = $('<input>').attr({
                name: this.options.name,
                type: 'hidden'
            });

            this.input.appendTo(this.element);

            // Кнопка обновления капчи
            var reset = $('#' + $(el).attr('id') + '-reset');

            if (reset.length) {
                reset.click(this.reset.bind(this));
            }
        },

        callbackResponse: function (response) {
            this.input.val(response);
        },

        initNotReady: function () {
            if (notReadyItems) {
                $.each(notReadyItems, function (k, v) {
                    v.init()
                });
                notReadyItems = [];
            }
        },

        reset: function () {
            grecaptcha.reset(this.grecaptcha);
        }
    });
})(jQuery);

function ___ls_grecaptcha_onload() {
    jQuery.livestreet.lsReCaptcha.prototype.initNotReady();
    $(window).trigger('___ls_grecaptcha_onload');
}
window['___grecaptcha_cfg'] = window['___grecaptcha_cfg'] || [];
window['___grecaptcha_cfg']['onload'] = '___ls_grecaptcha_onload';