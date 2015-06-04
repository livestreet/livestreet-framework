{test_heading text='Использование'}

{capture 'test_example_content'}
    {component 'icon' icon='trash'}
{/capture}

{capture 'test_example_code'}
{ldelim}component 'icon' icon='trash'{rdelim}
{/capture}

{test_example content=$smarty.capture.test_example_content code=$smarty.capture.test_example_code}


{test_heading text='Иконки на темном фоне'}

<p>Иконкам, которые будут размещаться на темном фоне, необходимо добавить мод-ор <code>white</code></p>

{capture 'test_example_content'}
    <div style="background: #f44336; padding: 3px 5px; display: inline-block; border-radius: 3px;">
        {component 'icon' mods='white' icon='star'}
    </div>
{/capture}

{capture 'test_example_code'}
{ldelim}component 'icon' mods='white' icon='trash'{rdelim}
{/capture}

{test_example content=$smarty.capture.test_example_content code=$smarty.capture.test_example_code}


{test_heading text='Список иконок'}

<p>Полный список иконок можно посмотреть на странице шрифта <a href="http://fontawesome.io/icons/">FontAwesome</a></p>