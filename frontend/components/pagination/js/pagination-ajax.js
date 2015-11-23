/**
 * Пагинация
 *
 * @module ls/pagination/ajax
 *
 * @license   GNU General Public License, version 2
 * @copyright 2013 OOO "ЛС-СОФТ" {@link http://livestreetcms.com}
 * @author    Denis Shakhov <denis.shakhov@gmail.com>
 */

(function($) {
    "use strict";

    $.widget( "livestreet.lsPaginationAjax", $.livestreet.lsComponent, {
        /**
         * Дефолтные опции
         */
        options: {
            selectors: {
                page: '.js-pagination-item'
            },

            classes: {
                active: 'active'
            },

            html: {
                // Страница
                page: function ( pageNumber ) {
                    return '<div class="ls-pagination-item js-pagination-item" data-page="' + pageNumber + '">' +
                        '<span class="ls-pagination-item-link ls-pagination-item-inner">' + pageNumber + '</span>' +
                        '</div>';
                },
                // Разделитель
                separator: function () {
                    return '<div class="ls-pagination-item"><span class="ls-pagination-item-inner">...</span></div>';
                }
            },

            // Кол-во страниц отображаемых по бокам от активной страницы
            padding: 1,

            // Кол-во страниц
            total_pages: 10,

            // Скрывать или нет пагинатор если страница только одна
            hide_one_page: false
        },

        /**
         * Конструктор
         *
         * @constructor
         * @private
         */
        _create: function () {
            this._super();

            // Минимально возможная страница
            this.MIN_PAGE = 1;

            // Текущая страница
            this._currentPage = 0;

            // Кол-во страниц
            this._totalPages = 0;

            this.setTotalPages( this.option( 'total_pages' ) );
            this.setCurrentPage( this.MIN_PAGE );

            this.element.on( 'click', this.option( 'selectors.page' ), this._onClick.bind( this ) );
        },

        /**
         * Коллбэк вызываемый при клике по странице
         */
        _onClick: function ( event ) {
            this.go( + $( event.currentTarget ).data( 'page' ) );
        },

        /**
         * Собирает пагинатор
         */
        _build: function () {
            var current = this.getCurrentPage();
            var total = this.getTotalPages();
            var padding = this.option( 'padding' );
            var result = '';

            // Вычисляем стартовую и конечную страницы
            var start = this.MIN_PAGE;
            var end = total;

            // Проверяем нужно ли скрывать одностраничную пагинацию
            if ( total === 1 && this.option( 'hide_one_page' ) ) {
                this.element.empty().hide();
                return;
            }

            // Проверяем нужно ли выводить разделители "..." или нет
            if ( total > padding * 2 + 1 ) {
                start = ( current - padding < 4 ) ? 1 : current - padding;
                end = ( current + padding > total - 3 ) ? total : current + padding;
            }

            if ( start > 2 ) {
                result += this.option( 'html.page' )( 1 ) + this.option( 'html.separator' )();
            }

            for (var i = start; i <= end; i++) {
                result += this.option( 'html.page' )( i );
            };

            if ( end < total - 1 ) {
                result += this.option( 'html.separator' )() + this.option( 'html.page' )( total );
            }

            this.element.show().html( result );
        },

        /**
         * Получает номер текущей страницы
         *
         * @return {Number} Текущая страница
         */
        getCurrentPage: function () {
            return this._currentPage;
        },

        /**
         * Устанавливает номер текущей страницы
         *
         * @param {Number} pageNumber Номер страницы
         */
        setCurrentPage: function ( pageNumber ) {
            if ( pageNumber < 1 || pageNumber > this.getTotalPages() ) return;

            this._currentPage = pageNumber;

            this._build();

            this.element
                .find( this.option( 'selectors.page' ) )
                .removeClass( this.option( 'classes.active' ) )
                .filter( '[data-page=' + pageNumber + ']' )
                .addClass( this.option( 'classes.active' ) );

            this._trigger( 'pageset', null, pageNumber );
        },

        /**
         * Получает кол-во страниц
         */
        getTotalPages: function () {
            return this._totalPages;
        },

        /**
         * Устанавливает кол-во страниц
         *
         * @param {Number} totalPages Кол-во страниц
         */
        setTotalPages: function ( totalPages ) {
            if ( totalPages < this.MIN_PAGE ) {
                throw new RangeError( 'Parameter totalPages must be greater than ' + this.MIN_PAGE );
            }

            this._totalPages = totalPages;
        },

        /**
         * Переход на указанную страницу
         *
         * @param {Number} pageNumber Номер страницы
         */
        go: function ( pageNumber ) {
            this.setCurrentPage( pageNumber );
            this._trigger( 'pagechanged', null, pageNumber );
        },

        /**
         * Переход на следующую страницу
         */
        next: function () {
            if ( this.getCurrentPage() === this.getTotalPages() ) return;

            this.go( this.getCurrentPage() + 1 );
        },

        /**
         * Переход на предыдущую страницу
         */
        prev: function () {
            if ( this.getCurrentPage() === this.MIN_PAGE ) return;

            this.go( this.getCurrentPage() - 1 );
        }
    });
})(jQuery);