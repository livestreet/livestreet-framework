{**
 * Уведомления
 *
 * @param string  $text
 * @param string  $url
 *}

{* Название компонента *}
{$component = 'ls-tags-item'}

{* Генерируем копии локальных переменных, *}
{* чтобы их можно было изменять в дочерних шаблонах *}
{foreach [ 'text', 'url', 'isFirst', 'isLast', 'mods', 'classes', 'attributes' ] as $param}
    {assign var="$param" value=$smarty.local.$param}
{/foreach}

{block 'tags_item_options'}{/block}

{* Уведомление *}
<li class="{$component} {cmods name=$component mods=$mods} {$classes}">
    <a href="{$url}" rel="tag">
        {$text|escape}
    </a>
</li>