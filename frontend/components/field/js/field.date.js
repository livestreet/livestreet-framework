/**
 * Date picker
 *
 * @module ls/field/date
 *
 * @license   GNU General Public License, version 2
 * @copyright 2013 OOO "ЛС-СОФТ" {@link http://livestreetcms.com}
 * @author    Denis Shakhov <denis.shakhov@gmail.com>
 */

/**
 * Временный хак для форматирования даты.
 * Метод изменен так, чтобы в качестве формата даты можно было указывать функцию.
 * По дефолту форматирование в либе не поддерживается (надо подключать доп. либу momentjs)
 *
 * TODO: Использовать jquery globalize
 */
Pikaday.prototype.toString = function() {
    if (!this._d) {
        return ''
    } else if (typeof this._o.format === "function") {
        return this._o.format(this._d);
    } else {
        return hasMoment ? moment(this._d).format(format || this._o.format) : this._d.toDateString();
    }
};

(function($) {
    "use strict";

    $.widget( "livestreet.lsFieldDate", $.livestreet.lsComponent, {
        /**
         * Дефолтные опции
         */
        options: {
            format: function (date) {
                return date.getDate() + '.' + (date.getMonth() + 1) + '.' + date.getFullYear();
            },
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