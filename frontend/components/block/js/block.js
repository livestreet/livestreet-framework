/**
 * Block
 *
 * @module ls/block
 *
 * @license   GNU General Public License, version 2
 * @copyright 2013 OOO "ЛС-СОФТ" {@link http://livestreetcms.com}
 * @author    Denis Shakhov <denis.shakhov@gmail.com>
 */

(function($) {
    "use strict";

    $.widget( "livestreet.lsBlock", $.livestreet.lsComponent, {
        /**
         * Дефолтные опции
         */
        options: {
            // Селекторы
            selectors: {
                // Блок с табами
                tabs: '.js-tabs-block',
                // Блок-обертка содержимого табов
                pane_container: '[data-tab-panes]'
            }
        },

        /**
         * Конструктор
         *
         * @constructor
         * @private
         */
        _create: function () {
            this._super();

            // Сохраняем высоту блока при переключении табов
            this.elements.tabs.lsTabs({
                tabbeforeactivate: function () {
                    var h = this.elements.pane_container.height();

                    this.elements.pane_container.css( 'height', h > 150 ? h : 150 );
                }.bind( this ),
                tabactivate: function () {
                    this.elements.pane_container.css( 'height', 'auto' );
                }.bind( this )
            });
        }
    });
})(jQuery);