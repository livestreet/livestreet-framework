{test_heading text='Шаблоны'}

{plugin_docs_api_file items=[
    [
        'title' => 'block',
        'content' => 'Блок',
        'body' => {plugin_docs_params params=[
            [ 'title',           'string',  'null',  'Заголовок' ],
            [ 'content',         'string',  'null',  'Содержимое блока' ],
            [ 'show',            'boolean',  'true',  'Показывать или нет блок' ],
            [ 'list',            'array|string',  'null',  'Список (компонент item)' ],
            [ 'tabs',            'array|string',  'null',  'Табы (компонент tabs)' ],
            [ 'mods',            'string',  'null',  'Список модификторов основного блока (через пробел)' ],
            [ 'classes',         'string',  'null',  'Список классов основного блока (через пробел)' ],
            [ 'attributes',      'array',   'null',  'Список атрибутов основного блока' ]
        ]}
    ]
]}

{test_heading text='Виджеты'}

{plugin_docs_api_file items=[
    [
        'title' => 'lsBlock',
        'content' => {plugin_docs_api_file items=[
            [
                'title' => 'Опции',
                'content' => 'Опции виджета.',
                'body' => {plugin_docs_params params=[
                    [ 'selectors.tabs',  'string',  '<code>.js-tabs-block</code>',   'Блок с табами' ],
                    [ 'selectors.pane_container',  'string',  '<code>[data-type=tab-panes]</code>',   'Блок-обертка содержимого табов' ]
                ]}
            ]
        ]}
    ]
]}

{test_heading text='Модификаторы'}

{plugin_docs_api_file items=[
    [
        'title' => 'block',
        'content' => 'Модификаторы шаблона <code>block</code>',
        'body' => {plugin_docs_params_short params=[
            [ 'primary', 'Основной блок на странице' ],
            [ 'success', 'Success' ],
            [ 'info',    'Info' ],
            [ 'danger',  'Danger' ],
            [ 'warning', 'Warning' ],
            [ 'transparent',   'Блок с прозрачным фоном, без border\'ов' ],
            [ 'nopadding',   'Блок без внутренних отступов у содержимого блока' ]
        ]}
    ]
]}