/**
 * Imageset Ajax
 *
 * @module ls/field/image-ajax
 *
 * @license   GNU General Public License, version 2
 * @copyright 2013 OOO "ЛС-СОФТ" {@link http://livestreetcms.com}
 * @author    Oleg Demidov
 */

(function($) {
    "use strict";

    $.widget( "livestreet.lsFieldImageSetAjaxItem", $.livestreet.lsComponent, {
        /**
         * Дефолтные опции
         */
        options: {
            
            // Селекторы
            selectors: {
                remove: '.js-imageset-item-but-remove'                
            },
            
            params: {}
        },

        /**
         * Конструктор
         *
         * @constructor
         * @private
         */
        _create: function () {
            this._super();
            
            this._on(this.elements.remove, {click:'remove'});

        },

        create: function(attr){
            if(this.element.hasClass('js-imageset-template-item')){
                var clone = this.element.clone();
                clone.attr(attr);
                return clone;
            }
        },
        /**
         * Удаление 
         */
        remove: function() {
            this.element.remove();
        },

        /**
         * Подгружает созданное превью
         */
        load: function() {
//            this._load( 'load', function( response ) {
//                this.elements.image.removeClass( this.option( 'classes.loading' ) ).show().html( $.trim( response.sTemplatePreview ) );
//            });
        }
    });
})(jQuery);