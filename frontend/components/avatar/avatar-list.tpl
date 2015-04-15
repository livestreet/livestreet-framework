{**
 * Список аваторов
 *
 * @param string|array $items
 * @param string $mods
 * @param string $classes
 * @param array  $attributes
 *}

{* Название компонента *}
{$component = 'avatar-list'}

{* Генерируем копии локальных переменных, *}
{* чтобы их можно было изменять в дочерних шаблонах *}
{foreach [ 'items', 'mods', 'classes', 'attributes' ] as $param}
    {assign var="$param" value=$smarty.local.$param}
{/foreach}

{$classes = "$classes clearfix"}

{block 'avatar_list_options'}{/block}

<div class="{$component} {cmods name=$component mods=$mods} {$classes}" {cattr list=$attributes}>
    {block 'avatar_list_inner'}
        {component 'avatar' template='loop' items=$items}
    {/block}
</div>