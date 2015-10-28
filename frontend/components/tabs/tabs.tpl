{**
 * Tabs
 *
 * @param array $tabs Табы. Структура: [ 'text', 'content' ]
 *}

{$component = 'ls-tabs'}

{$tabs = $smarty.local.tabs}

<div class="{$component} {$smarty.local.classes} {cmods name=$component mods=$smarty.local.mods}">
    {* Табы *}
    <ul class="ls-tab-list ls-clearfix" data-tab-list>
        {foreach $tabs as $tab}
            {* Уникальный ID для привязки таба к его содержимому *}
            {$uid = "tab{rand( 0, 10e10 )}"}
            {$tabs[ $tab@index ][ 'uid' ] = $uid}

            {if $tab[ 'is_enabled' ]|default:true}
                <li class="ls-tab {$tab[ 'classes' ]}
                    {if $tab@first}active{/if}"
                    data-tab
                    data-lstab-options='{
                        "target": "{$uid}",
                        "urls": {
                            "load": "{$tab[ 'url' ]}"
                        }
                    }'
                    {$tab[ 'attributes' ]}>

                    {$tab[ 'text' ]}
                </li>
            {/if}
        {/foreach}
    </ul>

    {* Содержимое табов *}
    <div class="ls-tabs-panes" data-tab-panes>
        {foreach $tabs as $tab}
            {if $tab[ 'is_enabled' ]|default:true}
                <div class="ls-tab-pane" {if $tab@first}style="display: block"{/if} data-tab-pane id="{$tab[ 'uid' ]}">
                    {if $tab[ 'content' ]}
                        <div class="ls-tab-pane-content">
                            {$tab[ 'content' ]}
                        </div>
                    {/if}

                    {* Item group *}
                    {if is_array( $tab[ 'list' ] )}
                        {component 'item' template='group' params=$tab[ 'list' ]}
                    {elseif $tab[ 'list' ]}
                        {$tab[ 'list' ]}
                    {/if}

                    {$tab[ 'body' ]}
                </div>
            {/if}
        {/foreach}
    </div>
</div>