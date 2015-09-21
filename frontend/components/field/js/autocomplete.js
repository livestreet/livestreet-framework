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

    $.widget( "livestreet.lsAutocomplete", {
        /**
         * Дефолтные опции
         */
        options: {
            multiple: false,
            // Ссылки
            urls: {
                load: null
            },
            responseName: 'aItems',
            params: {}
        },

        /**
         * Конструктор
         *
         * @constructor
         * @private
         */
        _create: function () {
            this.element.autocomplete({
                serviceUrl: this.option( 'urls.load' ),
                type: 'POST',
                dataType: 'json',
                paramName: 'value',
                delimiter: this.option( 'multiple' ) ? ',' : null,
                transformResult: function(response) {
                    return {
                        suggestions: response[ this.option( 'responseName' ) ]
                    };
                }.bind(this),
                params: $.extend( {}, { security_ls_key: LIVESTREET_SECURITY_KEY }, this.option( 'params' ) )
            });
        }
    });
})(jQuery);