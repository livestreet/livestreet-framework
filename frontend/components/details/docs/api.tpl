{test_heading text='Шаблоны'}

{plugin_docs_api_file items=[
    [
        'title' => 'details',
        'content' => 'Сворачиваемый блок',
        'body' => {plugin_docs_params params=[
            [ 'title',           'string',  'null',  'Заголовок' ],
            [ 'content',         'string',  'null',  'Содержимое' ],
            [ 'body',            'string',  'null',  'Содержимое основного блока' ],
            [ 'open',            'boolean', 'false',  'Если true, то блок будет развернут' ],
            [ 'mods',            'string',  'null',  'Список модификторов основного блока (через пробел)' ],
            [ 'classes',         'string',  'null',  'Список классов основного блока (через пробел)' ],
            [ 'attributes',      'array',   'null',  'Список атрибутов основного блока' ]
        ]}
    ],
    [
        'title' => 'group',
        'content' => 'Группа сворачиваемых блоков',
        'body' => {plugin_docs_params params=[
            [ 'items',           'array',   'null',  'Список сворачиваемых блоков' ],
            [ 'mods',            'string',  'null',  'Список модификторов основного блока (через пробел)' ],
            [ 'classes',         'string',  'null',  'Список классов основного блока (через пробел)' ],
            [ 'attributes',      'array',   'null',  'Список атрибутов основного блока' ]
        ]}
    ]
]}

{test_heading text='Виджеты'}

{plugin_docs_api_file items=[
    [
        'title' => 'lsDetails',
        'content' => {plugin_docs_api_file items=[
            [
                'title' => 'Опции',
                'content' => 'Опции виджета.',
                'body' => {plugin_docs_params params=[
                    [ 'selectors.title',  'string',  'null',   'Селектор заголовка' ],
                    [ 'selectors.body',   'string',  'null',   'Селектор блока с содержимым' ],
                    [ 'classes.open',     'string',  'null',   'Класс добавляемый при разворачивании' ],
                    [ 'aftershow',        'function',  'null',   'Коллбэк вызываемый после разворачивания' ],
                    [ 'afterhide',        'function',  'null',   'Коллбэк вызываемый после сворачивания' ]
                ]}
            ],
            [
                'title' => 'Методы',
                'content' => 'Методы виджета.',
                'body' => {plugin_docs_params_short params=[
                    [ 'toggle()', 'Показать/скрыть содержимое' ],
                    [ 'show()',   'Развернуть' ],
                    [ 'hide()',   'Свернуть' ]
                ]}
            ]
        ]}
    ],
    [
        'title' => 'lsDetailsGroup',
        'content' => {plugin_docs_api_file items=[
            [
                'title' => 'Опции',
                'content' => 'Опции виджета.',
                'body' => {plugin_docs_params params=[
                    [ 'single',  'boolean',  'true',   'При открытии одного блока сворачивать все другие открытые' ],
                    [ 'selectors.item',   'string',  '<code>> .js-details-group-item</code>',   'Селектор сворачиваемого блока' ]
                ]}
            ],
            [
                'title' => 'Методы',
                'content' => 'Методы виджета.',
                'body' => {plugin_docs_params_short params=[
                    [ 'onItemShow( event, data )', 'Коллбэк вызываемый при открытии блока' ]
                ]}
            ]
        ]}
    ]
]}