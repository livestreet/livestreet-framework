{**
 * Использование
 *}
{test_heading text='Использование'}

{capture 'test_example_content'}
    {component 'block'
        title='Block'
        content='Lorem ipsum dolor sit amet, consectetur adipisicing elit. Esse, id.'}
{/capture}

{capture 'test_example_code'}
{ldelim}component 'block' title='Block' content='Lorem ipsum'{rdelim}
{/capture}

{test_example content=$smarty.capture.test_example_content code=$smarty.capture.test_example_code}


{**
 * Цвета
 *}
{test_heading text='Цвета'}

<p>Модификаторы <code>primary</code> <code>success</code> <code>info</code> <code>warning</code> <code>danger</code></p>

{capture 'test_example_content'}
    {component 'block' mods='primary' title='Block' content='Lorem ipsum dolor sit amet, consectetur adipisicing elit. Esse, id.'}
    {component 'block' mods='success' title='Block' content='Lorem ipsum dolor sit amet, consectetur adipisicing elit. Esse, id.'}
    {component 'block' mods='info' title='Block' content='Lorem ipsum dolor sit amet, consectetur adipisicing elit. Esse, id.'}
    {component 'block' mods='warning' title='Block' content='Lorem ipsum dolor sit amet, consectetur adipisicing elit. Esse, id.'}
    {component 'block' mods='danger' title='Block' content='Lorem ipsum dolor sit amet, consectetur adipisicing elit. Esse, id.'}
{/capture}

{capture 'test_example_code'}
...
{/capture}

{test_example content=$smarty.capture.test_example_content code=$smarty.capture.test_example_code}


{**
 * Списки
 *}
{test_heading text='Списки'}

<p>TODO</p>

{capture 'test_example_content'}
    {component 'block'
        mods='primary'
        title='Item group'
        list=[ 'items' => [
            [ 'desc' => 'Lorem ipsum dolor sit amet.' ],
            [ 'desc' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Necessitatibus, eligendi!' ],
            [ 'desc' => 'Lorem ipsum dolor sit amet, consectetur adipisicing.' ]
        ]]}

    {component 'block'
        mods='primary'
        title='Item group + content'
        content='Lorem ipsum dolor sit amet, consectetur adipisicing elit. Esse, id. Lorem ipsum dolor sit amet, consectetur adipisicing elit. Esse, id.'
        list=[ 'items' => [
            [ 'desc' => 'Lorem ipsum dolor sit amet.' ],
            [ 'desc' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Necessitatibus, eligendi!' ],
            [ 'desc' => 'Lorem ipsum dolor sit amet, consectetur adipisicing.' ]
        ]]}

    {component 'block'
        mods='primary'
        title='Item group + footer'
        footer='Footer'
        list=[ 'items' => [
            [ 'desc' => 'Lorem ipsum dolor sit amet.' ],
            [ 'desc' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Necessitatibus, eligendi!' ],
            [ 'desc' => 'Lorem ipsum dolor sit amet, consectetur adipisicing.' ]
        ]]}
{/capture}

{capture 'test_example_code'}
TODO
{/capture}

{test_example content=$smarty.capture.test_example_content code=$smarty.capture.test_example_code}


{**
 * Табы
 *}
{test_heading text='Табы'}

<p>TODO</p>

{capture 'test_example_content'}
    <script>
        jQuery(function($) {
            $('.js-myblock').lsBlock();
        });
    </script>

    {component 'block'
        classes='js-myblock'
        title='Tabs'
        tabs=[
            'classes' => 'js-tabs-block',
            'tabs' => [
                [ 'text' => 'Tab 1', 'content' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Odio, nesciunt.' ],
                [ 'text' => 'Tab 2', 'content' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Odio, nesciunt. Lorem ipsum dolor sit amet, consectetur adipisicing elit. Odio, nesciunt.' ]
            ]
        ]}
{/capture}

{capture 'test_example_code'}
<script>
    jQuery(function($) {
        $('.js-myblock').lsBlock();
    });
</script>

{ldelim}component 'block'
    classes='js-myblock'
    title='Tabs'
    tabs=[
        'classes' => 'js-tabs-block',
        'tabs' => [
            [ 'text' => 'Tab 1', 'content' => 'Lorem ipsum...' ],
            [ 'text' => 'Tab 2', 'content' => 'Lorem ipsum...' ]
        ]
    ]{rdelim}
{/capture}

{test_example content=$smarty.capture.test_example_content code=$smarty.capture.test_example_code}