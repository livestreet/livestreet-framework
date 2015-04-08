{**
 * Сообщения
 *}

{test_heading text='Сообщения'}

<p>Параметр <code>text</code></p>

{capture 'test_example_content'}
    {component 'alert' text='Lorem ipsum dolor sit amet, consectetur adipisicing elit. Vel, nisi.'}
{/capture}

{capture 'test_example_code'}
{ldelim}component 'alert' text='Lorem ipsum dolor sit amet ...'{rdelim}
{/capture}

{test_example content=$smarty.capture.test_example_content code=$smarty.capture.test_example_code}


{**
 * Закрываемое сообщение
 *}
{test_heading text='Закрываемое сообщение'}

<p>Параметр <code>close</code></p>

{capture 'test_example_content'}
    <script>
        jQuery(function($) {
            $('.js-my-alert').lsAlert();
        });
    </script>

    {component 'alert' close=true classes='js-my-alert' text='Lorem ipsum dolor sit amet, consectetur adipisicing elit. Vel, nisi.'}
{/capture}

{capture 'test_example_code'}
<script>
    jQuery(function($) {
        $('.js-my-alert').lsAlert();
    });
</script>

{ldelim}component 'alert'
    classes='js-my-alert'
    dismissible=true
    text='Lorem ipsum dolor sit amet ...'{rdelim}
{/capture}

{test_example content=$smarty.capture.test_example_content code=$smarty.capture.test_example_code}


{**
 * Сообщение с заголовком
 *}
{test_heading text='Сообщение с заголовком'}

<p>Параметр <code>title</code></p>

{capture 'test_example_content'}
    {component 'alert' title='Внимание' text='Lorem ipsum dolor sit amet, consectetur adipisicing elit. Vel, nisi.'}
{/capture}

{capture 'test_example_code'}
{ldelim}component 'alert' title='Внимание' text='Lorem ipsum dolor sit amet ...'{rdelim}
{/capture}

{test_example content=$smarty.capture.test_example_content code=$smarty.capture.test_example_code}


{**
 * Цвета
 *}
{test_heading text='Цвета'}

<p>Модификаторы <code>success</code> <code>info</code> <code>error</code> <code>empty</code></p>

{capture 'test_example_content'}
    {component 'alert' text='Сообщение без модификатора'}
    {component 'alert' text='Success' mods='success'}
    {component 'alert' text='Info' mods='info'}
    {component 'alert' text='Error' mods='error'}
{/capture}

{capture 'test_example_code'}
{ldelim}component 'alert' text='Default'{rdelim}
{ldelim}component 'alert' text='Success' mods='success'{rdelim}
{ldelim}component 'alert' text='Info' mods='info'{rdelim}
{ldelim}component 'alert' text='Error' mods='error'{rdelim}
{/capture}

{test_example content=$smarty.capture.test_example_content code=$smarty.capture.test_example_code}