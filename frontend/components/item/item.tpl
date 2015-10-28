{**
 * Item
 *}

{$component = 'ls-item'}

{* Генерируем копии локальных переменных, *}
{* чтобы их можно было изменять в дочерних шаблонах *}
{foreach [ 'image', 'content', 'desc', 'title', 'titleUrl', 'element', 'mods', 'classes', 'attributes' ] as $param}
    {assign var="$param" value=$smarty.local.$param}
{/foreach}

{block 'item_options'}{/block}

{* Дефолтные значения *}
{$classes = "$classes ls-clearfix"}
{$element = $element|default:'div'}

{if $image}
    {$mods = "$mods has-image"}
{/if}

{* Item *}
<{$element} class="{$component} {$classes} {cmods name=$component mods=$mods}" {cattr list=$attributes}>
    {if $image}
        <div class="{$component}-left">
            <a href="{$image[ 'url' ]}">
                <img src="{$image[ 'path' ]}" alt="{$image[ 'alt' ]}" title="{$image[ 'title' ]}" class="{$component}-image {$image[ 'classes' ]}">
            </a>
        </div>
    {/if}

    <div class="{$component}-body js-{$component}-body">
        {if $title}
            {if $titleUrl}
                <h3 class="{$component}-title"><a href="{$titleUrl}">{$title}</a></h3>
            {else}
                <h3 class="{$component}-title">{$title}</h3>
            {/if}
        {/if}

        {if $desc}
            <div class="{$component}-description">
                {$desc}
            </div>
        {/if}

        {$content}
    </div>
</{$element}>