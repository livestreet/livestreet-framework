{**
 * Slider
 *
 * @param array $images
 * @param string $mods
 * @param string $classes
 * @param array  $attributes
 *}

{* Название компонента *}
{$component = 'slider'}

{* Генерируем копии локальных переменных, *}
{* чтобы их можно было изменять в дочерних шаблонах *}
{foreach [ 'images', 'mods', 'classes', 'attributes' ] as $param}
    {assign var="$param" value=$smarty.local.$param}
{/foreach}

{block 'slider_options'}{/block}

{* Jumbotron *}
{if $images}
    <div class="{$component} {cmods name=$component mods=$mods} {$classes}" {cattr list=$attributes}>
        {foreach $images as $item}
            <img src="{$item['src']}" {$item['attributes']}>
        {/foreach}
    </div>
{/if}