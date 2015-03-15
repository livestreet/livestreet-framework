{test_heading text='Использование'}

<p>Список свойств объекта.</p>

{capture 'test_example_content'}
    {component 'info-list' list=[
        [ 'label' => 'Вес:', 'content' => '0.1кг' ],
        [ 'label' => 'Размеры:', 'content' => '100х100мм' ],
        [ 'label' => 'Цвет:', 'content' => 'Белый' ]
    ]}
{/capture}

{capture 'test_example_code'}
{ldelim}component 'info-list' list=[
    [ 'label' => 'Вес:', 'content' => '0.1кг' ],
    [ 'label' => 'Размеры:', 'content' => '100х100мм' ],
    [ 'label' => 'Цвет:', 'content' => 'Белый' ]
]{rdelim}
{/capture}

{test_example content=$smarty.capture.test_example_content code=$smarty.capture.test_example_code}