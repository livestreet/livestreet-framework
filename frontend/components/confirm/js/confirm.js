/**
 * Confirm
 *
 * @module ls/confirm
 *
 * @license   GNU General Public License, version 2
 * @copyright 2013 OOO "ЛС-СОФТ" {@link http://livestreetcms.com}
 * @author    Denis Shakhov <denis.shakhov@gmail.com>
 */

$.widget( "livestreet.lsConfirm", $.livestreet.lsComponent, {
    /**
     * Дефолтные опции
     */
    options: {
        // Текст
        message: null,
        // Коллбэк вызываемый при подтверждении
        onconfirm: null,
        // Коллбэк вызываемый при отмене
        oncancel: null
    },

    /**
     * Конструктор
     *
     * @constructor
     * @private
     */
    _create: function() {
        this._super();
        this._on({ click: '_onClick' });
    },

    /**
     * Коллбэк вызываемый при клике
     */
    _onClick: function ( event ) {
        if ( window.confirm( this.option( 'message' ) ) ) {
            if ( $.isFunction( this.option( 'onconfirm' ) ) ) {
                this.option( 'onconfirm' ).call( this, event );
                event.preventDefault();
            }
        } else {
            if ( $.isFunction( this.option( 'oncancel' ) ) ) {
                this.option( 'oncancel' ).call( this, event );
            }

            event.preventDefault();
        }
    }
});