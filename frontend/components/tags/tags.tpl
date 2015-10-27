{**
 * Список тегов
 *}

{$component = 'ls-tags'}

{* Генерируем копии локальных переменных, *}
{* чтобы их можно было изменять в дочерних шаблонах *}
{foreach [ 'title', 'tags', 'mods', 'classes', 'attributes' ] as $param}
    {assign var="$param" value=$smarty.local.$param}
{/foreach}

{block 'tags_options'}{/block}

{if $tags}
    <ul class="{$component} {cmods name=$component mods=$mods} {$classes} clearfix" {cattr list=$attributes}>
        {if $title}
            <li class="{$component}-item {$component}-title">
                {$title}
            </li>
        {/if}

        {block 'tags_list'}
            {foreach $tags as $tag}
                {component 'tags' template='item' text=$tag url="{router page='tag'}{$tag|escape:'url'}/" isFirst=$tag@first}
            {/foreach}
        {/block}
    </ul>
{/if}