{test_heading text='Шаблоны'}

{plugin_docs_api_file items=[
    [
        'title' => 'avatar',
        'content' => 'Аватар',
        'body' => {plugin_docs_params params=[
            [ 'image', 'string', 'null', 'Ссылка на изображения' ],
            [ 'size', 'string', 'default', 'Размер (large, default, small, xsmall, xxsmall, text). Альтернативный вариант модификатора <code>size-...</code>' ],
            [ 'name', 'string', 'null', 'Имя объекта' ],
            [ 'alt', 'string', 'null', 'Атрибут alt у изображения' ],
            [ 'url', 'string', 'null', 'Ссылка' ],
            [ 'mods', 'string', 'null', 'Список классов-модификаторов (указываются через пробел)' ],
            [ 'classes', 'string', 'null', 'Дополнительные классы (указываются через пробел)' ],
            [ 'attributes', 'array', 'null', 'Атрибуты' ]
        ]}
    ],
    [
        'title' => 'avatar-list',
        'content' => 'Группировка аватаров',
        'body' => {plugin_docs_params params=[
            [ 'items', 'array|string', 'null', 'Массив аватаров, либо строка с html кодом' ],
            [ 'blankslateParams', 'array', 'null', 'Параметры для компонента <code>blankslate</code>, который выводится если массив аваторов пуст' ],
            [ 'mods', 'string', 'null', 'Список классов-модификаторов (указываются через пробел)' ],
            [ 'classes', 'string', 'null', 'Дополнительные классы (указываются через пробел)' ],
            [ 'attributes', 'array', 'null', 'Атрибуты' ]
        ]}
    ]
]}

{test_heading text='Модификаторы'}

{plugin_docs_api_file items=[
    [
        'title' => 'avatar',
        'content' => 'Модификаторы шаблона <code>avatar</code>',
        'body' => {plugin_docs_params_short params=[
            [ 'inline', 'Выводить имя объекта в одну строку с аватаром' ],
            [ 'size-small', 'Средний аватар (64x64)' ],
            [ 'size-xsmall', 'Маленький аватар (48x48)' ],
            [ 'size-xxsmall', 'Очень маленький аватар (24x24)' ],
            [ 'size-text', 'Очень маленький аватар для использования в тексте (18x18)' ]
        ]}
    ]
]}