/**
 * Прикрипление файлов
 *
 * @module ls/uploader/attach
 *
 * @license   GNU General Public License, version 2
 * @copyright 2013 OOO "ЛС-СОФТ" {@link http://livestreetcms.com}
 * @author    Denis Shakhov <denis.shakhov@gmail.com>
 */

(function($) {
    "use strict";

    $.widget( "livestreet.lsUploaderAttach", $.livestreet.lsComponent, {
        /**
         * Дефолтные опции
         */
        options: {
            // Ссылки
            urls: {
                // Загрузка файла
                upload: aRouter['ajax'] + 'media/upload/',
                // Подгрузка файлов
                load: aRouter['ajax'] + 'media/load-gallery/',
                // Удаление файла
                remove: aRouter['ajax'] + 'media/remove-file/',
                // Обновление св-ва
                update_property: aRouter['ajax'] + 'media/save-data-file/',
                // Генерация временного хэша
                generate_target_tmp: aRouter['ajax'] + 'media/generate-target-tmp/'
            },

            // Селекторы
            selectors: {
                // Модальное окно с загрузчиком
                modal: '.js-uploader-attach-modal',
                // Кнопка открывающая загручик
                button: '.js-uploader-attach-button',
                // Строка, кол-во загруженных файлов
                counter: '.js-uploader-attach-file-counter',
                // Загрузчик lsUploader
                uploader: '.js-uploader-modal'
            },

            params: {}
        },

        /**
         * Конструктор
         *
         * @constructor
         * @private
         */
        _create: function () {
            this._super();

            this.elements.modal.lsModal({
                aftershow: function () {
                    this.elements.uploader.lsUploader( 'getElement', 'list' ).lsUploaderFileList( 'load' );
                    // т.к. генерация происходит после инициализации
                    this._setParam( 'target_tmp', this.elements.uploader.lsUploader( 'option', 'params.target_tmp' ) );
                }.bind(this),
                afterhide: this.onHide.bind(this)
            });

            this.elements.button.on( 'click', function () {
                this.elements.modal.lsModal('show');
            }.bind(this));

            this.elements.uploader.lsUploader({
                autoload: false,
                urls: this.option( 'urls' ),
                params: $.extend( {}, { security_ls_key: LIVESTREET_SECURITY_KEY }, this.options.params )
            });
        },

        /**
         * 
         */
        onHide: function() {
            this._trigger( 'afterhide', null, this );
        }
    });
})(jQuery);