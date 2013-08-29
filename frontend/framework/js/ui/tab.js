/**
 * Tabs
 *
 * @module tab
 * 
 * @license   GNU General Public License, version 2
 * @copyright 2013 OOO "ЛС-СОФТ" {@link http://livestreetcms.com}
 * @author    Denis Shakhov <denis.shakhov@gmail.com>
 *
 * Depends:
 *     jquery.ui.widget.js
 */

var ls = ls || {};

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

    // Обработка ивентов
    $(document).on('ready', function (e) {
        $(_selectors.tab).tab();
    });

    $(document).on('click.livestreet.tab', _selectors.tab, function (e) {
        $(e.currentTarget).tab('activate');
        e.preventDefault();
    });

    $.widget( "livestreet.tab", {
        /**
         * Дефолтные опции
         */
        options: {
            // Селектор объекта относительно которого будет позиционироваться тулбар
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
            this.options.params = ls.tools.getDataOptions(this.element, 'param');
            this.options = $.extend({}, this.options, ls.tools.getDataOptions(this.element));

            this.pane = $( '#' + this.options.target );
            
            // Поддержка активации табов с помощью хэшей
            if ( this.options.target == location.hash.substring(1) ) this.activate();
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

            ls.ajax(this.options.url, this.options.params, function (result) {
                this.pane.removeClass('loading');

                if (result.bStateError) {
                    ls.msg.error('Error', result.sMsg);
                } else {
                    this.pane.html(result[this.options.result]);
                }
                
                this._trigger("activate", null, this);
            }.bind(this), {
                error: function () {
                    this.pane.removeClass('loading');
                    this.pane.html('Error');
                    ls.msg.error('Error', 'Please try again later');
                }.bind(this)
            });
        }
    });
})(jQuery);