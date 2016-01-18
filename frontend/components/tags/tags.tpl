{**
 * Список тегов
 *}

{$component = 'ls-tags'}

{component_define_params params=[ 'title', 'tags', 'mods', 'classes', 'attributes' ]}

{block 'tags_options'}{/block}

{if $tags}
    <ul class="{$component} {cmods name=$component mods=$mods} {$classes} ls-clearfix" {cattr list=$attributes}>
        {if $title}
            <li class="{$component}-item {$component}-title">
                {$title}
            </li>
        {/if}

        {block 'tags_list'}
            {foreach $tags as $tag}
                {component 'tags' template='item' text=$tag->getText() url=$tag->getUrl() isFirst=$tag@first}
            {/foreach}
        {/block}
    </ul>
{/if}