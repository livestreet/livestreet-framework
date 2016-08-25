/**
 * Подгрузка контента
 *
 * @module ls/more
 *
 * @license   GNU General Public License, version 2
 * @copyright 2013 OOO "ЛС-СОФТ" {@link http://livestreetcms.com}
 * @author    Denis Shakhov <denis.shakhov@gmail.com>
 */

(function($) {
    "use strict";

    $.widget( "livestreet.lsMore", $.livestreet.lsComponent, {
        /**
         * Дефолтные опции
         */
        options: {
            urls: {
                load: null
            },
            selectors: {
                text: '.js-more-text'
            },
            classes: {
                loading: 'ls-more--loading',
                locked: 'ls-more--locked'
            },
            // Селектор блока с содержимым
            target: null,
            // Добавление контента в конец/начало контейнера
            // true - в конец
            // false - в начало
            append: true,
            // Параметры запроса
            params: {},
            // Проксирующие параметры
            proxy: [ 'next_page' ],
            i18n: {
                text: '@more.text',
                text_count: '@more.text_count',
                empty: '@more.empty'
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

            this.target = $( this.options.target );

            this._on({ click: 'onClick' });
            this.element.bind( 'keydown' + this.eventNamespace, 'return', this.onClick.bind( this ) );
        },

        /**
         * Коллбэк вызываемый при клике по кнопке
         */
        onClick: function ( event ) {
            if ( ! this.isLocked() ) this.load();
            event.preventDefault();
        },

        /**
         * Блокирует блок подгрузки
         */
        lock: function () {
            this._isLocked = true;
            this._addClass( 'loading locked' );
        },

        /**
         * Разблокировывает блок подгрузки
         */
        unlock: function () {
            this._isLocked = false;
            this._removeClass( 'loading locked' );
        },

        /**
         * Проверяет заблокирован виджет или нет
         */
        isLocked: function () {
            return this._isLocked;
        },

        /**
         * Получает значение счетчика
         */
        getCount: function () {
            return parseInt( this.element.data('lsmore-count'), 10 );
        },

        /**
         * Устанавливает значение счетчика
         */
        setCount: function ( number ) {
            this.element.data('lsmore-count', number);
            this.elements.text.text( this._i18n( 'text_count', number ) );
        },

        /**
         * Подгрузка
         */
        load: function () {
            this._trigger("beforeload", null, this);

            this.lock();

            this._load( 'load', function ( response ) {
                if ( response.count_loaded > 0 ) {
                    this.target[ this.options.append ? 'append' : 'prepend' ]( $.trim( response.html ) );

                    // Обновляем счетчик
                    var countLeft = this.getCount() - response.count_loaded;

                    if ( countLeft <= 0 ) {
                        response.hide = true;
                    } else {
                        this.setCount( countLeft || 0 );
                    }

                    // Обновляем параметры
                    $.each( this.options.proxy, function( k, v ) {
                        if ( response[ v ] ) this._setParam( v, response[ v ] );
                    }.bind( this ));
                } else {
                    // Для блоков без счетчиков
                    ls.msg.notice( null, this._i18n( 'empty' ) );
                }

                if ( response.hide ) {
                    this.element.hide();
                } else {
                    this.element.show();
                }

                this.unlock();

                this._trigger("afterload", null, { context: this, response: response });
            });
        }
    });
})(jQuery);