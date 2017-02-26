/**
 * Visual editor
 *
 * @module ls/editor/visual
 *
 * @license   GNU General Public License, version 2
 * @copyright 2013 OOO "ЛС-СОФТ" {@link http://livestreetcms.com}
 * @author    Denis Shakhov <denis.shakhov@gmail.com>
 */

(function($) {
    "use strict";

    $.widget( "livestreet.lsEditorVisual", {
        /**
         * Дефолтные опции
         */
        options: {
            set: 'default',
            sets: {
                common: {
                    language: LANGUAGE,
                    plugins: 'media table fullscreen autolink link pagebreak code autoresize livestreet',
                    skin: 'livestreet',
                    menubar: false,
                    statusbar: false,
                    pagebreak_separator: '<cut>',
                    forced_root_block: false,
                    extended_valid_elements: 'ls[user]',
                    custom_elements: '~ls',
                    short_ended_elements: 'ls img br hr param',
                    relative_urls: false,
                    remove_script_host: false,
                    style_formats: [
                        {
                            title: 'Headings', items: [
                            { title: 'Heading 4', format: 'h4' },
                            { title: 'Heading 5', format: 'h5' },
                            { title: 'Heading 6', format: 'h6' }
                        ]
                        },

                        {
                            title: 'Inline', items: [
                            { title: 'Bold', icon: 'bold', format: 'bold' },
                            { title: 'Italic', icon: 'italic', format: 'italic' },
                            { title: 'Underline', icon: 'underline', format: 'underline' },
                            { title: 'Strikethrough', icon: 'strikethrough', format: 'strikethrough' },
                            { title: 'Superscript', icon: 'superscript', format: 'superscript' },
                            { title: 'Subscript', icon: 'subscript', format: 'subscript' }
                        ]
                        },

                        {
                            title: 'Blocks', items: [
                            { title: 'Paragraph', format: 'p' },
                            { title: 'Blockquote', format: 'blockquote' },
                            { title: 'Code', format: 'code' },
                            { title: 'Pre', format: 'pre' }
                        ]
                        },

                        {
                            title: 'Alignment', items: [
                            { title: 'Left', icon: 'alignleft', format: 'alignleft' },
                            { title: 'Center', icon: 'aligncenter', format: 'aligncenter' },
                            { title: 'Right', icon: 'alignright', format: 'alignright' },
                            { title: 'Justify', icon: 'alignjustify', format: 'alignjustify' }
                        ]
                        }
                    ]
                },
                default: {
                    toolbar: 'styleselect ls-pre ls-code | bold italic strikethrough underline blockquote table | bullist numlist | link media ls-media ls-user | lsuser removeformat pagebreak code fullscreen'
                },
                light: {
                    toolbar: 'styleselect ls-pre ls-code | bold italic strikethrough underline blockquote | bullist numlist | removeformat code'
                }
            }
        },

        /**
         * Конструктор
         *
         * @constructor
         * @private
         */
        _create: function () {
            this.__init();
        },

        /**
         * 
         */
        __init: function () {
            this.element.tinymce( $.extend( {
                setup: function (editor) {
                    editor.on('keydown', function(event) {
                        if (event.ctrlKey && event.keyCode == 13){
                            this._trigger('submitted');
                            event.preventDefault();
                        }
                    }.bind(this));
                }.bind(this)
            }, this.option( 'sets.common' ), this.option( 'sets.' + this.option( 'set' ) ) ) );
        },

        /**
         * 
         */
        onShow: function () {
            this.element.tinymce().destroy();
            this.__init();
        },

        /**
         * Вставка текста
         *
         * @param {String} text Текст для вставки
         */
        insert: function ( text ) {
            this.element.tinymce().insertContent( text );
        },

        /**
         * 
         */
        getText: function () {
            this.element.tinymce().getContent();
        },

        /**
         * 
         */
        setText: function ( text ) {
            this.element.tinymce().setContent( text );
        },

        /**
         * 
         */
        focus: function () {
            this.element.tinymce().focus();
        },

        /**
         * 
         */
        showMedia: function ( text ) {
            this.option( 'media' ).lsMedia( 'show' );
        }
    });
})(jQuery);