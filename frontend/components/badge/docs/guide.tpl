<p>Счетчик. Используется для отображения кол-ва элементов, например в навигации или списках.</p>

{test_heading text='Использование'}

<p>В параметре <code>value</code> необходимо указать значение для отображения в счетчике.</p>

{capture 'test_example_content'}
    {component 'badge' value=199}
    {component 'badge' value='+30'}
{/capture}

{capture 'test_example_code'}
{ldelim}component 'badge' value=199{rdelim}
{ldelim}component 'badge' value='+30'{rdelim}
{/capture}

{test_example content=$smarty.capture.test_example_content code=$smarty.capture.test_example_code}