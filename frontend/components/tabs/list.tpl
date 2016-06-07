{$component = 'ls-tab-list'}
{component_define_params params=[ 'activeTab', 'tabs', 'mods', 'classes', 'attributes' ]}

<ul class="{$component} {cmods name=$component mods=$mods} {$classes} ls-clearfix" {cattr list=$attributes} data-tab-list>
    {if ! $activeTab && $tabs}
        {$tabs[0]['isActive'] = true}
    {/if}

    {foreach $tabs as $tab}
        {if $tab['is_enabled']|default:true}
            {component 'tabs.tab' isActive=($tab['isActive'] || ($activeTab && $tab['name'] == $activeTab)) params=$tab}
        {/if}
    {/foreach}
</ul>