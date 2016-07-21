/**
 * Date picker
 *
 * @license   GNU General Public License, version 2
 * @copyright 2013 OOO "ЛС-СОФТ" {@link http://livestreetcms.com}
 * @author    Denis Shakhov <denis.shakhov@gmail.com>
 */

(function($) {
    "use strict";

    $.widget( "livestreet.lsDate", $.livestreet.lsComponent, {
        /**
         * Дефолтные опции
         */
        options: {
            format: 'DD.MM.YYYY',
            yearRange: 100,
            firstDay: 1,
            language: null,
            i18n: {
                previousMonth : 'Previous Month',
                nextMonth     : 'Next Month',
                months        : ['January','February','March','April','May','June','July','August','September','October','November','December'],
                weekdays      : ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'],
                weekdaysShort : ['Sun','Mon','Tue','Wed','Thu','Fri','Sat']
            }
        },

        /**
         * Конструктор
         *
         * @constructor
         * @private
         */
        _create: function() {
            this._super();

            this.option( 'i18n', window.PikadayConfig.i18n[ this.option( 'language' ) ] || this.option( 'i18n' ) );

            this.element.pikaday( this.options );
        }
    });
})(jQuery);