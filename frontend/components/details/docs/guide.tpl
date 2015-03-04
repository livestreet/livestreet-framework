<p>Сворачиваемые блоки.</p>

{test_heading text='Использование'}

{capture 'test_example_content'}
    <script>
        jQuery(function($) {
            $('.js-my-details').lsDetails();
        });
    </script>

    {component 'details'
        classes='js-my-details'
        title='Lorem ipsum dolor sit amet'
        content='Lorem ipsum dolor sit amet, consectetur adipisicing elit. Autem consequatur officiis possimus explicabo quasi iusto aut nihil fuga saepe labore.'}
{/capture}

{capture 'test_example_code'}
<script>
    jQuery(function($) {
        $('.js-my-details').lsDetails();
    });
</script>

{ldelim}component 'details'
    classes = 'js-my-details'
    title   = 'Lorem ipsum...'
    content = 'Lorem ipsum...'{rdelim}
{/capture}

{test_example content=$smarty.capture.test_example_content code=$smarty.capture.test_example_code}


{**
 * Группировка
 *}
{test_heading text='Группировка'}

<p>Группировка позволяет при открытии одного блока сворачивать все остальные в группе.</p>

{capture 'test_example_content'}
    <script>
        jQuery(function($) {
            $('.js-my-details-group').lsDetailsGroup();
        });
    </script>

    {component 'details' template='group' classes='js-my-details-group' items=[
        [ title => 'Lorem ipsum dolor sit amet', content => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Autem consequatur officiis possimus explicabo quasi iusto aut nihil fuga saepe labore.' ],
        [ title => 'Lorem ipsum dolor', content => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Autem consequatur officiis possimus explicabo.' ],
        [ title => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Sequi, reprehenderit.', content => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Autem consequatur officiis possimus explicabo.' ],
        [ title => 'Lorem ipsum dolor sit', content => 'Lorem ipsum dolor sit amet. Autem consequatur officiis possimus explicabo quasi iusto aut nihil fuga saepe labore.' ]
    ]}
{/capture}

{capture 'test_example_code'}
<script>
    jQuery(function($) {
        $('.js-my-details-group').lsDetailsGroup();
    });
</script>

{ldelim}component 'details' template='group' classes='js-my-details-group' items=[
    [ title => 'Lorem ipsum...', content => 'Lorem ipsum...' ],
    [ title => 'Lorem ipsum...', content => 'Lorem ipsum...' ],
    [ title => 'Lorem ipsum...', content => 'Lorem ipsum...' ]
]{rdelim}
{/capture}

{test_example content=$smarty.capture.test_example_content code=$smarty.capture.test_example_code}