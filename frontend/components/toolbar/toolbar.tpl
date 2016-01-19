{**
 * Тулбар
 *}

{extends 'component@button.toolbar'}

{block 'button_toolbar_options' append}
    {$classes = "$classes ls-toolbar"}
    {$mods = "$mods vertical"}
    {$groups = $items}
{/block}

{block 'button_toolbar_group'}
    {component 'toolbar' template='item' role='group' mods=$groupMod params=$group}
{/block}