<style>
    .modal-visible {
        display: block;
        position: static;
    }
</style>

<script>
    domReady(function() {
        $('.js-ls-modal-tabs').lsTabs();
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

<p>Теперь можно добавить например кнопку, которая будет открывать наше модальное окно, для этого нужно использовать атрибут <code>data-lsmodaltoggle-modal</code> в котором указать <code>id</code> окна, в нашем случае это <code>my_modal</code></p>

{capture 'test_example_content'}
    <script>
        domReady(function() {
            $('.js-mymodal').lsModal();
        });
    </script>

    {component 'button' type='button' text='Показать окно' classes='js-modal-toggle-default' attributes=[ 'data-lsmodaltoggle-modal' => 'my_modal' ]}

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

        // Иниц-ия кнопки которая показывает окно
        $('.js-my-modal-toggle').lsModalToggle();
    });
</script>

{ldelim}component 'button'
    type='button'
    text='Показать окно'
    classes='js-my-modal-toggle'
    attributes=[ 'data-lsmodaltoggle-modal' => 'my_modal' ]{rdelim}

{ldelim}component 'modal'
    classes='js-mymodal'
    id='my_modal'
    title='Modal'
    content='Lorem ipsum...'{rdelim}
{/capture}

{test_example content=$smarty.capture.test_example_content code=$smarty.capture.test_example_code}


{**
 * Вложенные модальные окна
 *}
{test_heading text='Вложенные модальные окна'}

<p>TODO</p>

{capture 'test_example_content'}
    {component 'button' type='button' text='Показать окно' classes='js-modal-toggle-default' attributes=[ 'data-lsmodaltoggle-modal' => 'my-modal' ]}

    {capture 'modal_content'}
        {component 'button' type='button' text='Показать окно' classes='js-modal-toggle-default' attributes=[ 'data-lsmodaltoggle-modal' => 'my-modal-nested' ]}
    {/capture}

    {component 'modal'
        classes='js-mymodal'
        id='my-modal'
        title='Modal'
        content=$smarty.capture.modal_content}

    {component 'modal'
        classes='js-mymodal'
        id='my-modal-nested'
        title='Modal'
        content='Lorem ipsum dolor sit amet, consectetur adipisicing elit. Vitae, vero, distinctio. Sit veniam cupiditate sunt, reprehenderit officiis, voluptates nesciunt odio?'}
{/capture}

{test_example content=$smarty.capture.test_example_content code='...'}


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
            'tabs' => [
                [ 'text' => 'Tab 1', 'content' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Odio, nesciunt.' ],
                [ 'text' => 'Tab 2', 'content' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Odio, nesciunt. Lorem ipsum dolor sit amet, consectetur adipisicing elit. Odio, nesciunt.' ]
            ]
        ]}
{/capture}

{capture 'test_example_code'}
{ldelim}component 'modal'
    title='Modal'
    classes='js-mymodal'
    tabs=[
        'tabs' => [
            [ 'text' => 'Tab 1', 'content' => 'Lorem ipsum...' ],
            [ 'text' => 'Tab 2', 'content' => 'Lorem ipsum...' ]
        ]
    ]{rdelim}
{/capture}

{test_example content=$smarty.capture.test_example_content code=$smarty.capture.test_example_code}