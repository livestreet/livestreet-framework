/**
 * Date picker
 *
 * @module ls/field/date
 *
 * @license   GNU General Public License, version 2
 * @copyright 2013 OOO "ЛС-СОФТ" {@link http://livestreetcms.com}
 * @author    Denis Shakhov <denis.shakhov@gmail.com>
 */

(function($) {
    "use strict";

    $.widget( "livestreet.lsFieldDate", $.livestreet.lsComponent, {
        /**
         * Дефолтные опции
         */
        options: {
            format: 'DD.MM.YYYY',
            yearRange: 100,
            firstDay: 1,
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

            this.element.pikaday( this.options );
        }
    });
})(jQuery);