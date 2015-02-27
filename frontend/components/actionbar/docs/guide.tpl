<p>Список действий.</p>

{test_heading text='Использование'}

<p>У компонента необходимо указать массив с группами кнопок <code>items</code>.</p>

{capture 'test_example_content'}
    {component 'actionbar' items=[
        [
            'buttons' => [
                [ 'icon' => 'cog' ]
            ]
        ],
        [
            'buttons' => [
                [ 'icon' => 'edit' ],
                [ 'icon' => 'trash' ],
                [ 'icon' => 'exclamation-sign', 'text' => 'Mark as spam' ],
                [ 'text' => 'Report' ]
            ]
        ]
    ]}
{/capture}

{capture 'test_example_code'}
{ldelim}component 'actionbar' items=[
    [
        'buttons' => [
            [ 'icon' => 'cog' ]
        ]
    ],
    [
        'buttons' => [
            [ 'icon' => 'edit' ],
            [ 'icon' => 'trash' ],
            [ 'icon' => 'exclamation-sign', 'text' => 'Mark as spam' ],
            [ 'text' => 'Report' ]
        ]
    ]
]{rdelim}
{/capture}

{test_example content=$smarty.capture.test_example_content code=$smarty.capture.test_example_code}