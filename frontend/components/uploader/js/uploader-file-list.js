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
			uploader: $(),

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
					'<li class="uploader-file js-uploader-file">' +
						'<div class="progress">' +
							'<div class="progress-value js-uploader-file-progress-value"></div>' +
							'<span class="progress-info js-uploader-file-progress-label">0%</span>' +
						'</div>' +
					'</li>'
			},

			file_options: {}
		},

		/**
		 * Конструктор
		 *
		 * @constructor
		 * @private
		 */
		_create: function () {
			this._super();
			this.resizeHeight();
		},

		/**
		 * Подгрузка списка файлов
		 */
		load: function() {
			this.empty();

			this._load( 'load', this.option( 'uploader' ).lsUploader( 'option', 'params' ), 'onLoad' );
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
			this.option( 'uploader' ).lsUploader( 'checkEmpty' );
			this.initFiles( this.getFiles() );
		},

		/**
		 * Добавляет файл в список
		 */
		addFile: function( data ) {
			data.context = $( this.option( 'html.file' ) );

			this.initFiles( data.context ).lsUploaderFile( 'uploading' );
			this.option( 'uploader' ).lsUploader( 'markAsNotEmpty' );
			this.element.prepend( data.context );
		},

		/**
		 * Иниц-ия файлов
		 */
		initFiles: function( files ) {
			return files.lsUploaderFile( $.extend( {}, this.option( 'file_options' ), {
				beforeactivate: this.onFileBeforeActivate.bind( this ),
				afteractivate: this.onFileAfterActivate.bind( this ),
				afterdeactivate: this.onFileAfterDeactivate.bind( this ),
				afterunselect: this.onFileAfterUnselect.bind( this ),
				afterremove: this.onFileAfterRemove.bind( this ),
				beforeclick: this.onFileBeforeClick.bind( this )
			}));
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
		 * Убирает выделение со всех файлов
		 */
		clearSelected: function() {
			this.getFiles().lsUploaderFile( 'unselect' );
		},

		/**
		 * 
		 */
		onFileBeforeClick: function( event, data ) {
			var multiselect      = this.option( 'multiselect' ),
				multiselect_ctrl = this.option( 'multiselect_ctrl' );

			if ( ! multiselect || ( multiselect && multiselect_ctrl && ! ( event.ctrlKey || event.metaKey ) ) ) {
				this.clearSelected();
			}
		},

		/**
		 * 
		 */
		onFileBeforeActivate: function( event, data ) {
			this.getActiveFile().lsUploaderFile( 'deactivate' );
		},

		/**
		 * 
		 */
		onFileAfterActivate: function( event, data ) {
			this.option( 'uploader' ).lsUploader( 'showBlocks' );
			this.option( 'uploader' ).lsUploader( 'getElement', 'info' ).lsUploaderInfo( 'setFile', data.element );
			this.resizeHeight();

			this._trigger( 'fileactivate', null, data );
		},

		/**
		 * 
		 */
		onFileAfterDeactivate: function( event, data ) {
			this.option( 'uploader' ).lsUploader( 'hideBlocks' );
			this.option( 'uploader' ).lsUploader( 'getElement', 'info' ).lsUploaderInfo( 'empty' );
			this.resizeHeight();
		},

		/**
		 * 
		 */
		onFileAfterUnselect: function( event, data ) {
			this.activateNextFile();
		},

		/**
		 * 
		 */
		onFileAfterRemove: function( event, data ) {
			data.element.lsUploaderFile( 'destroy' );
			data.element.remove();
			this.option( 'uploader' ).lsUploader( 'checkEmpty' );
		},

		/**
		 * Делает активным последний выделенный файл
		 */
		activateNextFile: function() {
			var last = this.getSelectedFiles().last();

			if ( last.length ) {
				last.lsUploaderFile( 'activate' );
			} else {
				this.option( 'uploader' ).lsUploader( 'getElement', 'info' ).lsUploaderInfo( 'empty' );
			}
		},

		/**
		 * Изменяет высоту списка так, чтобы она была равна высоте сайдбара
		 */
		resizeHeight: function() {
			var aside = this.option( 'uploader' ).lsUploader( 'getElement', 'aside' ),
				asideHeight = aside.outerHeight();

			if ( ! aside.hasClass( 'is-empty' ) && asideHeight > this.option( 'max_height' ) ) {
				this.element.css( 'max-height', asideHeight );
			} else {
				this.element.css( 'max-height', this.option( 'max_height' ) );
			}
		},

		/**
		 * Получает файлы
		 */
		getFiles: function() {
			return this.element.find( this.option( 'selectors.file' ) );
		},

		/**
		 * Показывает файлы только определенного типа, файлы других типов скрываются
		 */
		filterFilesByType: function( types ) {
			this.clearSelected();
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
		}
	});
})(jQuery);