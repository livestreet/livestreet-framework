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
			this.options = $.extend({}, this.options, ls.utils.getDataOptions(this.element, 'captcha'));

            this._on({
                click: function (e) {
                    this.update();
                    e.preventDefault();
                }
            });

            this.update();
        },

		/**
		 * Получает url каптчи
		 */
		getUrl: function () {
			return PATH_FRAMEWORK_LIBS_VENDOR + '/kcaptcha/index.php?' + SESSION_NAME + '=' + SESSION_ID + '&n=' + Math.random() + '&name=' + this.options.name;
		},

		/**
		 * Обновляет каптчу
		 */
		update: function () {
			this.element.css('background-image', 'url(' + this.getUrl() + ')');
		}
    });
})(jQuery);