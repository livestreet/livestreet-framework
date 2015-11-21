/**
 * Кастомные валидаторы
 */
window.ParsleyExtend = window.ParsleyExtend || {};
window.ParsleyExtend = $.extend(window.ParsleyExtend, {
    asyncValidators: $.extend({
        fields: {
            fn: function (xhr) {
                this._remoteCache = {};

                xhr.done(function (response) {
                    if ('undefined' !== typeof response.aErrors) {
                        var name = this.$element.attr('name') || this.$element.attr('id');
                        var msg = response.aErrors[name].join("<br>");
                        window.ParsleyUI.updateError(this, 'remote', msg);
                        return false
                    }
                }.bind(this));

                return 'undefined' === typeof xhr.responseJSON.aErrors;
            },
            prepare: function () {
                var params = {
                    data: {
                        fields: {
                            0: {
                                field: this.$element.attr('name') || this.$element.attr('id'),
                                value: this.getValue()
                            }
                        }
                    }
                };
                this.options.remoteOptions = $.extend(true, this.options.remoteOptions || {}, params);
            },
            url: false
        }
    }, window.ParsleyExtend.asyncValidators)
});

jQuery(document).ready(function ($) {
    Parsley
        .addValidator('rangetags', function (value, requirement) {
            var tag_count = value.replace(/ /g, "").match(/[^\s,]+(,|)/gi);
            return tag_count && tag_count.length >= requirement[0] && tag_count.length <= requirement[1];
        }, 50)
        .addMessage(LANGUAGE, 'rangetags', ls.lang.get('validate.tags.count', {'field': '', 'min': '%s', 'max': '%s'}));
});