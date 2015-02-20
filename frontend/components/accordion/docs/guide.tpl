<p>Сворачиваемые блоки.</p>

{test_heading text='Использование'}

{capture 'test_example_content'}
    <script>
        jQuery(function($) {
            $('.js-my-accordion').lsAccordion();
        });
    </script>

    {component 'accordion' classes='js-my-accordion' items=[
        [ title => 'Lorem ipsum dolor sit amet', content => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Autem consequatur officiis possimus explicabo quasi iusto aut nihil fuga saepe labore.' ],
        [ title => 'Lorem ipsum dolor', content => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Autem consequatur officiis possimus explicabo.' ],
        [ title => 'Lorem ipsum dolor sit', content => 'Lorem ipsum dolor sit amet. Autem consequatur officiis possimus explicabo quasi iusto aut nihil fuga saepe labore.' ]
    ]}
{/capture}

{capture 'test_example_code'}
<script>
    jQuery(function($) {
        $('.js-my-accordion').lsAccordion();
    });
</script>

{ldelim}component 'accordion' classes='js-my-accordion' items=[
    [ title => 'Lorem ipsum...', content => 'Lorem ipsum...' ],
    [ title => 'Lorem ipsum...', content => 'Lorem ipsum...' ],
    [ title => 'Lorem ipsum...', content => 'Lorem ipsum...' ]
]{rdelim}
{/capture}

{test_example content=$smarty.capture.test_example_content code=$smarty.capture.test_example_code}