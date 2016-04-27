/**
 * Uploader File
 *
 * @module ls/uploader/file
 *
 * @license   GNU General Public License, version 2
 * @copyright 2013 OOO "ЛС-СОФТ" {@link http://livestreetcms.com}
 * @author    Denis Shakhov <denis.shakhov@gmail.com>
 */

(function($) {
    "use strict";

    $.widget( "livestreet.lsUploaderFile", $.livestreet.lsComponent, {
        /**
         * Дефолтные опции
         */
        options: {
            // Ссылки
            urls: {
                // Удаление
                remove: aRouter['ajax'] + 'media/remove-file/'
            },

            // Селекторы
            selectors: {
                progress_value: '.js-uploader-file-progress-value',
                progress_label: '.js-uploader-file-progress-label'
            },

            // Классы
            classes : {
                // Файл активен
                active: 'active',
                // Произошла ошибка при загрузке
                error: 'is-error',
                // Файл загружается
                uploading: 'is-uploading',
                // Файл выделен
                selected: 'is-selected'
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

            // Информация о файле
            this._info = this.getInfo();

            // Состояния файла
            this._states = {
                active: false,
                selected: false,
                uploading: false,
                error: false
            };

            this._on({ click: 'onClick' });
        },

        /**
         * Коллбэк вызываемый при клике по файлу
         */
        onClick: function( event ) {
            this._trigger( 'beforeclick', event, this );

            this.toggleActive();

            this._trigger( 'afterclick', event, this );
        },

        /**
         * Изменение состояния файла активен/не активен
         */
        toggleActive: function() {
            this[ this.getState( 'active' ) ? 'unselect' : 'activate' ]();
        },

        /**
         * Получает информацию о файле
         *
         * TODO: Refactor
         */
        getInfo: function() {
            var result = {};

            $.each( this.element[ 0 ].attributes, function( index, attr ) {
                if ( ~ attr.name.indexOf( 'data-media' ) ) {
                    result[ attr.name.slice( 11 ) ] = attr.value;
                }
            });

            return result;
        },

        /**
         * Устанавливает свойство файла
         */
        setProperty: function( name, value ) {
            this._info[ name ] = value;
        },

        /**
         * Получает свойство файла
         */
        getProperty: function( name ) {
            return this._info[ name ];
        },

        /**
         * Удаляет файл
         */
        remove: function() {
            this.unselect();

            this._load( 'remove', { id: this._info.id }, 'removeDom' );
        },

        /**
         * Удаляет файл
         */
        removeDom: function() {
            this.element.fadeOut( 500, this.onRemoveDom.bind( this ) );
        },

        /**
         * Коллбэк вызываемый после удаления
         */
        onRemoveDom: function() {
            this._trigger( 'afterremove', null, this );
        },

        /**
         * Помечает файл как активный
         */
        activate: function() {
            // Не активируем незагруженный файл
            if ( this.getState( 'active' ) || this.getState( 'error' ) || this.getState( 'uploading' ) ) return;

            this._trigger( 'beforeactivate', null, this );

            // При активации, также выделяем файл
            this.select();

            this.setState( 'active', true );
            this._addClass( 'active' );

            this._trigger( 'afteractivate', null, this );
        },

        /**
         * Помечает файл как неактивный
         */
        deactivate: function() {
            if ( ! this.getState( 'active' ) ) return;

            this._trigger( 'beforedeactivate', null, this );

            this.setState( 'active', false );
            this.element.removeClass( this.option( 'classes.active' ) );

            this._trigger( 'afterdeactivate', null, this );
        },

        /**
         * Выделяет файл
         */
        select: function() {
            if ( this.getState( 'selected' ) ) return;

            this._trigger( 'beforeselect', null, this );

            this.setState( 'selected', true );
            this._addClass( 'selected' );

            this._trigger( 'afterselect', null, this );
        },

        /**
         * Убирает выделение с файла
         */
        unselect: function() {
            if ( ! this.getState( 'selected' ) ) return;

            this._trigger( 'beforeunselect', null, this );

            this.setState( 'selected', false );
            this.element.removeClass( this.option( 'classes.selected' ) );

            // Также деактивируем файл
            if ( this.getState( 'active' ) ) this.deactivate();

            this._trigger( 'afterunselect', null, this );
        },

        /**
         * Помечает файл как незагруженный
         */
        error: function() {
            this.setState( 'error', true );
            this._addClass( 'error' );

            this.getElement( 'progress_value' ).height( 0 );
            this.getElement( 'progress_label' ).text( 'ERROR' );
        },

        /**
         * Помечает файл как незагруженный
         */
        uploading: function() {
            this.setState( 'uploading', true );
            this._addClass( 'uploading' );
        },

        /**
         * Помечает файл как загруженный
         */
        uploaded: function() {
            this.setState( 'uploading', false );
            this.element.removeClass( this.option( 'classes.uploading' ) );
        },

        /**
         * Устанавливает процент загрузки
         *
         * @param {Number} percent Процент загрузки
         */
        setProgress: function( percent ) {
            this.getElement( 'progress_value' ).height( percent + '%' );
            this.getElement( 'progress_label' ).text( percent == 100 ? 'Обработка..' : percent + '%' );
        },

        /**
         * Получает состяние
         *
         * @param {String} state Название состояния
         */
        getState: function( state ) {
            return this._states[ state ];
        },

        /**
         * Устанавливает состяние
         *
         * @param {String}  state Название состояния
         * @param {Boolean} value Значение состояния
         */
        setState: function( state, value ) {
            this._states[ state ] = value;
        }
    });
})(jQuery);