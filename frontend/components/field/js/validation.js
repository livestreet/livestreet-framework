/**
 * Кастомные валидаторы
 */

jQuery(document).ready(function($) {
    ParsleyValidator
        .addValidator('rangetags', function (value, requirement) {
            var tag_count = value.replace(/ /g, "").match(/[^\s,]+(,|)/gi);
            return tag_count && tag_count.length >= requirement[0] && tag_count.length <= requirement[1];
        }, 50)
        .addMessage(LANGUAGE, 'rangetags', ls.lang.get('validate.tags.count', { 'field': '', 'min': '%s', 'max': '%s' }));
});