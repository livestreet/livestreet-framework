<p>Блок отображаемый при отсутствии какого-либо контента.</p>

{test_heading text='Использование'}

{capture 'test_example_content'}
    {component 'blankslate'
        title = 'Lorem ipsum'
        text  = 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Reprehenderit omnis, error incidunt alias a animi'}

    {component 'blankslate'
        title = 'Нет добавленных топиков'}

    {component 'blankslate'
        text = 'Нет добавленных топиков'}
{/capture}

{capture 'test_example_code'}
{ldelim}component 'blankslate'
    title = 'Lorem ipsum'
    text  = 'Lorem ipsum dolor sit amet ...'{rdelim}
{/capture}

{test_example content=$smarty.capture.test_example_content code=$smarty.capture.test_example_code}


{test_heading text='Без фона'}

<p>Мод-ор <code>no-background</code>.</p>

{capture 'test_example_content'}
    {component 'blankslate'
        title = 'Lorem ipsum'
        text  = 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Reprehenderit omnis, error incidunt alias a animi'
        mods = 'no-background'}
{/capture}

{capture 'test_example_code'}
{ldelim}component 'blankslate'
    title = 'Lorem ipsum'
    text  = 'Lorem ipsum dolor sit amet ...'
    mods  = 'no-background'{rdelim}
{/capture}

{test_example content=$smarty.capture.test_example_content code=$smarty.capture.test_example_code}