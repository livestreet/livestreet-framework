{**
 * Группированный список
 *
 * @param string $items
 * @param string $mods
 * @param string $classes
 * @param array  $attributes
 *}

{* Название компонента *}
{$component = 'ls-item-group'}

{component_define_params params=[ 'items', 'mods', 'classes', 'attributes' ]}

{block 'list_group_options'}{/block}

{* Список *}
<ul class="{$component} {cmods name=$component mods=$mods} {$classes}" {cattr list=$attributes}>
    {if is_array( $items )}
        {foreach $items as $item}
            {component 'item' element='li' params=$item}
        {/foreach}
    {else}
        {$items}
    {/if}
</ul>