/**
 * Tabs
 *
 * @module tab
 * 
 * @license   GNU General Public License, version 2
 * @copyright 2013 OOO "ЛС-СОФТ" {@link http://livestreetcms.com}
 * @author    Denis Shakhov <denis.shakhov@gmail.com>
 */

(function($) {
    "use strict";

    var _selectors = {
        // Блок с табами
        tabs:  '[data-type=tabs]',
        // Таб
        tab:   '[data-type=tab]',
        // Блок с контентом табов
        panes: '[data-type=tab-panes]',
        // Блок с контентом таба
        pane:  '[data-type=tab-pane]'
    };

    $.widget( "livestreet.tab", {
        /**
         * Дефолтные опции
         */
        options: {
            // Блок с содержимым таба
            target: null,

            // Настройки аякса

            // Ссылка
            url: null,
            // Название переменной с результатом
            result: 'sText',
            // Параметры запроса
            params: {},
            
            // Callbacks

            // Вызывается при активации таба
            beforeActivate: null,
            // Вызывается в конце активации таба
            activate: null
        },

        /**
         * Конструктор
         *
         * @constructor
         * @private
         */
        _create: function() {
            this.options.params = ls.utilities.getDataOptions(this.element, 'param');
            this.options = $.extend({}, this.options, ls.utilities.getDataOptions(this.element, 'tab'));

            this.pane = $( '#' + this.options.target );

            this._on({
                click: function (e) {
                    this.activate();
                    e.preventDefault();
                }
            });
            
            // Поддержка активации табов с помощью хэшей
            // Активируем таб с классом active
            if ( this.options.target == location.hash.substring(1) || ( this.options.url && this.element.hasClass( ls.options.classes.states.active ) && ! this.pane.text() ) ) this.activate();
        },


        /**
         * Активация таба
         */
        activate: function () {
            this._trigger("beforeActivate", null, this);

            // Активируем таб
            this.element
                .addClass('active')
                .closest(_selectors.tabs)
                .find(_selectors.tab)
                .not(this.element)
                .removeClass('active');

            // Показываем блок с контентом таба
            this.pane
                .show()
                .closest(_selectors.panes)
                .find(_selectors.pane)
                .not(this.pane)
                .hide();

            // Поддержка дропдаунов
            // var dropdown = this.element.closest('ul').parent('li');
            // if (dropdown.length > 0) dropdown.addClass('active');

            // Загрузка содержимого таба через аякс
            if (this.options.url) {
                this._load();
            } else {
                this._trigger("activate", null, this);
            }
        },

        /**
         * Загрузка содержимого таба через аякс
         * 
         * @private
         */
        _load: function () {
            this.pane.empty().addClass('loading');

            ls.ajax.load(this.options.url, this.options.params, function (result) {
                this.pane.removeClass('loading');

                if (result.bStateError) {
                    this.pane.removeClass('loading');
                    //this.pane.html('Error');
                } else {
                    this.pane.html(result[this.options.result]);
                }
                
                this._trigger("activate", null, this);
            }.bind(this), {
                error: function (result) {
                    this.pane.removeClass('loading');
                    //this.pane.html('Error');
                }.bind(this)
            });
        }
    });
})(jQuery);