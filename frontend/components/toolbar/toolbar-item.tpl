{**
 * Тулбар
 *}

{extends 'component@button.group'}

{block 'button_group_options' append}
    {$classes = "$classes toolbar-item"}
    {$mods = "$mods vertical"}
    {$groups = $smarty.local.items}
{/block}

{block 'button_group_button'}
    {component 'toolbar' template='button' params=array_merge( $buttonParams|default:[], $button )}
{/block}