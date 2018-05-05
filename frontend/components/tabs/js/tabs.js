/**
 * Tabs
 *
 * @module ls/tabs
 *
 * @license   GNU General Public License, version 2
 * @copyright 2013 OOO "ЛС-СОФТ" {@link http://livestreetcms.com}
 * @author    Denis Shakhov <denis.shakhov@gmail.com>
 */

(function($) {
    "use strict";

    $.widget( "livestreet.lsTabs", $.livestreet.lsComponent, {
        /**
         * Дефолтные опции
         */
        options: {
            // Селекторы
            selectors: {
                tab: '[data-tab-list]:first [data-tab]',
                pane: '[data-tab-panes]:first [data-tab-pane]'
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

            this.elements.tab.lsTab({
                tabs: this.element,
                beforeactivate: function ( event, data ) {
                    this._trigger( 'tabbeforeactivate', event, data );
                    this.getActiveTab().lsTab( 'deactivate' );
                }.bind(this),
                activate: function ( event, data ) {
                    this._trigger( 'tabactivate', event, data );
                }.bind(this)
            });
        },

        /**
         * Get tabs
         */
        getTabs: function() {
            return this.elements.tab;
        },

        /**
         * Get panes
         */
        getPanes: function() {
            return this.elements.pane;
        },

        /**
         * Get active tab
         */
        getActiveTab: function() {
            return this.getTabs().filter(function() {
                return $( this ).lsTab( 'isActive' );
            });
        }
    });
})(jQuery);