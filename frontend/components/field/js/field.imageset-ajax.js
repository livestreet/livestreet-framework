/**
 * Imageset Ajax
 *
 * @module ls/field/imageset-ajax
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
                remove: aRouter['ajax'] + 'media/remove-target/',
                load: aRouter['ajax'] + 'media/load-gallery/'
            },
            // Селекторы
            selectors: {
                image_container:".js-field-imageset-items",
                modal: '.js-field-imageset-modal',
                uploader: '.js-field-imageset-modal  .js-uploader-modal',
                show_modal: '.js-field-imageset-but-show-modal',
                choose: '.js-uploader-modal-choose',
                input:"[data-imageset-input]",
                count_input:".field-count-image"
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
                    let targetTmp = this.elements.uploader.lsUploader( 'option', 'params.target_tmp' );
                    this._setParam( 'target_tmp', targetTmp );
                    
                    if(this.elements.input.val() == '' || this.elements.input.val() === undefined){
                        this.elements.input.val(targetTmp);
                    }
                }.bind(this)
            });
                    
            this.elements.uploader.lsUploader({
                autoload: false,
                params: $.extend( {}, { security_ls_key: LIVESTREET_SECURITY_KEY }, this.options.params )
            });

            this.elements.show_modal.on( 'click' + this.eventNamespace, function () {
                this.elements.modal.lsModal( 'show' );
            }.bind(this));

            this.elements.choose.on( 'click' + this.eventNamespace, this.createPreview.bind( this ) );
            
            this.load();

        },

        /**
         * Создает превью
         */
        createPreview: function() {
            var id = this.elements.uploader.lsUploader( 'getElement', 'list' ).lsUploaderFileList( 'getSelectedFiles' ).eq( 0 ).lsUploaderFile( 'getProperty', 'id' );

            if ( ! id ) return;
                        
            this.elements.show_modal.addClass( this.option( 'classes.loading' ) );

            this.elements.modal.lsModal( 'hide' );

            this._load( 'create', { 'id': id }, function( response ) {
                this.load();
                
                this.options.params['id']=id;
            });
        },

        /**
         * Удаление превью и таргета
         */
        remove: function(e) {
            this.options.params['id'] = $(e.currentTarget).parent().data('mediaId');
            this._load( 'remove', function( response ) {
                this.load();
            });
        },

        /**
         * Подгружает созданное превью
         */
        load: function() {
            this._load( 'load', function( response ) {
                var images = $(response.html);                
                
                this.elements.count_input.val(response.count_loaded).change();      
                /*
                 * Костыль. От чего то не срабатывает событие change для parsley
                 */
                this.elements.count_input.parsley().validate();
                
                var remove = $('<i class="fa fa-trash"></i>');
                
                $.each(images, function(i, el){
                    $(el).append(remove.clone().on('click', this.remove.bind(this)));
                }.bind(this));
                
                this.elements.show_modal.removeClass( this.option( 'classes.loading' ) );
                this.elements.image_container.empty().append( images );
            });
        }
    });
})(jQuery);