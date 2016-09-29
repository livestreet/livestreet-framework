{**
 * Тулбар
 *}

{$component = 'ls-toolbar'}
{component_define_params params=[ 'items', 'mods', 'classes', 'attributes' ]}

<div class="{$component} {cmods name=$component mods=$mods} {$classes}" {cattr list=$attributes}>
    {if is_array($items)}
        {foreach $items as $item}
            {component 'toolbar.item' params=$item}
        {/foreach}
    {else}
        {$items}
    {/if}
</div>