/**
 * Кастомные валидаторы
 */
window.Parsley.addAsyncValidator('livestreet', function (xhr) {
    this._remoteCache = {};

    xhr.done(function (response) {
        if ('undefined' !== typeof response.aErrors) {
            var name = this.$element.attr('name') || this.$element.attr('id');
            var msg = response.aErrors[name].join("<br>");
            window.ParsleyUI.updateError(this, 'remote', msg);
            return false;
        }
    }.bind(this));

    return 'undefined' === typeof xhr.responseJSON.aErrors;
});

jQuery(document).ready(function ($) {
    Parsley
        .addValidator('rangetags', function (value, requirement) {
            var tag_count = value.replace(/ /g, "").match(/[^\s,]+(,|)/gi);
            return tag_count && tag_count.length >= requirement[0] && tag_count.length <= requirement[1];
        }, 50)
        .addMessage(LANGUAGE, 'rangetags', ls.lang.get('validate.tags.count', {'field': '', 'min': '%s', 'max': '%s'}));
});