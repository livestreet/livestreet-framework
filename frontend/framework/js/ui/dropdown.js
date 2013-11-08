/**
 * Выпадающее меню
 *
 * @module dropdown
 * 
 * @license   GNU General Public License, version 2
 * @copyright 2013 OOO "ЛС-СОФТ" {@link http://livestreetcms.com}
 * @author    Denis Shakhov <denis.shakhov@gmail.com>
 */

$.widget( "livestreet.dropdown", {
    /**
     * Дефолтные опции
     */
    options: {
        // Позиционирование
        // Для позиционирования используется модуль position библиотеки jQuery UI
        position: {
            my: "left top+5",
            at: "left bottom",
            collision: "flipfit flip"
        },
        // Анимация при показе
        show: {
            effect: 'slideDown',
            duration: 200
        },
        // Анимация при скрытии
        hide: {
            effect: 'slideUp',
            duration: 200
        },
        // Поведение как у select'а
        selectable: false,
        // Выносить меню в тег body или нет
        body: false,

        // Коллбэки
        reposition: null,
        aftershow: null,
        afterhide: null,
        beforeshow: null,
        beforehide: null
    },

    /**
     * Конструктор
     *
     * @constructor
     * @private
     */
    _create: function() {
        this.options = $.extend({}, this.options, ls.utilities.getDataOptions(this.element, 'dropdown'));
        
        this._target = $( '#' + this.element.data('dropdown-target') );

        // Вынос меню в тег body
        if ( this.options.body ) this._target.appendTo('body');

        // Пункты меню
        var items = this._target.find('li:not(.dropdown-separator)');
        
        // Присваиваем текст активного пункта меню переключателю
        if ( this.options.selectable ) {
            var text = items.filter('.active').eq(0).find('a').text();
            if ( text ) this.element.text( text );
        }

        // Объект относительно которого позиционируется меню
        this.options.position.of = this.options.position.of || this.element;


        // События
        // ----------
        
        // Клик по переключателю
        this._on({
            click : function (event) {
                this.toggle();
                event.preventDefault();
            }
        });

        // Обработка кликов по пунктам меню
        this._on(true, items.find('a'), {
            click: function (event) {
                if ( this.options.selectable ) {
                    var itemLink = $(event.currentTarget);

                    items.removeClass('active');
                    itemLink.closest('li').addClass('active');
                    this.element.text( itemLink.text() );
                }

                this.hide();
            }
        });

        // Reposition menu on window scroll or resize
        $(window).on('resize scroll', this._reposition.bind(this));
        
        // Hide when click anywhere but menu or toggle
        $(document).on('click', function (event) {
            if ( ! this._target.is(event.target) && this._target.has(event.target).length === 0 && ! this.element.is(event.target) && this.element.has(event.target).length === 0 ) this.hide();
        }.bind(this));
    },

    /**
     * Показавает/скрывает меню
     */
    toggle: function () {
        if ( this._target.is(':visible') ) {
            this.hide();
        } else {
            this.show();
        }
    },

    /**
     * Показывает меню
     */
    show: function () {
        this._trigger("beforeshow", null, this);

        this._show(this._target, this.options.show, function () {
            this._trigger("aftershow", null, this);
        }.bind(this));

        this._reposition();
        this.element.addClass('open');
    },

    /**
     * Скрывает меню
     */
    hide: function () {
        if ( ! this._target.is(':visible') || this.element.data('dropdown-state-hide') === true ) return false;

        this._trigger("beforehide", null, this);

        this.element.data('dropdown-state-hide', true);

        this._hide(this._target, this.options.hide, function () {
            this.element.removeClass('open').removeData('dropdown-state-hide');
            this._trigger("afterhide", null, this);
        }.bind(this));
    },

    /**
     * Изменение положения меню
     */
    _reposition: function () {
        if ( ! this._target.is(':visible') ) return false;

        this._target.position(this.options.position);
        this._trigger("reposition", null, this);
    }
});