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
        options: {
            // Классы
            classes: {},
            // Селекторы
            selectors: {},
            // Ссылки
            urls: {},
            // Параметры отправляемые при каждом аякс запросе
            params: {}
        },

        /**
         * Конструктор
         *
         * @constructor
         * @private
         */
        _create: function () {
            // Получаем опции из data атрибутов
            $.extend( this.options, this.getData() );

            // Получаем опции в формате JSON
            $.extend( this.options, this.element.data( this.widgetName.toLowerCase() + '-options' ) );

            // Получаем параметры отправляемые при каждом аякс запросе
            this._getParamsFromData();

            // Список локальных элементов
            this.elements = this._getElementsFromSelectors( this.options.selectors, this.element );
        },

        /**
         * Получает элементы компонента из селекторов
         */
        _getElementsFromSelectors: function( selectors, context ) {
            var elements = {};

            $.each( selectors || {}, function ( key, value ) {
                elements[ key ] = ( context || this.document ).find( value );
            }.bind( this ));

            return elements;
        },

        /**
         * Получает локальный элемент по его имени
         */
        getElement: function( name ) {
            return this.elements[ name ];
        },

        /**
         * Получает список атрибутов с префиксом равным названию виджета
         *
         * @param {jQuery} element Элемент (опционально), по умолчанию = this.element
         * @param {String} prefix Префикс (опционально), по умолчанию равняется названию виджета
         */
        getData: function( element, prefix ) {
            if (typeof element === 'string') {
                prefix = element;
                element = this.element;
            }

            return ls.utils.getDataOptions(element || this.element, prefix || this.widgetName.toLowerCase());
        },

        /**
         * Получает параметры отправляемые при каждом аякс запросе
         */
        _getParamsFromData: function( url, params, callback ) {
            $.extend( this.options.params, this.getData( 'param' ) );
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
            if ( typeof callback === "string" ) {
                callback = this[ callback ];
            }

            if ( $.isFunction( callback ) ) {
                callback = callback.bind( this );
            }

            ls.ajax.submit( this.options.urls[ url ], form, callback, $.extend({
                params: this.option( 'params' ) || {}
            }, more ));
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
        },

        /**
         * Функция взята из widget.js
         * Добавлен вызов инлайновых коллбэков
         */
        _trigger: function( type, event, data ) {
            var prop, orig,
                callback = this.options[ type ];

            // @livestreet
            if ( typeof callback === 'string' ) {
                eval(callback);
                return true;
            }
            // @livestreet end

            data = data || {};
            event = $.Event( event );
            event.type = ( type === this.widgetEventPrefix ?
                type :
                this.widgetEventPrefix + type ).toLowerCase();
            // the original event may come from any element
            // so we need to reset the target on the new event
            event.target = this.element[ 0 ];

            // copy original event properties over to the new event
            orig = event.originalEvent;
            if ( orig ) {
                for ( prop in orig ) {
                    if ( !( prop in event ) ) {
                        event[ prop ] = orig[ prop ];
                    }
                }
            }

            this.element.trigger( event, data );
            return !( $.isFunction( callback ) &&
                callback.apply( this.element[0], [ event ].concat( data ) ) === false ||
                event.isDefaultPrevented() );
        },

        /**
         * Проверка наличия класса
         */
        _hasClass: function( element, key ) {
            if ( typeof element === "string" ) {
                key = element;
                element = this.element;
            }

            return element.hasClass( this.option( 'classes.' + key ) );
        }
    });
})(jQuery);
