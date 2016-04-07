/**
 * Кастомные валидаторы
 */
window.Parsley.addAsyncValidator('livestreet', function (xhr) {
    this._remoteCache = {};

    xhr.done(function (response) {
        if ('undefined' !== typeof response.errors) {
            var name = this.$element.attr('name') || this.$element.attr('id');
            var msg = response.errors[name].join("<br>");
            window.ParsleyUI.updateError(this, 'remote', msg);
            return false;
        }
    }.bind(this));

    return 'undefined' === typeof xhr.responseJSON.errors;
});

jQuery(function ($) {
    Parsley.addValidator('rangetags', {
        requirementType: [ 'integer', 'integer' ],
        validateString: function(value, min, max) {
            var tag_count = value.replace(/ /g, "").match(/[^\s,]+(,|)/gi);
            return tag_count && tag_count.length >= min && tag_count.length <= max;
        },
        messages: {
            ru: ls.lang.get('validate.tags.count', {'field': '', 'min': '%s', 'max': '%s'})
        }
    });
});