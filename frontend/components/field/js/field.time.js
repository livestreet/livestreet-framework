/**
 * Time picker
 *
 * @module ls/field/time
 *
 * @license   GNU General Public License, version 2
 * @copyright 2013 OOO "ЛС-СОФТ" {@link http://livestreetcms.com}
 * @author    Denis Shakhov <denis.shakhov@gmail.com>
 */

(function($) {
    "use strict";

    $.widget( "livestreet.lsFieldTime", $.livestreet.lsComponent, {
        /**
         * Дефолтные опции
         */
        options: {
            // Опции теже что и у плагина datetimepicker
        },

        /**
         * Конструктор
         *
         * @constructor
         * @private
         */
        _create: function() {
            this._super();

            $.extend( this.options, { timeOnly: true } );

            this.element.datetimepicker( this.options );
        }
    });
})(jQuery);