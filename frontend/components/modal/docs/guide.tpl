<style>
    .modal-visible {
        display: block;
        position: static;
    }
</style>

<script>
    jQuery(function ($) {
        $('.js-tabs-default').lsTabs();
    });
</script>

<p>Модальные окна.</p>


{**
 * Использование
 *}
{test_heading text='Использование'}

<p>Для базового использования, необходимо сначало добавить в код само модальное окно. Обязательные параметры <code>title</code> - заголовок окна и <code>content</code> - содержимое, так же указываем класс к которому будет привязываться jquery-виджет и параметр <code>id</code> который будет использоваться для показывания окна.</p>

{component 'alert' text='В примерах ниже, модальные окна специально сделаны видимыми, при обычном подключении они будут скрыты.'}

{capture 'test_example_content'}
    {component 'modal'
        classes='modal-visible'
        title='Modal'
        content='Lorem ipsum dolor sit amet, consectetur adipisicing elit. Vitae, vero, distinctio. Sit veniam cupiditate sunt, reprehenderit officiis, voluptates nesciunt odio?'}
{/capture}

{capture 'test_example_code'}
{ldelim}component 'modal'
    title='Modal'
    content='Lorem ipsum...'
    id='my_modal'
    classes='js-mymodal'{rdelim}
{/capture}

{test_example content=$smarty.capture.test_example_content code=$smarty.capture.test_example_code}

<p>После этого иниц-ем jquery-виджет <code>lsModal</code>:</p>

{capture 'test_code'}
jQuery(function ($) {
    $('.js-mymodal').lsModal();
});
{/capture}

{test_code code=$smarty.capture.test_code}

<p>Теперь можно добавить например кнопку, которая будет открывать наше модальное окно, для этого нужно использовать атрибут <code>data-modal-target</code> в котором указать <code>id</code> окна, в нашем случае это <code>my_modal</code></p>

{capture 'test_example_content'}
    <script>
        jQuery(function ($) {
            $('.js-mymodal').lsModal();
        });
    </script>

    {component 'button' type='button' text='Показать окно' attributes=[ 'data-modal-target' => 'my_modal' ]}

    {component 'modal'
        classes='js-mymodal'
        id='my_modal'
        title='Modal'
        content='Lorem ipsum dolor sit amet, consectetur adipisicing elit. Vitae, vero, distinctio. Sit veniam cupiditate sunt, reprehenderit officiis, voluptates nesciunt odio?'}
{/capture}

{capture 'test_example_code'}
<script>
    jQuery(function ($) {
        $('.js-mymodal').lsModal();
    });
</script>

{ldelim}component 'button'
    type='button'
    text='Показать окно'
    attributes=[ 'data-modal-target' => 'my_modal' ]{rdelim}

{ldelim}component 'modal'
    classes='js-mymodal'
    id='my_modal'
    title='Modal'
    content='Lorem ipsum...'{rdelim}
{/capture}

{test_example content=$smarty.capture.test_example_content code=$smarty.capture.test_example_code}


{**
 * Табы
 *}
{test_heading text='Табы'}

<p>TODO</p>

{capture 'test_example_content'}
    {component 'modal'
        classes='modal-visible'
        title='Modal'
        tabs=[
            'classes' => 'js-tabs-default',
            'tabs' => [
                [ 'text' => 'Tab 1', 'content' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Odio, nesciunt.' ],
                [ 'text' => 'Tab 2', 'content' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Odio, nesciunt. Lorem ipsum dolor sit amet, consectetur adipisicing elit. Odio, nesciunt.' ]
            ]
        ]}
{/capture}

{capture 'test_example_code'}
<script>
    jQuery(function($) {
        $('.js-tabs-default').lsTabs();
    });
</script>

{ldelim}component 'modal'
    title='Modal'
    tabs=[
        'classes' => 'js-tabs-default',
        'tabs' => [
            [ 'text' => 'Tab 1', 'content' => 'Lorem ipsum...' ],
            [ 'text' => 'Tab 2', 'content' => 'Lorem ipsum...' ]
        ]
    ]{rdelim}
{/capture}

{test_example content=$smarty.capture.test_example_content code=$smarty.capture.test_example_code}