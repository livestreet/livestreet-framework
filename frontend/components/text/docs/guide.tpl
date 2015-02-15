<p>Компонент для оформления текста.</p>

{test_heading text='Использование'}

{capture 'test_example_content'}
    {capture 'text'}
        <ul>
            <li>Очевидно</li>
            <li>что последнее</li>
            <li>векторное</li>
            <li>равенство</li>
        </ul>
        <br>
        <ol>
            <li>Очевидно</li>
            <li>что последнее</li>
            <li>векторное</li>
            <li>равенство</li>
        </ol>
        <br>
        <table width="300">
            <thead>
                <th>Name</th>
                <th>Price</th>
            </thead>
            <tbody>
                <tr><td>Apple</td><td>$10</td></tr>
                <tr><td>Orange</td><td>$7</td></tr>
                <tr><td>Banana</td><td>$5</td></tr>
            </tbody>
        </table>
    {/capture}

    {component 'text' text=$smarty.capture.text}
{/capture}

{capture 'test_example_code'}
{ldelim}component 'text' text='...'{rdelim}
{/capture}

{test_example content=$smarty.capture.test_example_content code=$smarty.capture.test_example_code}