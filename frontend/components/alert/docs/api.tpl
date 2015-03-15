{test_heading text='Шаблоны'}

{plugin_docs_api_file items=[
    [
        'title' => 'alert',
        'content' => 'Уведомление',
        'body' => {plugin_docs_params params=[
            [ 'title',           'string',  'null',  'Заголовок' ],
            [ 'text',            'string|array',  'null',  'Массив либо строка с текстом уведомления. Массив должен быть в формате: <code>[ [ title, msg ], ... ]</code>' ],
            [ 'visible',         'boolean',  'true',  'Показывать или нет уведомление' ],
            [ 'close',           'boolean', 'false',  'Показывать или нет кнопку закрытия' ],
            [ 'mods',            'string',  'success',  'Список модификторов основного блока (через пробел)' ],
            [ 'classes',         'string',  'null',  'Список классов основного блока (через пробел)' ],
            [ 'attributes',      'array',   'null',  'Список атрибутов основного блока' ]
        ]}
    ]
]}

{test_heading text='Виджеты'}

{plugin_docs_api_file items=[
    [
        'title' => 'lsAlert',
        'content' => {plugin_docs_api_file items=[
            [
                'title' => 'Опции',
                'content' => 'Опции виджета.',
                'body' => {plugin_docs_params params=[
                    [ 'selectors.close',  'string',  '<code>.js-alert-close</code>',   'Кнопка закрывающая уведомление' ],
                    [ 'hide',     'mixed',  'null',   'Опции анимации скрытия, <a href="http://api.jqueryui.com/jQuery.widget/#option-hide">подробнее</a>' ],
                    [ 'open',        'mixed',  'null',   'Опции анимации показывания <a href="http://api.jqueryui.com/jQuery.widget/#option-show">подробнее</a>' ]
                ]}
            ],
            [
                'title' => 'Методы',
                'content' => 'Методы виджета.',
                'body' => {plugin_docs_params_short params=[
                    [ 'show()',   'Показать' ],
                    [ 'hide()',   'Скрыть' ]
                ]}
            ]
        ]}
    ]
]}

{test_heading text='Модификаторы'}

{plugin_docs_api_file items=[
    [
        'title' => 'alert',
        'content' => 'Модификаторы шаблона <code>alert</code>',
        'body' => {plugin_docs_params_short params=[
            [ 'dismissible',   'Закрываемое уведомление' ],
            [ 'success',   'Успешно завершено' ],
            [ 'info',   'Информация' ],
            [ 'empty',   'Отсутствие содержимого' ],
            [ 'error',   'Ошибка' ]
        ]}
    ]
]}