{**
 * Тулбар
 *}

{extends 'component@button.button'}

{block 'button_options' append}
    {$classes = "$classes ls-toolbar-button"}
    {$mods = "$mods icon"}
{/block}