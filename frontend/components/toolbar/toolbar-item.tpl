{**
 * Тулбар
 *}

{extends 'component@button.group'}

{block 'button_group_options' append}
    {$classes = "$classes ls-toolbar-item"}
    {$mods = "$mods vertical"}
    {$groups = $items}
{/block}

{block 'button_group_button'}
    {component 'toolbar' template='button' params=array_merge( $buttonParams|default:[], $button )}
{/block}