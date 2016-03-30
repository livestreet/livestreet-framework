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
                upload: null,
                // Подгрузка файлов
                load: null,
                // Удаление файла
                remove: null,
                // Обновление св-ва
                update_property: null,
                // Кол-во загруженных файлов
                count: null,
                // Генерация временного хэша
                generate_target_tmp: null
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

            i18n: {
                empty: '@uploader.attach.empty',
                count: '@uploader.attach.count'
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

            this.elements.button.on( 'click', this.showUploader.bind( this ) );

            this.elements.modal.lsModal({
                aftershow: this.onUploaderShow.bind( this ),
                afterhide: this.onUploaderHide.bind( this )
            });

            this.elements.uploader.lsUploader({
                autoload: false,
                urls: this.option( 'urls' ),
                params: this.option( 'params' )
            });
        },

        /**
         * Показывает загрузчик
         */
        showUploader: function() {
            this.elements.modal.lsModal( 'show' );
        },

        /**
         * Коллбэк вызываемый после того как загрузчик показан
         */
        onUploaderShow: function() {
            this.elements.uploader.lsUploader( 'getElement', 'list' ).lsUploaderFileList( 'load' );
            // т.к. генерация происходит после инициализации
            this._setParam( 'target_tmp', this.elements.uploader.lsUploader( 'option', 'params.target_tmp' ) );
        },

        /**
         * Коллбэк вызываемый после того как загрузчик закрыт
         */
        onUploaderHide: function() {
            this.updateCounter();
            this._trigger( 'afterhide', null, this );
        },

        /**
         * Обновляет счетчик
         */
        updateCounter: function() {
            this.elements.counter.text( '...' );

            this._load( 'count', function ( response ) {
                this.setCounter( response.count );
            }.bind( this ));
        },

        /**
         * Устанавливает счетчик
         *
         * @param {Number} count Кол-во загруженных файлов
         */
        setCounter: function( count ) {
            if ( count <= 0 ) {
                this.elements.counter.text( this._i18n( 'empty' ) );
            } else {
                this.elements.counter.text( this._i18n( 'count', count ) );
            }
        }
    });
})(jQuery);