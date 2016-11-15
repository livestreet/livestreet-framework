/**
 * Модальное окно с кропом изображения
 *
 * @module ls/crop/modal
 *
 * @license   GNU General Public License, version 2
 * @copyright 2013 OOO "ЛС-СОФТ" {@link http://livestreetcms.com}
 * @author    Denis Shakhov <denis.shakhov@gmail.com>
 */

(function($) {
    "use strict";

    $.widget( "livestreet.lsCropModal", $.livestreet.lsComponent, {
        /**
         * Дефолтные опции
         */
        options: {
            // Ссылки
            urls: {
                submit: null
            },

            // Селекторы
            selectors: {
                crop: '.js-crop',
                submit: '.js-crop-submit'
            },

            cropOptions: {},

            submitted: null
        },

        /**
         * Конструктор
         *
         * @constructor
         * @private
         */
        _create: function () {
            this._super();

            this.elements.crop.lsCrop(this.option('cropOptions'));

            this._on(this.elements.submit, { click: 'onSubmit' });
        },

        /**
         * Сабмит
         */
        onSubmit: function() {
            var params = {
                size: this.elements.crop.lsCrop( 'getSelection' ),
                canvas_width: this.elements.crop.lsCrop( 'getImageData' ).naturalWidth
            };

            this._load('submit', params, 'onSubmitSuccess');
        },

        /**
         * Коллбэк вызываемый после сабмита
         */
        onSubmitSuccess: function(response) {
            this._trigger('submitted', null, { element: this.element, response: response });
            this.element.lsModal('hide');
        }
    });
})(jQuery);