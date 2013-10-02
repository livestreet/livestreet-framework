/**
 * Вспомогательные функции
 *
 * @module utilities
 * 
 * @license   GNU General Public License, version 2
 * @copyright 2013 OOO "ЛС-СОФТ" {@link http://livestreetcms.com}
 * @author    Denis Shakhov <denis.shakhov@gmail.com>
 */

var ls = ls || {};

ls.utilities = ls.tools = (function ($) {
	/**
	 * Переводит первый символ в верхний регистр
	 */
	this.ucfirst = function(str) {
		var f = str.charAt(0).toUpperCase();
		return f + str.substr(1, str.length-1);
	};

	/**
	 * Выделяет все chekbox с определенным css классом
	 */
	this.checkAll = function(cssclass, checkbox, invert) {
		$('.'+cssclass).each(function(index, item){
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
	this.textPreview = function(textId, save, divPreview) {
		var text = WYSIWYG ? tinyMCE.activeEditor.getContent() : $('#' + textId).val();
		var ajaxUrl = aRouter['ajax']+'preview/text/';
		var ajaxOptions = {text: text, save: save};
		ls.hook.marker('textPreviewAjaxBefore');
		ls.ajax.load(ajaxUrl, ajaxOptions, function(result){
			if (!result) {
				ls.msg.error('Error','Please try again later');
			}
			if (result.bStateError) {
				ls.msg.error(result.sMsgTitle||'Error',result.sMsg||'Please try again later');
			} else {
				if (!divPreview) {
					divPreview = 'text_preview';
				}
				var elementPreview = $('#'+divPreview);
				ls.hook.marker('textPreviewDisplayBefore');
				if (elementPreview.length) {
					elementPreview.html(result.sText);
					ls.hook.marker('textPreviewDisplayAfter');
				}
			}
		});
	};

	/**
	 * Возвращает выделенный текст на странице
	 */
	this.getSelectedText = function(){
		var text = '';
		if(window.getSelection){
			text = window.getSelection().toString();
		} else if(window.document.selection){
			var sel = window.document.selection.createRange();
			text = sel.text || sel;
			if(text.toString) {
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
		element[0].className = $.trim( ( element[0].className + ' ' ).replace(new RegExp('\\b' + prefix + '.*?\\s', 'g'), '') );
	};

	return this;
}).call(ls.utilities || {},jQuery);