/**
 * Details
 *
 * @module ls/details/group
 *
 * @license   GNU General Public License, version 2
 * @copyright 2013 OOO "ЛС-СОФТ" {@link http://livestreetcms.com}
 * @author    Denis Shakhov <denis.shakhov@gmail.com>
 */

(function($) {
    "use strict";

    $.widget( "livestreet.lsDetailsGroup", $.livestreet.lsComponent, {
        /**
         * Дефолтные опции
         */
        options: {
            // При открытии одного блока сворачивать все другие открытые
            // TODO: Fix nested groups
            single: true,

            // Селекторы
            selectors: {
                // Селектор сворачиваемого блока
                item: '> .js-details-group-item',
            },

            // Опции каждого сворачиваемого блока в группе
            itemOptions: {}
        },

        /**
         * Конструктор
         *
         * @constructor
         * @private
         */
        _create: function () {
            this._super();

            this.getElement( 'item' ).lsDetails( this.option( 'itemOptions' ) );

            // При открытии одного блока сворачиваем все другие открытые
            if ( this.option( 'single' ) ) {
                this._on( this.getElement( 'item' ), { lsdetailsaftershow: 'onItemShow' });
            }
        },

        /**
         * Коллбэк вызываемый при открытии блока
         */
        onItemShow: function ( event, data ) {
            this.getElement( 'item' ).not( data.element ).lsDetails( 'hide' );
        }
    });
})(jQuery);