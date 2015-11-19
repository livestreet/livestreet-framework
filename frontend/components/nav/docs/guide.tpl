        <p>Навигация.</p>


{**
 * Использование
 *}
{test_heading text='Использование'}

<p>Базовое использование.</p>

{capture 'test_example_content'}
    {component 'nav'
        activeItem = 'item2'
        mods='pills'
        items=[
            [ 'name' => 'item1', 'url' => "/", 'text' => 'Item 1' ],
            [ 'name' => 'item2', 'url' => "/", 'text' => 'Item 2' ],
            [ 'name' => 'item3', 'url' => "/", 'text' => 'Item 3' ]
        ]}

    {component 'nav'
        activeItem = 'item2'
        mods='pills'
        items=[
            [ 'name' => 'item1', 'url' => "/", 'text' => 'Item 1', 'icon' => 'star' ],
            [ 'name' => 'item2', 'url' => "/", 'text' => 'Item 2', 'icon' => [ 'icon' => 'star', 'mods' => 'white' ] ],
            [ 'name' => 'item3', 'url' => "/", 'text' => 'Item 3', 'icon' => [ 'icon' => 'star', 'mods' => 'white' ] ]
        ]}
{/capture}

{capture 'test_example_code'}
{ldelim}component 'nav'
    activeItem = 'item2'
    mods = 'pills'
    items=[
        [ 'name' => 'item1', 'url' => "/", 'text' => 'Item 1' ],
        [ 'name' => 'item2', 'url' => "/", 'text' => 'Item 2' ],
        [ 'name' => 'item3', 'url' => "/", 'text' => 'Item 3' ]
    ]{rdelim}
{/capture}

{test_example content=$smarty.capture.test_example_content code=$smarty.capture.test_example_code}

{**
 * Stacked
 *}
{test_heading text='Stacked'}

{capture 'test_example_content'}
    {component 'nav'
        activeItem = 'item2'
        mods='pills stacked'
        items=[
            [ 'name' => 'item1', 'url' => "/", 'text' => 'Item 1' ],
            [ 'name' => 'item2', 'url' => "/", 'text' => 'Item 2' ],
            [ 'name' => 'item3', 'url' => "/", 'text' => 'Item 3', 'count' => 5 ],
            [ 'name' => 'item4', 'url' => "/", 'text' => 'Item 4' ],
            [ 'name' => 'item5', 'url' => "/", 'text' => 'Item 5' ]
        ]}
{/capture}

{capture 'test_example_code'}
{ldelim}component 'nav'
    activeItem = 'item2'
    mods = 'pills stacked'
    items=[
        [ 'name' => 'item1', 'url' => "/", 'text' => 'Item 1' ],
        ...
    ]{rdelim}
{/capture}

{test_example content=$smarty.capture.test_example_content code=$smarty.capture.test_example_code}

{**
 * Счетчики
 *}
{test_heading text='Счетчики'}

{capture 'test_example_content'}
    {component 'nav'
        activeItem = 'item2'
        mods='pills'
        items=[
            [ 'name' => 'item1', 'url' => "/", 'text' => 'Item 1', 'count' => 5 ],
            [ 'name' => 'item2', 'url' => "/", 'text' => 'Item 2', 'count' => '+3' ],
            [ 'name' => 'item3', 'url' => "/", 'text' => 'Item 3' ]
        ]}
{/capture}

{capture 'test_example_code'}
{ldelim}component 'nav'
    activeItem = 'item2'
    mods = 'pills'
    items=[
        [ 'name' => 'item1', 'url' => "/", 'text' => 'Item 1', 'count' => 5 ],
        [ 'name' => 'item2', 'url' => "/", 'text' => 'Item 2', 'count' => '+3' ],
        [ 'name' => 'item3', 'url' => "/", 'text' => 'Item 3' ]
    ]{rdelim}
{/capture}

{test_example content=$smarty.capture.test_example_content code=$smarty.capture.test_example_code}


{**
 * Многоуровневая навигация
 *}
{test_heading text='Многоуровневая навигация'}

{capture 'test_example_content'}
    {component 'nav'
        activeItem = 'item2'
        mods='pills'
        items=[
            [ 'name' => 'item1', 'url' => "/", 'text' => 'Item 1' ],
            [ 'name' => 'item2', 'url' => "/", 'text' => 'Item 2' ],
            [ 'name' => 'item3', 'url' => "/", 'text' => 'Item 3', 'menu' => [
                    [ 'name' => 'subitem1', 'url' => "/", 'text' => 'Sub Item 1' ],
                    [ 'name' => 'subitem2', 'url' => "/", 'text' => 'Sub Item 2' ],
                    [ 'name' => '-' ],
                    [ 'name' => 'subitem3', 'url' => "/", 'text' => 'Sub Item 3' ],
                    [ 'name' => 'subitem4', 'url' => "/", 'text' => 'Sub Item 4' ],
                    [ 'name' => 'subitem5', 'url' => "/", 'text' => 'Sub Item 5', 'menu' => [
                            [ 'name' => 'subsubitem1', 'url' => "/", 'text' => 'Sub Sub Item 1' ],
                            [ 'name' => 'subsubitem2', 'url' => "/", 'text' => 'Sub Sub Item 2' ],
                            [ 'name' => 'subsubitem3', 'url' => "/", 'text' => 'Sub Sub Item 3' ]
                        ]
                    ]
                ]
            ]
        ]}
{/capture}

