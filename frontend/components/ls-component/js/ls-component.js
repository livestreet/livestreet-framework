/**
 * Родительский jquery-виджет
 * Предоставляет вспомогательные методы для дочерних виджетов
 *
 * @module ls/component
 *
 * @license   GNU General Public License, version 2
 * @copyright 2013 OOO "ЛС-СОФТ" {@link http://livestreetcms.com}
 * @author    Denis Shakhov <denis.shakhov@gmail.com>
 */

(function($) {
    "use strict";

    $.widget( "livestreet.lsComponent", {
        /**
         * Конструктор
         *
         * @constructor
         * @private
         */
        _create: function () {
            // Получаем опции из data атрибутов
            $.extend( this.options, ls.utils.getDataOptions( this.element, this.widgetName.toLowerCase() ) );

            // Получаем опции в формате JSON
            $.extend( this.options, this.element.data( this.widgetName.toLowerCase() + '-options' ) );

            // Получаем параметры отправляемые при каждом аякс запросе
            this._getParamsFromData();

            // Список локальных элементов
            this.elements = {};

            // Получаем локальные элементы компонента из селекторов
            $.each( this.options.selectors || {}, function ( key, value ) {
                this.elements[ key ] = this.element.find( value );
            }.bind( this ));

            // Генерируем методы для работы с классами
            $.each( [ 'hasClass', 'addClass', 'removeClass' ], function ( key, value ) {
                this[ '_' + value ] = function( element, classes ) {
                    if ( typeof element === "string" ) {
                        classes = element;
                        element = this.element;
                    }

                    classes = $.map( classes.split( ' ' ), function ( value ) {
                        return this.option( 'classes.' + value );
                    }.bind( this )).join( ' ' );

                    return element[ value ]( classes );
                }.bind( this )
            }.bind( this ));
        },

        /**
         * Получает локальный элемент по его имени
         */
        getElement: function( name ) {
            return this.elements[ name ];
        },

        /**
         * Получает параметры отправляемые при каждом аякс запросе
         */
        _getParamsFromData: function( url, params, callback ) {
            $.extend( this.options.params, ls.utils.getDataOptions( this.element, 'param' ) );
        },

        /**
         * Ajax запрос
         */
        _load: function( url, params, callback, more ) {
            if ( $.isFunction( params ) || typeof params === "string" ) {
                more = callback;
                callback = params;
                params = {};
            }

            params = params || {};
            if (this.option( 'params' )) {
                params = $.extend({}, this.option( 'params' ), params);
            }

            // Добавляем возможность указывать коллбэк в виде строки,
            // в этом случае будет вызываться метод текущего виджета указанный в строке
            if ( typeof callback === "string" ) {
                callback = this[ callback ];
            }

            if ( $.isFunction( callback ) ) {
                callback = callback.bind( this );
            }

            ls.ajax.load( this.options.urls[ url ], params || {}, callback, more );
        },

        /**
         * Отправка формы
         */
        _submit: function( url, form, callback, more ) {
            more = more || {};
            more.lock = typeof more.lock === 'undefined' ? true : more.lock;

            form.bind('form-submit-validate',function(){
                // todo: это не сработает, т.к. в jquery.form.js есть принудительная установка disabled=false
                if ( more.lock ) ls.utils.formLock( form );
            });

            ls.ajax.submit( this.options.urls[ url ], form, callback.bind( this ), {
                params: this.option( 'params' ) || {},
                onResponse: function () {
                    if ( more.lock ) ls.utils.formUnlock( form );
                    if ( $.isFunction( more.onResponse ) ) more.onResponse.apply( this, arguments );
                }
            });
        },

        /**
         * Устанавливает ajax параметр
         */
        _setParam: function( param, value ) {
            return this.option( 'params.' + param, value );
        },

        /**
         * Получает ajax параметр
         */
        _getParam: function( param ) {
            return this.option( 'params.' + param );
        }

        /**
         * Проверка наличия класса
         */
        // _hasClass: function( element, classes ) {},

        /**
         * Добавление класса
         */
        // _addClass: function( element, classes ) {},

        /**
         * Удаление класса
         */
        // _removeClass: function( element, classes ) {},
    });
})(jQuery);
