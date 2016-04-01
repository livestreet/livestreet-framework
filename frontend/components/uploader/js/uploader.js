/**
 * Media
 *
 * @module ls/uploader
 *
 * @license   GNU General Public License, version 2
 * @copyright 2013 OOO "ЛС-СОФТ" {@link http://livestreetcms.com}
 * @author    Denis Shakhov <denis.shakhov@gmail.com>
 */

(function($) {
    "use strict";

    $.widget( "livestreet.lsUploader", $.livestreet.lsComponent, {
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
                // Удаление
                remove: aRouter['ajax'] + 'media/remove-file/',
                // Обновление св-ва
                update_property: aRouter['ajax'] + 'media/save-data-file/',
                // Генерация временного хэша
                generate_target_tmp: aRouter['ajax'] + 'media/generate-target-tmp/'
            },

            // Селекторы
            selectors: {
                // Список файлов
                list: '.js-uploader-list',
                // Информация о файле
                info: '.js-uploader-info',

                // Контейнер с элементами blocks и empty
                aside: '.js-uploader-aside',
                // Контейнер который отображается когда есть активный файл
                // и скрывается когда активного файла нет
                blocks: '.js-uploader-blocks',
                // Сообщение об отсутствии активного файла
                empty: '.js-uploader-aside-empty',
                content: '.js-uploader-content',

                // Drag & drop зона
                upload_zone:  '.js-uploader-area',
                // Инпут
                upload_input: '.js-uploader-file',

                filter: '.js-uploader-filter',
                filter_item: '.js-uploader-filter-item',
                list_blankslate: '.js-uploader-list-blankslate',
                list_pagination: '.js-uploader-list-pagination'
            },

            // Классы
            classes: {
                empty: 'is-empty'
            },

            // Настройки загрузчика
            fileupload : {
                url: null,
                sequentialUploads: false,
                singleFileUploads: true,
                limitConcurrentUploads: 3
            },

            // Доп-ые параметры передаваемые в аякс запросах
            params: {},

            // Подгрузка файлов сразу после иниц-ии
            autoload: true,

            info_options: {},
            list_options: {}
        },

        /**
         * Конструктор
         *
         * @constructor
         * @private
         */
        _create: function () {
            this._super();

            this.option( 'target_type', this.option( 'params.target_type' ) );

            /**
             * Генерация временного хэша для привязки
             * TODO: Перенести в media
             *
             * Может быть ситуация, когда на странице несколько аплоадеров для одного типа таргета медиа
             * В итоге они все делают запрос на получение временного ключа и перезаписывают его
             * Нужно использовать внешний триггер для фиксации первого запроса
             */
            if ( ! this.option( 'params.target_id' ) ) {
                this.option( 'params.target_tmp', this.element.data( 'tmp' ) || $.cookie( 'media_target_tmp_' + this.option( 'params.target_type' ) ) );

                if ( ! this.option( 'params.target_tmp' ) ) {
                    this.generateTargetTmp();
                }
            }

            // Иниц-ия саб-компонентов
            this.elements.info.lsUploaderInfo( $.extend( {}, this.option( 'info_options' ), {
                urls: {
                    update_property: this.option( 'urls.update_property' )
                },
                uploader: this.element
            }));
            this.elements.list.lsUploaderFileList( $.extend( {}, this.option( 'list_options' ), {
                urls: {
                    load: this.option( 'urls.load' ),
                    remove: this.option( 'urls.remove' )
                },
                params: this.option( 'params' ),
                beforeload: this._onFileListBeforeLoad.bind( this ),
                afterload: this._onFileListLoaded.bind( this ),
                filebeforeactivate: this._onFileBeforeActivate.bind( this ),
                fileactivate: this._onFileActivate.bind( this ),
                filedeactivate: this._onFileDeactivate.bind( this ),
                filebeforedeactivate: this._onFileBeforeDeactivate.bind( this ),
                fileafterremove: this._onFileAfterRemove.bind( this )
            }));

            this.elements.list_pagination.lsPaginationAjax({
                pagechanged: this._onPageChanged.bind( this )
            });

            this._initFileUploader();

            this._activeFilter = 'uploaded';

            this._on( this.elements.filter_item, { click: function ( event ) {
                this.setTargetTypeFilter( $( event.target ).data( 'filter' ) );
            }});

            // Подгрузка списка файлов
            this.option( 'autoload' ) && this.elements.list.lsUploaderFileList( 'load' );
        },

        /**
         * 
         */
        setTargetTypeFilter: function ( filter ) {
            var targetType = filter === 'all' ? null : this.option( 'target_type' );

            this._activeFilter = filter;

            this.elements.filter_item.removeClass('active')
            this.elements.filter_item.filter('[data-filter=' + filter + ']').addClass('active');

            this.elements.list.lsUploaderFileList( 'option', 'params.target_type', targetType );
            this.elements.list.lsUploaderFileList( 'option', 'params.page', 1 );

            this.elements.list_pagination.hide();

            this.reload();
        },

        /**
         * Иниц-ия загрузчика
         */
        _initFileUploader: function() {
            // Настройки загрузчика
            $.extend( this.option( 'fileupload' ), {
                url:      this.option( 'urls.upload' ),
                dropZone: this.elements.upload_zone
            });

            // Иниц-ия плагина
            this.elements.upload_input.fileupload( this.option( 'fileupload' ) );

            // Коллбэки
            this.element.on({
                fileuploadadd: this.onUploadAdd.bind( this ),
                fileuploaddone: function( event, data ) {
                    this[ data.result.bStateError ? 'onUploadError' : 'onUploadDone' ]( data.context, data.result );
                }.bind( this ),
                fileuploadprogress: function( event, data ) {
                    this.onUploadProgress( data.context, parseInt( data.loaded / data.total * 100, 10 ) );
                }.bind( this )
            });
        },

        /**
         * Изменяет высоту списка так, чтобы она была равна высоте сайдбара
         */
        _resizeFileList: function() {
            var asideHeight = this.getElement( 'aside' ).outerHeight();
            var maxHeight = this.getElement( 'list' ).lsUploaderFileList( 'option', 'max_height' );

            if ( ! this.getElement( 'aside' ).hasClass( 'is-empty' ) && asideHeight > maxHeight ) {
                this.getElement( 'list' ).css( 'max-height', asideHeight );
            } else {
                this.getElement( 'list' ).css( 'max-height', maxHeight );
            }
        },

        /**
         * 
         */
        _onPageChanged: function( event, page ) {
            this.getElement( 'list' ).lsUploaderFileList( 'option', 'params.page', page );
            this.reload();
        },

        /**
         * 
         */
        _onFileAfterRemove: function( event, data ) {
            this.checkEmpty();
        },

        /**
         * 
         */
        _onFileBeforeDeactivate: function( event, data ) {
            this.hideBlocks();
            this.getElement( 'info' ).lsUploaderInfo( 'empty' );
        },

        /**
         * 
         */
        _onFileBeforeActivate: function( event, data ) {
            this.showBlocks();
            this.getElement( 'info' ).lsUploaderInfo( 'setFile', data.element );

            this._trigger( 'filebeforeactivate', event, data );
        },

        /**
         * 
         */
        _onFileDeactivate: function( event, data ) {
            this._resizeFileList();

            this._trigger( 'fileafteractivate', event, data );
        },

        /**
         * 
         */
        _onFileActivate: function( event, data ) {
            this._resizeFileList();

            this._trigger( 'fileafteractivate', event, data );
        },

        /**
         * 
         */
        _onFileListBeforeLoad: function( event, data ) {
            this.elements.list_blankslate.hide();
        },

        /**
         * 
         */
        _onFileListLoaded: function( event, data ) {
            if ( data.response.pagination ) {
                this.elements.list_pagination
                    .lsPaginationAjax( 'setTotalPages', data.response.pagination.iCountPage )
                    .lsPaginationAjax( 'setCurrentPage', data.response.pagination.iCurrentPage )
                    .show();
            } else {
                this.elements.list_pagination.hide();
            }

            this.checkEmpty();
            this._resizeFileList();
        },

        /**
         * 
         */
        onUploadProgress: function( file, percent ) {
            file.lsUploaderFile( 'setProgress', percent );
        },

        /**
         * 
         */
        onUploadAdd: function( event, file ) {
            // TODO: Перенести в иниц-ию fileuploader'а

            // В параметрах заменяем null на пустую строку
            $.each( this.option( 'params' ), function ( key, value ) {
                value === null && this.option( 'params.' + key, '' );
            }.bind( this ));

            // Устанавливаем актуальные параметры для загрузчика,
            // т.к. они могли измениться с момента иниц-ии (например target_tmp)
            $( event.target ).fileupload( 'option', 'formData', this.option( 'params' ) );

            this.elements.list.lsUploaderFileList( 'addFile', file );
            this.elements.list_blankslate.hide();
        },

        /**
         * 
         */
        onUploadDone: function( file, response ) {
            if ( ! this.elements.list.lsUploaderFileList( 'option', 'multiselect' ) ) {
                this.elements.list.lsUploaderFileList( 'unselectAll' );
            }

            file.lsUploaderFile( 'destroy' );
            file.replaceWith(
                // TODO: Fix
                this.elements.list.lsUploaderFileList( 'initFiles', $( $.trim( response.sTemplateFile ) ) )
                    .lsUploaderFile( 'uploaded' )
            );

            file = null;
        },

        /**
         * 
         */
        onUploadError: function( file, response ) {
            ls.msg.error( response.sMsgTitle, response.sMsg );

            file.lsUploaderFile( 'error' );

            setTimeout(function () {
                file.lsUploaderFile( 'removeDom' );
                file = null;
            }.bind( this ), 500 );
        },

        /**
         * Генерация хэша для привязки к нему загруженных файлов
         * Суть в том, чтобы не делать несколько запросов на генерацию для одного типа (когда на одной странице несколько аплоадеров)
         */
        generateTargetTmp: function() {
            var key = 'ls.media.target_tmp_create_request_' + this.option( 'params.target_type' );

            if ( ls.registry.get( key ) ) {
                this.window.bind( key, function( e, sTmpKey ) {
                    this.option( 'params.target_tmp', sTmpKey || null );
                }.bind( this ));
            } else {
                ls.registry.set(key, true);

                this._load( 'generate_target_tmp', {
                    type: this.option( 'params.target_type' )
                }, function( response ) {
                    this.window.trigger( key, [response.sTmpKey] );
                    this.option( 'params.target_tmp', response.sTmpKey || null );
                }, { async: false });
            }
        },

        /**
         * Скрывает контейнер с блоками
         */
        hideBlocks: function() {
            this._addClass( this.getElement( 'aside' ), 'empty' );
        },

        /**
         * Показывает контейнер с блоками
         */
        showBlocks: function() {
            this._removeClass( this.getElement( 'aside' ), 'empty' );
        },

        /**
         * Проверяет пустой список файлов или нет
         */
        checkEmpty: function() {
            this.elements.list_blankslate[ this.getElement( 'list' ).lsUploaderFileList( 'isEmpty' ) ? 'show' : 'hide' ]();
        },

        /**
         * Получает элемент
         */
        getElement: function( name ) {
            return this.elements[ name ];
        },

        /**
         * 
         */
        reload: function() {
            this.getElement( 'list' ).lsUploaderFileList( 'load' );
        },

        /**
         * Получает активный файл
         */
        getActiveFile: function() {
            return this.getElement( 'list' ).lsUploaderFileList( 'getActiveFile' );
        },

        /**
         * Получает выделенные файлы
         */
        getSelectedFiles: function() {
            return this.getElement( 'list' ).lsUploaderFileList( 'getSelectedFiles' );
        },

        /**
         * Получает файлы
         */
        getFiles: function() {
            return this.getElement( 'list' ).lsUploaderFileList( 'getFiles' );
        },

        /**
         * Убирает выделение со всех файлов
         */
        unselectAll: function() {
            this.getElement( 'list' ).lsUploaderFileList( 'unselectAll' );
        },

        /**
         * Показывает файлы только определенного типа, файлы других типов скрываются
         *
         * @param {Array} types Массив типов файлов которые необходимо показать
         */
        filterFilesByType: function( types ) {
            this.getElement( 'list' ).lsUploaderFileList( 'filterFilesByType', types );
        },

        /**
         * Сбрасывает текущий фильтр (показывает все файлы)
         */
        resetFilter: function() {
            this.getElement( 'list' ).lsUploaderFileList( 'resetFilter' );
        },
    });
})(jQuery);