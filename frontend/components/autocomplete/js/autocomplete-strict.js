/**
 * lsAutocompleteStrict
 *
 * @module ls/autocompleteStrict
 *
 * @license   GNU General Public License, version 2
 * @copyright 2015 OOO "ЛС-СОФТ" {@link http://livestreetcms.com}
 * @author    Maxim Mzhelskiy <rus.engine@gmail.com>
 */

(function ($) {
    "use strict";

    $.widget("livestreet.lsAutocompleteStrict", $.ui.tagit, {

        options: {
            multiple: true,
            urls: {
                load: null
            },
            params: {}
        },

        _create: function () {
            if (!this.options.multiple) {
                this.options.tagLimit = 1;
            }

            this._superApply(arguments);

            var that = this;
            this.options.autocomplete.source = function (request, response) {
                var params = {
                    value: request.term
                };
                $.extend(params, that.options.params);
                ls.ajax.load(that.options.urls.load, params, function (data) {
                    response(data.aItems);
                });
            };


            if (!this.options.singleField && !(/\[\]$/.test(this.options.fieldName))) {
                this.options.fieldName = this.options.fieldName + '[]';
            }

            this.tagInput.unbind('keydown');
            this.tagInput
                .keydown(function (event) {
                    // Backspace is not detected within a keypress, so it must use keydown.
                    if (event.which == $.ui.keyCode.BACKSPACE && that.tagInput.val() === '') {
                        var tag = that._lastTag();
                        if (!that.options.removeConfirmation || tag.hasClass('remove')) {
                            // When backspace is pressed, the last tag is deleted.
                            that.removeTag(tag);
                        } else if (that.options.removeConfirmation) {
                            tag.addClass('remove ui-state-highlight');
                        }
                    } else if (that.options.removeConfirmation) {
                        that._lastTag().removeClass('remove ui-state-highlight');
                    }
                });


            // Autocomplete.
            this.tagInput.autocomplete('destroy');
            if (this.options.availableTags || this.options.tagSource || this.options.autocomplete.source) {
                var autocompleteOptions = {
                    select: function (event, ui) {
                        that.createTagNew(ui.item);
                        // Preventing the tag input to be updated with the chosen value.
                        return false;
                    },
                    focus: function () {
                        return false;
                    }
                };
                $.extend(autocompleteOptions, this.options.autocomplete);

                // tagSource is deprecated, but takes precedence here since autocomplete.source is set by default,
                // while tagSource is left null by default.
                autocompleteOptions.source = this.options.tagSource || autocompleteOptions.source;

                this.tagInput.autocomplete(autocompleteOptions);
                this.tagInput.autocomplete('widget').addClass('tagit-autocomplete');
            }
        },

        createTag: function (value, additionalClass, duringInitialization) {
            // clear
        },

        createTagNew: function (item, additionalClass, duringInitialization) {
            var that = this;

            if (this.options.preprocessTag) {
                item = this.options.preprocessTag(item);
            }

            var value = $.trim(item.value);
            var label = $.trim(item.label);

            if (value === '') {
                return false;
            }

            if (!this.options.allowDuplicates) {
                var existingTag = this._findTagByValue(value);
                if (existingTag) {
                    if (this._trigger('onTagExists', null, {
                            existingTag: existingTag,
                            duringInitialization: duringInitialization
                        }) !== false) {
                        if (this._effectExists('highlight')) {
                            existingTag.effect('highlight');
                        }
                    }
                    return false;
                }
            }

            if (this.options.tagLimit && this._tags().length >= this.options.tagLimit) {
                this._trigger('onTagLimitExceeded', null, {duringInitialization: duringInitialization});
                return false;
            }

            label = $(this.options.onTagClicked ? '<a class="tagit-label"></a>' : '<span class="tagit-label"></span>').text(label);

            // Create tag.
            var tag = $('<li></li>')
                .addClass('tagit-choice ui-widget-content ui-state-default ui-corner-all')
                .addClass(additionalClass)
                .append(label)
                .data('value', value);

            if (this.options.readOnly) {
                tag.addClass('tagit-choice-read-only');
            } else {
                tag.addClass('tagit-choice-editable');
                // Button for removing the tag.
                var removeTagIcon = $('<span></span>')
                    .addClass('ui-icon ui-icon-close');
                var removeTag = $('<a><span class="text-icon">\xd7</span></a>') // \xd7 is an X
                    .addClass('tagit-close')
                    .append(removeTagIcon)
                    .click(function (e) {
                        // Removes a tag when the little 'x' is clicked.
                        that.removeTag(tag);
                    });
                tag.append(removeTag);
            }

            // Unless options.singleField is set, each tag has a hidden input field inline.
            if (!this.options.singleField) {
                var escapedValue = this._escapeHtml(value);
                tag.append('<input type="hidden" value="' + escapedValue + '" name="' + this.options.fieldName + '" class="tagit-hidden-field" />');
            }

            if (this._trigger('beforeTagAdded', null, {
                    tag: tag,
                    tagLabel: this.tagLabel(tag),
                    duringInitialization: duringInitialization
                }) === false) {
                return;
            }

            if (this.options.singleField) {
                var tags = this.assignedTags();
                tags.push(value);
                this._updateSingleTagsField(tags);
            }

            this.tagInput.val('');

            // Insert tag.
            this.tagInput.parent().before(tag);

            this._trigger('afterTagAdded', null, {
                tag: tag,
                tagLabel: this.tagLabel(tag),
                duringInitialization: duringInitialization
            });

            if (this.options.showAutocompleteOnFocus && !duringInitialization) {
                setTimeout(function () {
                    that._showAutocomplete();
                }, 0);
            }
        },

        _findTagByValue: function (value) {
            var that = this;
            var tag = null;
            this._tags().each(function (i) {
                var v = that.tagValue(this);
                if (that._formatStr(value) == that._formatStr(v)) {
                    tag = $(this);
                    return false;
                }
            });
            return tag;
        },

        tagValue: function (tag) {
            return $(tag).data('value');
        },

        removeTag: function (tag, animate) {
            var singleFieldOld = this.options.singleField;

            this.options.singleField = false;
            this._superApply(arguments);
            this.options.singleField = singleFieldOld;

            if (this.options.singleField) {
                var tags = this.assignedTags();
                var removedTagLabel = this.tagValue(tag);
                tags = $.grep(tags, function (el) {
                    return el != removedTagLabel;
                });
                this._updateSingleTagsField(tags);
            }
        },

        _escapeHtml: function (text) {
            var map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };
            return text.replace(/[&<>"']/g, function (m) {
                return map[m];
            });
        }

    });
})(jQuery);