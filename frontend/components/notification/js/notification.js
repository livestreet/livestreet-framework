/**
 * Всплывающие уведомления
 *
 * @module notification
 *
 * @license   GNU General Public License, version 2
 * @copyright 2013 OOO "ЛС-СОФТ" {@link http://livestreetcms.com}
 * @author    Denis Shakhov <denis.shakhov@gmail.com>
 */

var ls = ls || {};

ls.msg = ls.notification = (function ($) {
    /**
     * Опции
     */
    this.options = {
        container_id: 'ls-notifier',
        classes: {
            notification: 'ls-notification',
            notice: 'ls-notification--success',
            info: 'ls-notification--info',
            error: 'ls-notification--error'
        },
        duration: 4000
    };

    this.init = function(options) {
        $.extend(this.options, options || {});

        $.extend($.notifier.options, {
            core:    this.options.container_id,
            box_class:    this.options.classes.notification,
            notice_class: this.options.classes.success,
            error_class:  this.options.classes.error,
            duration:     this.options.duration
        });
    };

    /**
     * Отображение информационного сообщения
     */
    this.show = function( title, message, type ) {
        if ( ! title && ! message ) {
            ls.dev.log('Необходимо указать заголовок или текст уведомления');
            return;
        }

        type = typeof type === 'undefined' ? 'notice' : type;

        $.notifier.broadcast(title, message, this.options.classes[ type ]);
    };

    /**
     * Отображение информационного сообщения
     */
    this.notice = this.success = function( title, message ) {
        this.show(title, message, 'notice');
    };

    /**
     * Отображение сообщения об ошибке
     */
    this.error = function( title, message ) {
        this.show( title, message, 'error' );
    };

    /**
     * Отображение сообщения об ошибке
     */
    this.info = function( title, message ) {
        this.show( title, message, 'info' );
    };

    return this;
}).call(ls.notification || {}, jQuery);