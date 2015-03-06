<style>
    .toolbar.my-toolbar {
        position: static;
    }
</style>

<p>Плавающий тулбар.</p>

{test_heading text='Использование'}

<p>У компонента необходимо указать массив с группами кнопок <code>items</code> и класс к которому будет привязываться jquery-виджет.</p>

<p>В виджете необходимо указать селектор элемента <code>target</code> относительно которого будет позиционироваться тулбар и опциональные параметры <code>offsetX</code> <code>offsetY</code> в которых указывается смещение тулбара по осям в пикселях.</p>

{capture 'test_example_content'}
    {component 'toolbar' classes='my-toolbar' items=[
        [ 'buttons' => [[ 'icon' => 'cog' ]] ],
        [
            'buttons' => [
                [ 'icon' => 'arrow-up' ],
                [ 'icon' => 'arrow-down' ]
            ]
        ]
    ]}
{/capture}

{capture 'test_example_code'}
<script>
    jQuery(function($) {
        $('.js-toolbar-default').lsToolbar({
            target: '.container',
            offsetX: 10
        });
    });
</script>

{ldelim}component 'toolbar' classes='my-toolbar' items=[
    [
        'buttons' => [
            [ 'icon' => 'cog', 'url' => 'aaasdf' ]
        ]
    ],
    [
        'buttons' => [
            [ 'icon' => 'arrow-up' ],
            [ 'icon' => 'arrow-down' ]
        ]
    ]
]{rdelim}
{/capture}

{test_example content=$smarty.capture.test_example_content code=$smarty.capture.test_example_code}