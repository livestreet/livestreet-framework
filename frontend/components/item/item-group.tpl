{**
 * Группированный список
 *
 * @param string $items
 * @param string $mods
 * @param string $classes
 * @param array  $attributes
 *}

{* Название компонента *}
{$component = 'item-group'}

{* Генерируем копии локальных переменных, *}
{* чтобы их можно было изменять в дочерних шаблонах *}
{foreach [ 'items', 'mods', 'classes', 'attributes' ] as $param}
    {assign var="$param" value=$smarty.local.$param}
{/foreach}

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