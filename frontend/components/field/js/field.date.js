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
            // Опции теже что и у плагина datepicker
        },

        /**
         * Конструктор
         *
         * @constructor
         * @private
         */
        _create: function() {
            this._super();

            this.element.datepicker( this.options );
        }
    });
})(jQuery);