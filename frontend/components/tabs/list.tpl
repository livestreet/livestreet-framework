{$component = 'ls-tab-list'}
{component_define_params params=[ 'activeTab', 'tabs', 'mods', 'classes', 'attributes' ]}

<ul class="{$component} {cmods name=$component mods=$mods} {$classes} ls-clearfix" {cattr list=$attributes} data-tab-list>
    {foreach $tabs as $tab}
        {if $tab[ 'is_enabled' ]|default:true}
            <li class="ls-tab {$tab[ 'classes' ]}
                {if $tab['isActive'] || $activeTab == $tab['name']}active{/if}"
                data-tab
                data-lstab-options='{
                    "target": "{$tab['uid']}",
                    "urls": {
                        "load": "{$tab[ 'url' ]}"
                    }
                }'
                {cattr list=$tab[ 'attributes' ]}>

                {if $tab['url']}
                    <a href="{$tab['url']}" class="ls-tab-inner">{$tab[ 'text' ]}</a>
                {else}
                    <span class="ls-tab-inner">{$tab[ 'text' ]}</span>
                {/if}
            </li>
        {/if}
    {/foreach}
</ul>