/**
 * Details
 *
 * @module ls/details
 *
 * @license   GNU General Public License, version 2
 * @copyright 2013 OOO "ЛС-СОФТ" {@link http://livestreetcms.com}
 * @author    Denis Shakhov <denis.shakhov@gmail.com>
 */

(function($) {
    "use strict";

    $.widget( "livestreet.lsDetails", $.livestreet.lsComponent, {
        /**
         * Дефолтные опции
         */
        options: {
            selectors: {
                // Заголовок
                title: '> .js-details-title',
                // Основной блок с содержимым
                body: '> .js-details-body'
            },

            // Классы
            classes: {
                // Класс добавляемый при разворачивании
                open: 'is-open'
            }

            // Коллбэк вызываемый после разворачивания
            // aftershow: function( event, data ) {}

            // Коллбэк вызываемый после сворачивания
            // afterhide: function( event, data ) {}
        },

        /**
         * Конструктор
         *
         * @constructor
         * @private
         */
        _create: function () {
            this._super();

            // Разворачиваем блок если он отмечен как открытый,
            // это необходимо т.к. по умолчанию блок с содержимым скрыт
            if ( this.isOpen() ) this.show();

            // Показать/скрыть содержимое
            this._on( this.getElement( 'title' ), { click: 'toggle' });
        },

        /**
         * Показать/скрыть содержимое
         */
        toggle: function () {
            this[ this.isOpen() ? 'hide' : 'show' ]();
        },

        /**
         * Развернуть
         */
        show: function () {
            this._addClass( 'open' );
            this._trigger("aftershow", null, this);
        },

        /**
         * Свернуть
         */
        hide: function () {
            this._removeClass( 'open' );
            this._trigger("afterhide", null, this);
        },

        /**
         * Развернут или нет
         */
        isOpen: function () {
            return this._hasClass( 'open' );
        }
    });
})(jQuery);