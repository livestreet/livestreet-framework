/**
 * Alert
 *
 * @module alert
 * 
 * @license   GNU General Public License, version 2
 * @copyright 2013 OOO "ЛС-СОФТ" {@link http://livestreetcms.com}
 * @author    Denis Shakhov <denis.shakhov@gmail.com>
 */

$.widget( "livestreet.alert", {
    /**
     * Дефолтные опции
     */
    options: {
        // Анимация при скрытии
        hide: {
            effect: 'fade',
            duration: 200
        }
    },

    /**
     * Конструктор
     *
     * @constructor
     * @private
     */
    _create: function() {
        this.closeButton = this.element.find('[data-type=alert-close]');

        this._on( this.closeButton, { 'click': this.hide } );
    },

    /**
     * Hide
     */
    hide: function () {
        this.element.hide(this.options.hide);
    }
});