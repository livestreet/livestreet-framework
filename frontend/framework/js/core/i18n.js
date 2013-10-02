/**
 * Модаль для работы с локализацией
 *
 * @module i18n
 * 
 * @license   GNU General Public License, version 2
 * @copyright 2013 OOO "ЛС-СОФТ" {@link http://livestreetcms.com}
 * @author    Denis Shakhov <denis.shakhov@gmail.com>
 */

var ls = ls || {};

ls.lang = ls.i18n = (function ($) {
	"use strict";

	/**
	 * Набор текстовок
	 *
	 * @private
	 */
	var _msgs = {};

	/**
	 * Загрузка текстовок
	 *
	 * @param {Object} msgs Текстовки
	 */
	this.load = function(msgs) {
		$.extend(true, _msgs, msgs);
	};

	/**
	 * Получает текстовку
	 *
	 * @param {String} name    Название текстовки
	 * @param {String} replace Список аргументов для замены
	 */
	this.get = function(name, replace) {
		if (_msgs[name]) {
			var value = _msgs[name];

			if (replace) {
				value = value.tr(replace);
			}

			return value;
		}

		return '';
	};

	return this;
}).call(ls.lang || {}, jQuery);