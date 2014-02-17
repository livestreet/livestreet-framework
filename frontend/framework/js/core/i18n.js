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
	 * Правило образования слов во множественном числе
	 * TODO: Вынести в лок-ию или конфиг
	 */
	this.oPluralRules = {
		ru: '(n % 10 == 1 && n % 100 != 11 ? 0 : n % 10 >= 2 && n % 10 <= 4 && (n % 100 < 10 || n % 100 >= 20) ? 1 : 2)',
		ua: '(n % 10 == 1 && n % 100 != 11 ? 0 : n % 10 >= 2 && n % 10 <= 4 && (n % 100 < 10 || n % 100 >= 20) ? 1 : 2)',
		en: '(n != 1)'
	};

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

	/**
	 * Склонение слов после числительных
	 *
	 * @param  {String} iNumber   Кол-во объектов
	 * @param  {Mixed}  mText     Ключ с текстовкам разделенными символом ';', либо массив
	 * @param  {String} sLanguage Язык, опциональный параметр, по дефолту берется из настроек
	 * @return {String}
	 *
	 * TODO: Добавить автозамену ## на число
	 */
	this.pluralize = function(iNumber, mText, sLanguage) {
		var aWords    = $.isArray(mText) ? mText : this.get(mText).split(';'),
			sLanguage = sLanguage || LANGUAGE,
			mIndex    = eval(this.oPluralRules[sLanguage]),
			n         = Math.abs(iNumber);

		return iNumber + ' ' + aWords[ typeof mIndex === 'boolean' ? (mIndex ? 1 : 0) : mIndex ];
	};

	return this;
}).call(ls.lang || {}, jQuery);