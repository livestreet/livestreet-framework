<p>Пагинация.</p>

{**
 * Использование
 *}
{test_heading text='Использование'}

<p>Для базового использования нужно указать общее кол-во страниц <code>total</code>, текущую страницу <code>current</code> и ссылку <code>url</code> с параметром <code>__page__</code>, который при выводе страниц будет заменен на номер страницы.</p>

{capture 'test_example_content'}
    {component 'pagination' total=10 current=3 url='http://example.com/content/page__page__'}
{/capture}

{capture 'test_example_code'}
{ldelim}component 'pagination'
    total=10
    current=3
    url='http://example.com/content/page__page__'{rdelim}
{/capture}

{test_example content=$smarty.capture.test_example_content code=$smarty.capture.test_example_code}


{**
 * Навигация
 *}
{test_heading text='Навигация'}

<p>Для того что бы показать мини-навигацию "пред. / след. страница", необходимо установить параметр <code>showPager</code> в <code>true</code></p>

{capture 'test_example_content'}
    {component 'pagination' total=10 current=1 showPager=true url='http://example.com/content/page__page__'}
{/capture}

{capture 'test_example_code'}
{ldelim}component 'pagination'
    total=10
    current=1
    url='http://example.com/content/page__page__'
    showPager=true{rdelim}
{/capture}

{test_example content=$smarty.capture.test_example_content code=$smarty.capture.test_example_code}


{**
 * Отступы
 *}
{test_heading text='Отступы'}

<p>Кол-во показываемых страниц справа и слева от активной страницы указываются в параметре <code>padding</code> (по-умолчанию 2).</p>

{capture 'test_example_content'}
    {component 'pagination' total=10 current=5 padding=1 url='http://example.com/content/page__page__'}
{/capture}

{capture 'test_example_code'}
{ldelim}component 'pagination'
    total=10
    current=4
    padding=1
    url='http://example.com/content/page__page__'{rdelim}
{/capture}

{test_example content=$smarty.capture.test_example_content code=$smarty.capture.test_example_code}


{**
 * Клавиатурная навигация
 *}
{test_heading text='Клавиатурная навигация'}

<p>jQuery-виджет <code>lsPagination</code> позволяет добавить клавиатурную навигацию по страницам. По-умолчанию используется комбинации клавиш <kbd>ctrl + &rarr;</kbd> и <kbd>ctrl + &larr;</kbd></p>

{capture 'test_code'}
<script>
    jQuery(function($) {
        $('.js-mypagination').lsPagination();
    });
</script>

{ldelim}component 'pagination' ... classes='js-mypagination'{rdelim}
{/capture}

{test_code code=$smarty.capture.test_code}


{**
 * Модификаторы
 *}
{test_heading text='Модификаторы'}

{capture 'test_example_content'}
    {component 'pagination' total=10 current=3 url='http://example.com/content/page__page__' mods='small'}
{/capture}

{capture 'test_example_code'}
{ldelim}component 'pagination'
    total=10
    current=3
    url='http://example.com/content/page__page__'
    mods='small'{rdelim}
{/capture}

{test_example content=$smarty.capture.test_example_content code=$smarty.capture.test_example_code}


{test_heading text='Ajax пагинация'}

<script>
    jQuery(function($) {
        $('.js-mypagination-ajax').lsPaginationAjax();
        //$('.js-mypagination-ajax').lsPaginationAjax('setTotalPages', 0);
    });
</script>

{component 'pagination' classes='js-mypagination-ajax' total=10 current=3 url='http://example.com/content/page__page__' mods='small'}