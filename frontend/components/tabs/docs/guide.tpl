<p>Табы.</p>

{test_heading text='Использование'}

<p>Для базового использования нужно указать массив с табами в параметре <code>tabs</code> и прописать класс к которому будет привязываться jquery-виджет <code>lsTabs</code>.</p>

<script>
    jQuery(function($) {
        $('.js-my-tabs').lsTabs();
    });
</script>

{capture 'test_example_content'}
    {component 'tabs' classes='js-my-tabs' tabs=[
        [ text => 'Tab 1', content => 'Tab 1. Lorem ipsum dolor sit amet, consectetur adipisicing elit. Quae, explicabo!', isActive => true ],
        [ text => 'Tab 2', content => 'Tab 2. Lorem ipsum dolor sit amet, consectetur adipisicing elit. Qui expedita, quibusdam voluptas quia numquam provident nobis rem quam hic eum.' ]
    ]}
{/capture}

{capture 'test_example_code'}
<script>
    jQuery(function($) {
        $('.js-my-tabs').lsTabs();
    });
</script>

{ldelim}component 'tabs' classes='js-my-tabs' tabs=[
    [ text => 'Tab 1', content => 'Lorem ipsum...' ],
    [ text => 'Tab 2', content => 'Lorem ipsum...' ]
]{rdelim}
{/capture}

{test_example content=$smarty.capture.test_example_content code=$smarty.capture.test_example_code}