<p>E-mail отсылаемый пользователям.</p>

{**
 * Использование
 *}
{test_heading text='Использование'}

<p>...</p>

{capture 'test_example_content'}
    {component 'email' title='Lorem ipsum dolor sit amet.' content='Lorem ipsum dolor sit amet, consectetur adipisicing elit. Itaque incidunt ex voluptatum harum voluptate rerum maxime beatae. Impedit, cumque eos.'}
{/capture}

{capture 'test_example_code'}
...
{/capture}

{test_example content=$smarty.capture.test_example_content code=$smarty.capture.test_example_code}