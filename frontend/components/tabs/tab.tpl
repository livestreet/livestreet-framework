{$component = 'ls-tab'}
{component_define_params params=[ 'name', 'isActive', 'mods', 'classes', 'attributes' ]}

<li class="{$component} {cmods name=$component mods=$mods} {$classes} {if $isActive}active{/if}" {cattr list=$attributes}
    data-tab
    data-lstab-options='{
        "target": "{$uid}",
        "urls": {
            "load": "{$url}"
        }
    }'>

    {if $url}
        <a href="{$url}" class="ls-tab-inner">{$text}</a>
    {else}
        <span class="ls-tab-inner">{$text}</span>
    {/if}
</li>