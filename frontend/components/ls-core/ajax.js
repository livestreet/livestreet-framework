    /**
 * Ajax
 *
 * @module ajax
 *
 * @license   GNU General Public License, version 2
 * @copyright 2013 OOO "ЛС-СОФТ" {@link http://livestreetcms.com}
 * @author    Denis Shakhov <denis.shakhov@gmail.com>
 */

var ls = ls || {};

ls.ajax = (function ($) {
    "use strict";

    /**
     * Выполнение AJAX запроса, автоматически передает security key
     */
    this.load = function(url, params, callback, more) {
        more = more || {};
        params = params || {};

        more.showNotices = typeof more.showNotices === 'undefined' ? true : more.showNotices;

        if ( ! more.progressNotShow ) {
            NProgress.start();
        }

        if ( typeof LIVESTREET_SECURITY_KEY !== 'undefined' ) params.security_ls_key = LIVESTREET_SECURITY_KEY;

        $.each(params, function(k, v){
            if (typeof(v) == "boolean") {
                params[k] = v ? 1 : 0;
            }
        });

        if (url.indexOf('http://') != 0 && url.indexOf('https://') != 0 && url.indexOf('/') != 0) {
            url = aRouter['ajax'] + url + '/';
        }

        var ajaxOptions = $.extend({}, {
            type: "POST",
            url: url,
            data: params,
            dataType: 'json',
            success: function( response ) {
                if ( response.bStateError ) {
                    if ( more.showNotices && ( response.sMsgTitle || response.sMsg ) ) ls.msg.error( response.sMsgTitle, response.sMsg );
                } else {
                    if ( more.showNotices && ( response.sMsgTitle || response.sMsg ) ) ls.msg.notice( response.sMsgTitle, response.sMsg );
                    if ( $.isFunction( callback ) ) callback.apply( this, arguments );
                }

                if ( $.isFunction( more.onResponse ) ) more.onResponse.apply( this, arguments );
            }.bind(this),
            error: function(msg){
                if ( $.isFunction( more.onError ) ) more.onError.apply( this, arguments );
            }.bind(this),
            complete: function(msg){
                NProgress.done();
                if ( $.isFunction( more.onComplete ) ) more.onComplete.apply( this, arguments );
            }.bind(this)
        }, more);

        ls.hook.run('ls_ajax_before', [ajaxOptions, callback, more], this);

        return $.ajax(ajaxOptions);
    };

    /**
     * Выполнение AJAX отправки формы, включая загрузку файлов
     */
    this.submit = function(url, form, callback, more) {
        var more = more || {},
            form = typeof form == 'string' ? $(form) : form,
            button = more.submitButton || form.find('[type=submit]').eq(0),
            params = more.params || {},
            lock = typeof more.lock === 'undefined' ? true : more.lock;

        params.security_ls_key = LIVESTREET_SECURITY_KEY;

        if (!more.progressNotShow) {
            NProgress.start();
        }

        if (url.indexOf('http://') != 0 && url.indexOf('https://') != 0 && url.indexOf('/') != 0) {
            url = aRouter['ajax'] + url + '/';
        }

        var options = {
            type: 'POST',
            url: url,
            dataType: more.dataType || 'json',
            data: params,
            beforeSubmit: function (arr, form, options) {
                if ( lock ) ls.utils.formLock( form );
                button && button.prop('disabled', true).addClass('loading');
            },
            beforeSerialize: function (form, options) {
                if (typeof more.validate == 'undefined' || more.validate === true) {
                    var res=form.parsley().validate();
                    if (!res) {
                        NProgress.done();
                        if ( $.isFunction( more.onValidateFail ) ) more.onValidateFail.apply( this, arguments );
                    }
                    return res;
                }

                return true;
            },
            success: function (result, status, xhr, form) {
                if ( result.aErrors ) {
                    $.each(result.aErrors, function(key, field) {
                        ls.notification.error(null, field);
                    });
                }

                if (result.bStateError) {
                    ls.msg.error(null, result.sMsg);

                    // more.warning(result, status, xhr, form);
                } else {
                    if (result.sMsg) {
                        ls.msg.notice(null, result.sMsg);
                    }

                    if (typeof callback === 'function') {
                        callback(result, status, xhr, form);
                    }
                }

                if ( $.isFunction( more.onResponse ) ) more.onResponse.apply( this, arguments );
            },
            error: more.error,
            complete: function() {
                NProgress.done();
                button.prop('disabled', false).removeClass('loading');

                if (more.complete) {
                    more.complete.apply(ls.dev,arguments);
                }

                if ( lock ) ls.utils.formUnlock( form );
            }.bind(this)
        };

        ls.hook.run('ls_ajaxsubmit_before', [options,form,callback,more], this);

        form.ajaxSubmit(options);
    };

    /**
     * Создание ajax формы
     *
     * @param  {String}          url      Ссылка
     * @param  {jQuery, String}  form     Селектор формы либо объект jquery
     * @param  {Function}        callback Success коллбэк
     * @param  {Object}          more     Дополнительные параметры
     */
    this.form = function(url, form, callback, more) {
        var form = typeof form == 'string' ? $(form) : form;

        form.on('submit', function (e) {
            ls.ajax.submit(url, form, callback, more);
            e.preventDefault();
        });
    };

    return this;
}).call(ls.ajax || {}, jQuery);