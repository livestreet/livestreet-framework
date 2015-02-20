{**
 * Accordion
 *
 * @param array  $items
 * @param string $mods
 * @param string $classes
 * @param array  $attributes
 *}

{$component = 'accordion'}

{* Генерируем копии локальных переменных, *}
{* чтобы их можно было изменять в дочерних шаблонах *}
{foreach [ 'items', 'mods', 'classes', 'attributes' ] as $param}
    {assign var="$param" value=$smarty.local.$param}
{/foreach}

{block 'accordion_options'}{/block}

{* Accordion *}
<div class="{$component} {cmods name=$component mods=$mods} {$classes}" {cattr list=$attributes}>
    {if is_array( $items )}
        {foreach $items as $item}
            {if is_array( $item )}
                {block 'accordion_item'}
                    {component 'accordion' template='item' params=$item}
                {/block}
            {else}
                {$item}
            {/if}
        {/foreach}
    {else}
        {$items}
    {/if}
</div>