{capture 'test_example_code'}
{ldelim}component 'nav'
    activeItem = 'item2'
    mods='pills'
    items=[
        [ 'name' => 'item1', 'url' => "/", 'text' => 'Item 1' ],
        [ 'name' => 'item2', 'url' => "/", 'text' => 'Item 2' ],
        [ 'name' => 'item3', 'url' => "/", 'text' => 'Item 3', 'menu' => [
                [ 'name' => 'subitem1', 'url' => "/", 'text' => 'Sub Item 1' ],
                [ 'name' => 'subitem2', 'url' => "/", 'text' => 'Sub Item 2' ],
                [ 'name' => '-' ],
                [ 'name' => 'subitem3', 'url' => "/", 'text' => 'Sub Item 3' ],
                [ 'name' => 'subitem4', 'url' => "/", 'text' => 'Sub Item 4' ],
                [ 'name' => 'subitem5', 'url' => "/", 'text' => 'Sub Item 5', 'menu' => [
                        [ 'name' => 'subsubitem1', 'url' => "/", 'text' => 'Sub Sub Item 1' ],
                        [ 'name' => 'subsubitem2', 'url' => "/", 'text' => 'Sub Sub Item 2' ],
                        [ 'name' => 'subsubitem3', 'url' => "/", 'text' => 'Sub Sub Item 3' ]
                    ]
                ]
            ]
        ]
    ]{rdelim}
{/capture}

{test_example content=$smarty.capture.test_example_content code=$smarty.capture.test_example_code}

{**
 * Badge
 *}
{test_heading text='Badge'}

{capture 'test_example_content'}
    {component 'nav'
        activeItem = 'item2'
        mods='pills'
        items=[
            [ 'name' => 'item1', 'url' => "/", 'text' => 'Item 1' ],
            [ 'name' => 'item2', 'url' => "/", 'text' => 'Item 2' ],
            [ 'name' => 'item3', 'url' => "/", 'text' => 'Item 3', count => 10 ],
            [ 'name' => 'item4', 'url' => "/", 'text' => 'Item 4', count => '9999', 'menu' => [
                    [ 'name' => 'subsubitem1', 'url' => "/", 'text' => 'Sub Sub Item 1' ],
                    [ 'name' => 'subsubitem2', 'url' => "/", 'text' => 'Sub Sub Item 2' ],
                    [ 'name' => 'subsubitem3', 'url' => "/", 'text' => 'Sub Sub Item 3', count => 10, 'menu' => [
                            [ 'name' => 'subsubitem1', 'url' => "/", 'text' => 'Sub Sub Item 1' ],
                            [ 'name' => 'subsubitem2', 'url' => "/", 'text' => 'Sub Sub Item 2' ],
                            [ 'name' => 'subsubitem3', 'url' => "/", 'text' => 'Sub Sub Item 3' ]
                        ]
                    ]
                ]
            ],
            [ 'name' => 'item5', 'url' => "/", 'text' => 'Item 5' ]
        ]}
{/capture}

{capture 'test_example_code'}
{ldelim}component 'nav'
    activeItem = 'item2'
    mods = 'main'
    items=[
        [ 'name' => 'item1', 'url' => "/", 'text' => 'Item 1' ],
        ...
    ]{rdelim}
{/capture}

{test_example content=$smarty.capture.test_example_content code=$smarty.capture.test_example_code}

{**
 * Главное меню
 *}
{test_heading text='Главное меню'}

{capture 'test_example_content'}
    {component 'nav'
        activeItem = 'item2'
        mods='main'
        items=[
            [ 'name' => 'item1', 'url' => "/", 'text' => 'Item 1' ],
            [ 'name' => 'item2', 'url' => "/", 'text' => 'Item 2', count => 10 ],
            [ 'name' => 'item3', 'url' => "/", 'text' => 'Item 3', count => 15 ],
            [ 'name' => 'item4', 'url' => "/", 'text' => 'Item 4' ],
            [ 'name' => 'item5', 'url' => "/", 'text' => 'Item 5' ]
        ]}
{/capture}

{capture 'test_example_code'}
{ldelim}component 'nav'
    activeItem = 'item2'
    mods = 'main'
    items=[
        [ 'name' => 'item1', 'url' => "/", 'text' => 'Item 1' ],
        ...
    ]{rdelim}
{/capture}

{test_example content=$smarty.capture.test_example_content code=$smarty.capture.test_example_code}