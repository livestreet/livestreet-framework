/**
 * Tabs
 *
 * @module ls/tab
 *
 * @license   GNU General Public License, version 2
 * @copyright 2013 OOO "ЛС-СОФТ" {@link http://livestreetcms.com}
 * @author    Denis Shakhov <denis.shakhov@gmail.com>
 */

(function($) {
    "use strict";

    $.widget( "livestreet.lsTab", $.livestreet.lsComponent, {
        /**
         * Дефолтные опции
         */
        options: {
            // Блок с содержимым таба
            target: null,
            // Контейнер с табами
            tabs: $(),

            // Классы
            classes: {
                active: 'active',
                loading: 'loading'
            },

            // Настройки аякса

            // Ссылка
            urls: {
                load: null
            },
            // Название переменной с результатом
            result: 'sText',
            // Параметры запроса
            params: {},

            // Callbacks

            // Вызывается при активации таба
            beforeactivate: null,
            // Вызывается в конце активации таба
            activate: null
        },

        /**
         * Конструктор
         *
         * @constructor
         * @private
         */
        _create: function() {
            this._super();

            this._pane = $( '#' + this.option( 'target' ) );

            this._on({ click: 'onClick' });

            // Поддержка активации табов с помощью хэшей
            // Активируем таб с классом active
            if ( this.options.target == location.hash.substring(1)
                || (
                    this.options.urls.load
                    && this._hasClass( 'active' )
                    && ! this._pane.text()
                )
            ) this.activate();
        },

        /**
         * 
         */
        onClick: function ( event ) {
            this.activate();
            event.preventDefault();
        },

        /**
         * Проверяет активирован таб или нет
         *
         * @return {Boolean}
         */
        isActive: function () {
            return this._hasClass( 'active' );
        },

        /**
         * Активация таба
         */
        activate: function () {
            this._trigger( 'beforeactivate', null, this );

            // Активируем таб
            this._addClass( 'active' );
            this._pane.show();

            // Загрузка содержимого таба через аякс
            if ( this.options.urls.load ) {
                this._loadContent();
            } else {
                this._trigger( 'activate', null, this );
            }
        },

        /**
         * Деактивирует таб
         */
        deactivate: function () {
            this._removeClass( 'active' );
            this._pane.hide();
        },

        /**
         * Получает блок с контентом
         */
        getPane: function () {
            return this._pane;
        },

        /**
         * Установливает текст блока с контентом
         */
        setPaneContent: function ( html ) {
            return this.getPane().html( html );
        },

        /**
         * Загрузка содержимого таба через аякс
         *
         * @private
         */
        _loadContent: function () {
            this._addClass( this._pane.empty(), 'loading' );

            this._load( 'load', function ( response ) {
                this.setPaneContent( response[ this.options.result ] );
            }, {
                onError: function ( response ) {
                    this._removeClass( this._pane, 'loading' );
                    //this._pane.html('Error');
                }.bind( this ),
                onComplete: function ( response ) {
                    this._removeClass( this._pane, 'loading' );
                    this._trigger( 'activate', null, this );
                }.bind( this )
            });
        }
    });
})(jQuery);