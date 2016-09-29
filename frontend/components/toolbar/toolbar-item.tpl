{**
 * Тулбар
 *}

{$component = 'ls-toolbar-item'}
{component_define_params params=[ 'html', 'url', 'icon', 'mods', 'classes', 'attributes' ]}

{if $url}<a href="{$url}"{else}<div{/if} class="{$component} {cmods name=$component mods=$mods} {$classes}" {cattr list=$attributes}>
    {if $html}
        {$html}
    {else}
        {if $icon}
            {component 'icon' icon=$icon}
        {/if}
    {/if}
{if $url}</a>{else}</div>{/if}