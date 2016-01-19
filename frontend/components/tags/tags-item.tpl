{**
 * Уведомления
 *
 * @param string  $text
 * @param string  $url
 *}

{* Название компонента *}
{$component = 'ls-tags-item'}
{component_define_params params=[ 'text', 'url', 'isFirst', 'isLast', 'mods', 'classes', 'attributes' ]}

{block 'tags_item_options'}{/block}

{* Уведомление *}
<li class="{$component} {cmods name=$component mods=$mods} {$classes}">
    <a href="{$url}" rel="tag">
        {$text|escape}
    </a>
</li>