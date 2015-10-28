/**
 * Uploader File List
 *
 * @module ls/uploader/file-list
 *
 * @license   GNU General Public License, version 2
 * @copyright 2013 OOO "ЛС-СОФТ" {@link http://livestreetcms.com}
 * @author    Denis Shakhov <denis.shakhov@gmail.com>
 */

(function($) {
    "use strict";

    $.widget( "livestreet.lsUploaderFileList", $.livestreet.lsComponent, {
        /**
         * Дефолтные опции
         */
        options: {
            // Множественный выбор
            multiselect: true,

            // Множественный выбор только при нажатии на CTRL
            multiselect_ctrl: true,

            // Максимальная высота списка при отсутствии активного файла
            max_height: 113 * 3 + 15,

            // Ссылки
            urls: {
                load: aRouter['ajax'] + "media/load-gallery/"
            },

            // Селекторы
            selectors: {
                file: '.js-uploader-file'
            },

            // Классы
            classes : {
                loading: 'loading'
            },

            // HTML
            // TODO: Move to template
            html: {
                file:
                    '<li class="ls-uploader-file js-uploader-file">' +
                        '<div class="progress">' +
                            '<div class="progress-value js-uploader-file-progress-value"></div>' +
                            '<span class="progress-info js-uploader-file-progress-label">0%</span>' +
                        '</div>' +
                    '</li>'
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
        },

        /**
         * Подгрузка списка файлов
         */
        load: function() {
            this._trigger( 'beforeload', null, this );

            this.unselectAll();
            this.empty();
            this._load( 'load', 'onLoad' );
        },

        /**
         * Очистка списка
         */
        empty: function() {
            this.getFiles().lsUploaderFile( 'destroy' ).remove();
            this.element.empty();
            this._addClass( 'loading' );
        },

        /**
         * Пустой список или нет
         */
        isEmpty: function() {
            return ! this.getFiles().length;
        },

        /**
         * Коллбэк вызываемый после подгрузки списка файлов
         */
        onLoad: function( respone ) {
            this._removeClass( 'loading' ).html( $.trim( respone.html ) );
            this.initFiles( this.getFiles() );

            this._trigger( 'afterload', null, this );
        },

        /**
         * Добавляет файл в список
         */
        addFile: function( data ) {
            data.context = $( this.option( 'html.file' ) );

            this.initFiles( data.context ).lsUploaderFile( 'uploading' );
            this.element.prepend( data.context );
        },

        /**
         * Иниц-ия файлов
         */
        initFiles: function( files ) {
            return files.lsUploaderFile({
                beforeactivate: this._onFileBeforeActivate.bind( this ),
                afteractivate: this._onFileAfterActivate.bind( this ),
                afterdeactivate: this._onFileAfterDeactivate.bind( this ),
                afterunselect: this._onFileAfterUnselect.bind( this ),
                afterremove: this._onFileAfterRemove.bind( this ),
                beforeclick: this._onFileBeforeClick.bind( this )
            });
        },

        /**
         * Иниц-ия текущих файлов в списке
         */
        reinitFiles: function() {
            this.initFiles( this.getFiles().not( ":data( 'livestreet-lsUploaderFile' )" ) );
        },

        /**
         * Получает активный файл
         */
        getActiveFile: function() {
            return this.getFiles().filter( '.' + ls.options.classes.states.active );
        },

        /**
         * Получает выделенные файлы
         */
        getSelectedFiles: function() {
            return this.getFiles().filter(function () {
                return $( this ).lsUploaderFile( 'getState', 'selected' );
            });
        },

        /**
         * Получает файл по ID
         */
        getFileById: function( id ) {
            return this.getFiles().filter(function () {
                return $( this ).lsUploaderFile( 'getProperty', 'id' ) === id;
            });
        },

        /**
         * Получает файлы
         */
        getFiles: function() {
            return this.element.find( this.option( 'selectors.file' ) );
        },

        /**
         * Убирает выделение со всех файлов
         */
        unselectAll: function() {
            this.getFiles().lsUploaderFile( 'unselect' );
        },

        /**
         * Показывает файлы только определенного типа, файлы других типов скрываются
         *
         * @param {Array} types Массив типов файлов которые необходимо показать
         */
        filterFilesByType: function( types ) {
            this.unselectAll();
            this.getFiles().each(function () {
                var file = $( this );

                if ( !~ types.indexOf( file.lsUploaderFile( 'getProperty', 'type' ) ) ) {
                    file.hide();
                }
            });
        },

        /**
         * Сбрасывает текущий фильтр (показывает все файлы)
         */
        resetFilter: function() {
            this.getFiles().show();
        },

        /**
         * Делает активным последний выделенный файл
         */
        _activateNextFile: function() {
            this.getSelectedFiles().last().lsUploaderFile( 'activate' );
        },

        /**
         * 
         */
        _onFileBeforeClick: function( event, data ) {
            var multiselect      = this.option( 'multiselect' ),
                multiselect_ctrl = this.option( 'multiselect_ctrl' );

            if ( ! multiselect || ( multiselect && multiselect_ctrl && ! ( event.ctrlKey || event.metaKey ) ) ) {
                this.unselectAll();
            }
        },

        /**
         * 
         */
        _onFileBeforeActivate: function( event, data ) {
            this.getActiveFile().lsUploaderFile( 'deactivate' );
        },

        /**
         * 
         */
        _onFileAfterActivate: function( event, data ) {
            // TODO: Move
            this._trigger( 'filebeforeactivate', event, data );

            //this.resizeHeight();

            this._trigger( 'fileactivate', event, data );
        },

        /**
         * 
         */
        _onFileAfterDeactivate: function( event, data ) {
            // TODO: Move
            this._trigger( 'filebeforedeactivate', event, data );

            //this.resizeHeight();

            this._trigger( 'filedeactivate', event, data );
        },

        /**
         * 
         */
        _onFileAfterUnselect: function( event, data ) {
            this._activateNextFile();
        },

        /**
         * 
         */
        _onFileAfterRemove: function( event, data ) {
            this._trigger( 'filebeforeremove', event, data );

            data.element.lsUploaderFile( 'destroy' );
            data.element.remove();

            this._trigger( 'fileafterremove', event, data );
        },
    });
})(jQuery);