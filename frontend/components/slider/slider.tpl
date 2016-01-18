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

{component_define_params params=[ 'images', 'mods', 'classes', 'attributes' ]}

{block 'slider_options'}{/block}

{* Jumbotron *}
{if $images}
    <div class="{$component} {cmods name=$component mods=$mods} {$classes}" {cattr list=$attributes}>
        {foreach $images as $item}
            <img src="{$item['src']}" {$item['attributes']}>
        {/foreach}
    </div>
{/if}