{**
 * Форма поиска по тегам
 *}

{component_define_params params=[ 'mods', 'classes', 'attributes' ]}

{component 'search-form'
        name         = 'tags'
        mods         = $mods
        placeholder  = {lang 'tags.search.label'}
        classes      = 'js-tag-search-form'
        inputClasses = 'autocomplete-tags js-tag-search'
        inputName    = 'tag'
        value        = $tag}