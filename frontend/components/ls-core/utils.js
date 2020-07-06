/**
 * Вспомогательные функции
 *
 * @module utils
 *
 * @license   GNU General Public License, version 2
 * @copyright 2013 OOO "ЛС-СОФТ" {@link http://livestreetcms.com}
 * @author    Denis Shakhov <denis.shakhov@gmail.com>
 */

var ls = ls || {};

ls.utils = (function ($) {
    /**
     * Переводит первый символ в верхний регистр
     */
    this.ucfirst = function (str) {
        var f = str.charAt(0).toUpperCase();
        return f + str.substr(1, str.length - 1);
    };

    /**
     * Выделяет все chekbox с определенным css классом
     */
    this.checkAll = function (cssclass, checkbox, invert) {
        $('.' + cssclass).each(function (index, item) {
            if (invert) {
                $(item).attr('checked', !$(item).attr("checked"));
            } else {
                $(item).attr('checked', $(checkbox).attr("checked"));
            }
        });
    };

    /**
     * Предпросмотр
     */
    this.textPreview = function (mTextSelector, mPreviewSelector, bSave) {
        var sText = WYSIWYG ? tinyMCE.activeEditor.getContent() : (typeof mTextSelector === 'string' ? $(mTextSelector) : mTextSelector).val(),
            sUrl = aRouter['ajax'] + 'preview/text/',
            oParams = {text: sText, save: bSave};

        ls.hook.marker('textPreviewAjaxBefore');

        ls.ajax.load(sUrl, oParams, function (result) {
            if (result.bStateError) {
                ls.msg.error(result.sMsgTitle || 'Error', result.sMsg || 'Please try again later');
            } else {
                var oPreview = typeof mTextSelector === 'string' ? $(mPreviewSelector || '#text_preview') : mPreviewSelector;

                ls.hook.marker('textPreviewDisplayBefore');

                if (oPreview.length) {
                    oPreview.html(result.sText);

                    ls.hook.marker('textPreviewDisplayAfter');
                }
            }
        });
    };

    /**
     * Возвращает выделенный текст на странице
     */
    this.getSelectedText = function () {
        var text = '';
        if (window.getSelection) {
            text = window.getSelection().toString();
        } else if (window.document.selection) {
            var sel = window.document.selection.createRange();
            text = sel.text || sel;
            if (text.toString) {
                text = text.toString();
            } else {
                text = '';
            }
        }
        return text;
    };

    /**
     * Получает значения атрибутов data с заданным префиксом
     */
    this.getDataOptions = function (element, prefix) {
        var prefix = prefix || 'option',
            resultOptions = {},
            dataOptions = typeof element === 'string' ? $(element).data() : element.data();

        for (var option in dataOptions) {
            if (option === 'options') continue;

            // Remove 'option' prefix
            if (option.substring(0, prefix.length) == prefix) {
                var str = option.substring(prefix.length);
                resultOptions[str.charAt(0).toLowerCase() + str.substring(1)] = dataOptions[option];
            }
        }

        return resultOptions;
    };

    /**
     * Удаляет классы с заданным префиксом
     */
    this.removeClassByPrefix = function (element, prefix) {
        element[0].className = $.trim(( element[0].className + ' ' ).replace(new RegExp('\\b' + prefix + '.*?\\s', 'g'), ''));
    };

    /**
     * Блокирует/разблокировывает форму
     */
    this.formLockAccessor = function (sName) {
        return function (oForm) {
            var oElements = oForm.find('input, select, textarea, button').filter(sName == 'lock' ? ':not(:disabled)' : '.js-ls-form-disabled');

            oElements.each(function (iIndex, oInput) {
                $(this).prop('disabled', sName == 'lock' ? true : false)[sName == 'lock' ? 'addClass' : 'removeClass']('js-ls-form-disabled');
            });
        }
    };

    /**
     * Блокирует форму
     */
    this.formLock = function (oForm) {
        this.formLockAccessor('lock').apply(this, arguments);
    };

    /**
     * Разблокировывает форму
     */
    this.formUnlock = function (oForm) {
        this.formLockAccessor('unlock').apply(this, arguments);
    };

    /**
     * Возвращает форматированное оставшееся время
     */
    this.timeRemaining = function (seconds) {
        days = parseInt(seconds / 86400);
        seconds = seconds % 86400;

        hours = parseInt(seconds / 3600);
        seconds = seconds % 3600;

        minutes = parseInt(seconds / 60);
        seconds = parseInt(seconds % 60);

        if (days > 0) {
            return days + ', ' + hours + ':' + minutes + ':' + seconds;
        }
        if (hours > 0) {
            return hours + ':' + minutes + ':' + seconds;
        }
        if (minutes > 0) {
            return minutes + ':' + seconds;
        }
        return seconds;
    };

    /**
     * Экранирует HTML символы в тексте
     *
     * @param text
     * @return {String}
     */
    this.escapeHtml = function (text) {
        return text
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    };

    this.copyTextToClipboard = function (text) {
        var textArea = document.createElement("textarea");

        //
        // *** This styling is an extra step which is likely not required. ***
        //
        // Why is it here? To ensure:
        // 1. the element is able to have focus and selection.
        // 2. if element was to flash render it has minimal visual impact.
        // 3. less flakyness with selection and copying which **might** occur if
        //    the textarea element is not visible.
        //
        // The likelihood is the element won't even render, not even a
        // flash, so some of these are just precautions. However in
        // Internet Explorer the element is visible whilst the popup
        // box asking the user for permission for the web page to
        // copy to the clipboard.
        //

        // Place in top-left corner of screen regardless of scroll position.
        textArea.style.position = 'fixed';
        textArea.style.top = 0;
        textArea.style.left = 0;

        // Ensure it has a small width and height. Setting to 1px / 1em
        // doesn't work as this gives a negative w/h on some browsers.
        textArea.style.width = '2em';
        textArea.style.height = '2em';

        // We don't need padding, reducing the size if it does flash render.
        textArea.style.padding = 0;

        // Clean up any borders.
        textArea.style.border = 'none';
        textArea.style.outline = 'none';
        textArea.style.boxShadow = 'none';

        // Avoid flash of white box if rendered for any reason.
        textArea.style.background = 'transparent';


        textArea.value = text;

        document.body.appendChild(textArea);
        textArea.focus();
        textArea.select();

        try {
            var successful = document.execCommand('copy');
            var msg = successful ? 'successful' : 'unsuccessful';
            //console.log('Copying text command was ' + msg);
        } catch (err) {

        }
        document.body.removeChild(textArea);
    };

    this.pathinfo = function (filepath) {
        var group = filepath.split("/");
        var filenameWithExtension = group.pop();
        var filename_splitted = filenameWithExtension.split('.');
        var extension = filename_splitted.pop();
        if (extension === filenameWithExtension) {
            return {
                dirname: group.join('/'),
                filename: filenameWithExtension,
                extension: null
            };
        } else {
            return {
                dirname: group.join('/'),
                filename: filename_splitted[0],
                extension: extension
            };
        }
    };

    return this;
}).call(ls.utils || {}, jQuery);