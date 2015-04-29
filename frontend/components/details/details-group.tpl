{**
 * Шаблон служит оберткой для списка сворачиваемых блоков <code>item</code>
 *
 * @param array  $items
 * @param string $mods
 * @param string $classes
 * @param array  $attributes
 *}

{$component = 'details-group'}

{* Генерируем копии локальных переменных, *}
{* чтобы их можно было изменять в дочерних шаблонах *}
{foreach [ 'items', 'mods', 'classes', 'attributes' ] as $param}
    {assign var="$param" value=$smarty.local.$param}
{/foreach}

{block 'details_group_options'}{/block}

{* Details *}
<div class="{$component} {cmods name=$component mods=$mods} {$classes}" {cattr list=$attributes}>
    {if is_array( $items )}
        {foreach $items as $item}
            {if is_array( $item )}
                {block 'details_group_item'}
                    {component 'details' classes='js-ls-details-group-item' params=$item}
                {/block}
            {else}
                {$item}
            {/if}
        {/foreach}
    {else}
        {$items}
    {/if}
</div>