{**
 * Облако тегов
 *
 * @param array  $tags   Массив с тегами
 * @param string $active Текст активного тега
 *}

{$component = 'ls-tag-cloud'}
{component_define_params params=[ 'tags', 'acitve' ]}

{if $tags}
    <ul class="{$component} ls-word-wrap">
        {foreach $tags as $tag}
            <li class="{$component}-item {if $tag->getText() && $active == $tag->getText()}active{/if}">
                <a class="ls-tag-size-{$tag->getSize()}" href="{$tag->getUrl()}" title="{$tag->getCount()}">{$tag->getText()|escape}</a>
            </li>
        {/foreach}
    </ul>
{else}
    {component 'blankslate' text=$aLang.common.empty}
{/if}