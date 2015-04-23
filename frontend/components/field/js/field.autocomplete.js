/**
 * Autocomplete
 *
 * @module ls/autocomplete
 *
 * @license   GNU General Public License, version 2
 * @copyright 2013 OOO "ЛС-СОФТ" {@link http://livestreetcms.com}
 * @author    Denis Shakhov <denis.shakhov@gmail.com>
 */

(function($) {
    "use strict";

    $.widget( "livestreet.lsFieldAutocomplete", {
        /**
         * Дефолтные опции
         */
        options: {
            max_selected_options: 3,
            width: '100%',
            // Ссылки
            urls: {
                load: null
            },
            params: {}
        },

        /**
         * Конструктор
         *
         * @constructor
         * @private
         */
        _create: function () {
            if ( this.option( 'urls.load' ) ) {
                this._initAjax();
            } else {
                this.element.chosen( this.options );
            }
        },

        /**
         * 
         */
        _initAjax: function () {
            this.element.ajaxChosen({
                type: 'POST',
                url: this.option( 'urls.load' ),
                jsonTermKey: 'value',
                data: $.extend( {}, { security_ls_key: LIVESTREET_SECURITY_KEY }, this.option( 'params' ) ),
                dataType: 'json'
            }, function (data) {
                var results = [];

                $.each(data.aItems, function (i, data) {
                    results.push($.isArray(data) ? { value: data, text: data } : { value: data.value, text: data.text });
                });

                return results;
            }, this.options );
        }
    });
})(jQuery);