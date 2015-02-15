<p>Подсветка кода.</p>

{test_heading text='Использование'}

<p>Для подсветки используется jquery-виджет <code>lsHighlighter</code>.</p>

{capture 'test_example_content'}
<pre><code>if ( ! this.elements.country.val() ) {
    this.elements.region.empty().hide().change();
    return;
}</code></pre>
{/capture}

{capture 'test_example_code'}
<script>
    $( 'pre code' ).lsHighlighter();
</script>

<pre><code>if ( ! this ... return; }</code></pre>
{/capture}

{test_example content=$smarty.capture.test_example_content code=$smarty.capture.test_example_code}