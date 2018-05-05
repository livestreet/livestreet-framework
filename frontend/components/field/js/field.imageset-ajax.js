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

    $.widget( "livestreet.lsFieldImagesetAjax", $.livestreet.lsComponent, {
        /**
         * Дефолтные опции
         */
        options: {
            // Ссылки
            urls: {
                create: aRouter['ajax'] + 'media/create-preview-file/',
                remove: aRouter['ajax'] + 'media/remove-preview-file/',
                load: aRouter['ajax'] + 'media/load-gallery/'
            },
            // Селекторы
            selectors: {
//                remove: '.js-field-image-ajax-remove',
                image: '.js-field-imageset-ajax-image',
                image_template: '.js-imageset-template-item',
                image_container:".js-field-imageset-items",
                modal: '.js-field-imageset-modal',
                uploader: '.js-field-imageset-modal  .js-uploader-modal',
                show_modal: '.js-field-imageset-but-show-modal',
                choose: '.js-uploader-modal-choose'
            },
            // Классы
            classes: {
                loading: 'ls-loading'
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

            this.elements.modal.lsModal({
                aftershow: function () {
                    this.elements.uploader.lsUploader( 'getElement', 'list' ).lsUploaderFileList( 'load' );
                    // т.к. генерация происходит после инициализации
                    this._setParam( 'target_tmp', this.elements.uploader.lsUploader( 'option', 'params.target_tmp' ) );
                }.bind(this)
            });
                    
            this.elements.uploader.lsUploader({
                autoload: false,
                params: $.extend( {}, { security_ls_key: LIVESTREET_SECURITY_KEY }, this.options.params )
            });

            this.elements.show_modal.on( 'click' + this.eventNamespace, function () {
                this.elements.modal.lsModal( 'show' );
            }.bind(this));
//
//            this.elements.remove.on( 'click' + this.eventNamespace, this.remove.bind( this ) );
            this.elements.choose.on( 'click' + this.eventNamespace, this.createPreview.bind( this ) );

        },

        /**
         * Создает превью
         */
        createPreview: function() {
            var id = this.elements.uploader.lsUploader( 'getElement', 'list' ).lsUploaderFileList( 'getSelectedFiles' ).eq( 0 ).lsUploaderFile( 'getProperty', 'id' );

            if ( ! id ) return;
                        
            this.elements.show_modal.addClass( this.option( 'classes.loading' ) );

            //this.elements.image_template.show().addClass( this.option( 'classes.loading' ) );
            this.elements.modal.lsModal( 'hide' );

            this._load( 'create', { 'id': id }, function( response ) {
                this.load();
                
                this.options.params['id']=id;
            });
        },

        /**
         * Удаление превью
         */
        remove: function(e) {
            
            console.log(e.currentTarget)
//            this._load( 'remove', function( response ) {
//                this.elements.image.empty().hide();
//                this.elements.remove.hide();
//                this.elements.show_modal.show();
//            });
        },

        /**
         * Подгружает созданное превью
         */
        load: function() {
            this._load( 'load', function( response ) {
                var images = $(response.html)
                
                var remove = $('<i class="fa fa-trash"></i>');
                
                $.each(images, function(i, el){
                    $(el).append(remove.clone().on('click', this.remove.bind(this)));
                }.bind(this));
                
                this.elements.show_modal.removeClass( this.option( 'classes.loading' ) );
                this.elements.image_container.show().append( images );
            });
        }
    });
})(jQuery);