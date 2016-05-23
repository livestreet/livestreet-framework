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
            params: {
                security_ls_key: LIVESTREET_SECURITY_KEY
            },
            // Локализация
            i18n: {},
            // Элементы
            elements: {},
            // Глобальный префикс для селекторов и текстовок, элементы с данным префиксом ищутся во всем документе,
            // а не только внутри компонента
            _globalChar: '@'
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
            this.option( 'elements', this.elements );
        },

        /**
         * Получает элементы компонента из селекторов
         */
        _getElementsFromSelectors: function( selectors, context ) {
            var elements = {},
                context = context || this.document;

            $.each( selectors || {}, function ( key, value ) {
                // Если селектор начинается с глобального символа, то ищем элемент во всем документе,
                // а не только внутри компонента
                if ( $.type(value) == 'string' && value.charAt(0) == this.option('_globalChar') ) {
                    value = value.substr(1);
                    context = this.document;
                }

                elements[ key ] = $.type( value ) == 'object'
                    ? this._getElementsFromSelectors( value, context )
                    : context.find( value );
            }.bind( this ));

            return elements;
        },

        /**
         * Получает локальный элемент по его имени
         */
        getElement: function( name ) {
            return this.option( 'elements.' + name );
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

            ls.ajax.load( this.option('urls.' + url), params || {}, callback, more );
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

            ls.ajax.submit( this.option('urls.' + url), form, callback, $.extend({
                params: this.option( 'params' ) || {}
            }, more ));
        },

        /**
         * Подготавливает форму для ajax сабмита
         */
        _form: function( url, form, callback, more ) {
            var args = arguments;

            form.on('submit', function (event) {
                event.preventDefault();
                this._submit.apply(this, [].slice.call(args));
            }.bind(this));
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
        },

        /**
         * Локализация
         *
         * @param {String}        key    Ключ текстовки в опции i18n компонента
         * @param {Object|Number} params Список для замены в текстовке, например { name: 'John Doe' } заменит строку %%name%%  текстовке на 'John Doe'.
         *                               Если вместо объекта со списком замены указано число, то этот аргумент считается аргументом count.
         * @param {Number}        count  Кол-во элементов для склонения сущ. во множ. числе
         */
        _i18n: function( key, params, count ) {
            var text = this.option( 'i18n.' + key );

            // Если в значении указан глобальный префикс, то считаем значение ключом
            // и ищем значение в глобальном массиве текстовок
            if ( text && text.charAt(0) == this.option('_globalChar') ) {
                text = ls.i18n.get( text.substr(1) );
            }

            if ( ! text ) return key;

            if ( params && $.isNumeric( params ) ) {
                count = params;
                params = null;
            }

            if ( $.isFunction( text ) ) {
                text = text();
            }

            if ( count ) {
                text = ls.i18n.pluralize( count, text );
            }

            if ( params ) {
                text = ls.i18n.replace( text, params );
            }

            return text;
        },

        /**
         * Функция взята из widget.js
         * Убрано добавление ключа из option.classes к элементу
         */
        _classes: function( options ) {
            var full = [];
            var that = this;

            options = $.extend( {
                element: this.element,
                classes: this.options.classes || {}
            }, options );

            function processClassString( classes, checkOption ) {
                var current, i;

                for ( i = 0; i < classes.length; i++ ) {
                    if ( checkOption ) {
                        if ( options.classes[ classes[ i ] ] ) {
                            full.push( options.classes[ classes[ i ] ] );
                        }
                    } else {
                        current = that.classesElementLookup[ classes[ i ] ] || $();

                        if ( options.add ) {
                            current = $( $.unique( current.get().concat( options.element.get() ) ) );
                        } else {
                            current = $( current.not( options.element ).get() );
                        }

                        that.classesElementLookup[ classes[ i ] ] = current;
                        full.push( classes[ i ] );
                    }
                }
            }

            if ( options.keys ) {
                processClassString( options.keys.match( /\S+/g ) || [], true );
            }
            if ( options.extra ) {
                processClassString( options.extra.match( /\S+/g ) || [] );
            }

            return full.join( " " );
        }
    });
})(jQuery);
