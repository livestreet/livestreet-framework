<p>Всплывающие подсказки.</p>

{test_heading text='Использование'}

{capture 'test_example_content'}
    <script>
        domReady(function() {
            $('.js-my-tooltip').lsTooltip();
        })
    </script>

    {component 'button' text='Tooltip' classes='js-my-tooltip' attributes=[ title => 'My text' ]}
{/capture}

{capture 'test_example_code'}
...
{/capture}

{test_example content=$smarty.capture.test_example_content code=$smarty.capture.test_example_code}