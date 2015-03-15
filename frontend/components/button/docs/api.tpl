{test_heading text='Шаблоны'}

{plugin_docs_api_file items=[
    [
        'title' => 'button',
        'content' => 'Кнопка',
        'body' => {plugin_docs_params params=[
            [ 'type', 'string', 'submit', 'Тип кнопки (submit, reset, button)' ],
            [ 'text', 'string', 'null', 'Текст кнопки' ],
            [ 'url', 'string', 'null', 'Ссылка' ],
            [ 'id', 'string', 'null', 'Атрибут id' ],
            [ 'name', 'string', 'null', 'Атрибут name' ],
            [ 'isDisabled', 'boolean', 'false', 'Атрибут disabled' ],
            [ 'form', 'string', 'null', 'ID формы для сабмита' ],
            [ 'icon', 'string', 'null', 'Название иконки' ],
            [ 'classes', 'string', 'null', 'Дополнительные классы (указываются через пробел)' ],
            [ 'mods', 'string', 'null', 'Список классов-модификаторов (указываются через пробел)' ],
            [ 'attributes', 'array', 'null', 'Атрибуты' ]
        ]}
    ],
    [
        'title' => 'group',
        'content' => 'Группировка кнопок',
        'body' => {plugin_docs_params params=[
            [ 'role', 'string', 'group', 'ARIA role (group|toolbar)' ],
            [ 'buttonParams', 'array', 'null', 'Общие параметры для всех кнопок в группе' ],
            [ 'buttons', 'array|string', 'null', 'Массив кнопок, либо строка с html кодом кнопок' ]
        ]}
    ],
    [
        'title' => 'toolbar',
        'content' => 'Тулбар',
        'body' => {plugin_docs_params params=[
            [ 'groups', 'array|string', 'null', 'Массив групп кнопок, либо строка с html кодом групп кнопок' ],
            [ 'classes', 'string', 'null', 'Дополнительные классы (указываются через пробел)' ],
            [ 'mods', 'string', 'null', 'Список классов-модификаторов (указываются через пробел)' ],
            [ 'attributes', 'array', 'null', 'Атрибуты' ]
        ]}
    ]
]}

{test_heading text='Модификаторы'}

{plugin_docs_api_file items=[
    [
        'title' => 'button',
        'content' => 'Модификаторы шаблона <code>button</code>',
        'body' => {plugin_docs_params_short params=[
            [ 'primary', 'Кнопка отвечающая за основное действие' ],
            [ 'success', 'Success' ],
            [ 'info',    'Info' ],
            [ 'danger',  'Danger' ],
            [ 'warning', 'Warning' ],
            [ 'icon',    'Кнопка только с иконкой, без текста' ],
            [ 'large',   'Большая кнопка' ],
            [ 'small',   'Маленькая кнопка' ],
            [ 'xsmall',  'Очень мальнькая кнопка' ],
            [ 'block',   'Кнопка во всю ширину родительского блока' ]
        ]}
    ],
    [
        'title' => 'group',
        'content' => 'Модификаторы шаблона <code>group</code>',
        'body' => {plugin_docs_params_short params=[
            [ 'vertical', 'Вертикальное отображение кнопок' ]
        ]}
    ],
    [
        'title' => 'toolbar',
        'content' => 'Модификаторы шаблона <code>toolbar</code>',
        'body' => {plugin_docs_params_short params=[
            [ 'vertical', 'Вертикальное отображение кнопок' ]
        ]}
    ]
]}