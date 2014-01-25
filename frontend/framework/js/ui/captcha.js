/**
 * Captcha
 *
 * @module captcha
 * 
 * @license   GNU General Public License, version 2
 * @copyright 2013 OOO "ЛС-СОФТ" {@link http://livestreetcms.com}
 * @author    Denis Shakhov <denis.shakhov@gmail.com>
 */

(function($) {
    "use strict";

    var _selectors = {
        // Элемент с каптчей
		captcha:  '[data-type=captcha]'
    };

    $.widget( "livestreet.captcha", {
        /**
         * Дефолтные опции
         */
        options: {
            name: ''
        },

        /**
         * Конструктор
         *
         * @constructor
         * @private
         */
        _create: function() {
			this.options = $.extend({}, this.options, ls.utilities.getDataOptions(this.element, 'captcha'));

            this._on({
                click: function (e) {
                    this.update();
                    e.preventDefault();
                }
            });
        },

		/**
		 * Получает url каптчи
		 */
		getUrl: function () {
			return PATH_FRAMEWORK_LIBS_VENDOR + '/kcaptcha/index.php?' + SESSION_NAME + '=' + SESSION_ID + '&n=' + Math.random()+'&name='+this.options.name;
		},

		/**
		 * Обновляет каптчу
		 */
		update: function () {
			this.element.css('background-image', 'url(' + this.getUrl() + ')');
		}
    });

	// Инициализация
	$(document).on('ready', function (e) {
		$(document).on('click', '[data-type=captcha]', function (e) {
			$(this).captcha();
			$(this).captcha('update');
			e.preventDefault();
		});
	});
})(jQuery);