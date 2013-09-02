/**
 * Tooltip
 *
 * @module tooltip
 * 
 * @license   GNU General Public License, version 2
 * @copyright 2013 OOO "ЛС-СОФТ" {@link http://livestreetcms.com}
 * @author    Denis Shakhov <denis.shakhov@gmail.com>
 */

var ls = ls || {};

(function($) {
    "use strict";

    /**
     * Constructs tooltip objects
     *
     * @constructor
     * @param {Object} element jQuery объект тултипа
     * @param {Object} options Опции
     */
    var Tooltip = function (element, options) {
    	this.init('tooltip', element, options);
  	};

    Tooltip.prototype = $.extend({}, $.fn.over.Constructor.prototype, {
        constructor: Tooltip,

        hooks : {
            onInitTarget: function () {
                if ( ! this.options.target && ! this.options.url ) { 
                    if ( ! this.options.content ) { 
                        this.options.content = this.$toggle.attr('title'); 
                        this.$toggle.removeAttr('title');
                    }

                    this.setContent(this.options.content);
                }
            },

            onEnter: function () {
                var title = this.$toggle.attr('title');
                
                if (title) {
                    this.options.content = title;
                    this.$toggle.removeAttr('title');
                }
            }
        }
    });

    $.fn.tooltip = function (options, variable, value) {
        return ls.over.initPlugin('tooltip', this, options, variable, value);
    };

    $.fn.tooltip.Constructor = Tooltip;

    $.fn.tooltip.defaults = $.extend({} , $.fn.over.defaults, {
    	template: '<div class="tooltip" data-type="tooltip-target"><div class="tip-arrow"></div><div class="tooltip-content" data-type="tooltip-content"></div></div>',
        alignX: 'center',
        alignY: 'top',
        effect: 'fade',
        offsetY: 10,
        duration: 500,
        delay: 500,
        event: 'hover',
        preventDefault: false
    });

    $.fn.tooltip.settings = $.extend({} , $.fn.over.settings, { 
        toggleSelector: '[data-type=tooltip-toggle]',
        targetSelector: '[data-type=tooltip-target]'
    });
})(jQuery);