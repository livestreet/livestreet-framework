/**
 * Основной модуль
 *
 * @module ls
 * 
 * @license   GNU General Public License, version 2
 * @copyright 2013 OOO "ЛС-СОФТ" {@link http://livestreetcms.com}
 * @author    Denis Shakhov <denis.shakhov@gmail.com>
 */

Function.prototype.bind = function(context) {
	var fn = this;

	if(jQuery.type(fn) != 'function'){
		throw new TypeError('Function.prototype.bind: call on non-function');
	};

	if(jQuery.type(context) == 'null'){
		throw new TypeError('Function.prototype.bind: cant be bound to null');
	};

	return function() {
		return fn.apply(context, arguments);
	};
};

String.prototype.tr = function(a,p) {
	var k;
	var p = typeof(p)=='string' ? p : '';
	var s = this;
	jQuery.each(a,function(k){
		var tk = p?p.split('/'):[];
		tk[tk.length] = k;
		var tp = tk.join('/');
		if(typeof(a[k])=='object'){
			s = s.tr(a[k],tp);
		}else{
			s = s.replace((new RegExp('%%'+tp+'%%', 'g')), a[k]);
		};
	});
	return s;
};


var ls = ls || {};

/**
 * Дополнительные функции
 */
ls = (function ($) {
	/**
	 * Дефолтные опции
	 * 
	 * @private
	 */
	var _defaults = {
		debug: false,

		classes: {
			states: {
				active: 'active',
				loading: 'loading',
				open: 'open'
			}
		}
	};

	/**
	 * Инициализация
	 *
	 * @param {Object} options Опции
	 */
	this.init = function (options) {
		this.options = $.extend({}, _defaults, options);
	};

	/**
	 * Установка опций
	 *
	 * TODO: Заменить на registry
	 */
	this.setOption = function(option, value) {
		this.options[option] = value;
	};

	/**
	 * Дебаг сообщений
	 */
	this.debug = function() {
		if (this.options.debug) {
			this.log.apply(this, arguments);
		}
	};

	/**
	 * Лог сообщений
	 */
	this.log = function() {
		if (/*!$.browser.msie &&*/ window.console && window.console.log) {
			Function.prototype.bind.call(console.log, console).apply(console, arguments);
		} else {
			//alert(msg);
		}
	};

	return this;
}).call(ls || {}, jQuery);


/**
 * Инициализация
 */
$(function () {
	ls.init();
});