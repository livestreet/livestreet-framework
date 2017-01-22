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

    $.widget( "livestreet.lsTime", $.livestreet.lsComponent, {
        /**
         * Дефолтные опции
         */
        options: {
            timeFormat: 'H:i'
        },

        /**
         * Конструктор
         *
         * @constructor
         * @private
         */
        _create: function() {
            this._super();
            this.element.timepicker( this.options );
        }
    });
})(jQuery);