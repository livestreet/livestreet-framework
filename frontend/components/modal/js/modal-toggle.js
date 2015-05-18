/**
 * Modal toggle
 *
 * @module ls/modal/toggle
 *
 * @license   GNU General Public License, version 2
 * @copyright 2013 OOO "ЛС-СОФТ" {@link http://livestreetcms.com}
 * @author    Denis Shakhov <denis.shakhov@gmail.com>
 */

(function($) {
    "use strict";

    $.widget( "livestreet.lsModalToggle", $.livestreet.lsComponent, {
        /**
         * Дефолтные опции
         */
        options: {
            // ID либо jQuery объект модального окна
            modal: null,

            // Ссылки
            url: null,

            // Параметры
            params: {},

            // Опции модального-ajax окна
            // Их также можно задать через data-атрибуты с префиком lsmodal
            modalOptions: {}
        },

        /**
         * Конструктор
         *
         * @constructor
         * @private
         */
        _create: function () {
            this._super();

            this._modal = typeof this.option( 'modal' ) === 'string' ? $( '#' + this.option( 'modal' ) ) : this.option( 'modal' );

            this._on({ click: 'onClick' });
        },

        /**
         * Коллбэк вызываемый при клике
         */
        onClick: function( event ) {
            if ( this.option( 'url' ) ) {
                ls.modal.load(
                    this.option( 'url' ),
                    this.option( 'params' ),
                    $.extend( this.option( 'modalOptions' ), {
                        aftershow: function ( event, data ) {
                            this._trigger( 'show', event, data );
                        }.bind( this ),
                        afterhide: function ( event, data ) {
                            this._trigger( 'hide', event, data );
                        }.bind( this )
                    }, this.getData( 'lsmodal' ))
                );
            } else if ( this._modal ) {
                this._modal.lsModal( 'toggle' );
            }

            event.preventDefault();
        }
    });
})(jQuery);