{**
 * Тулбар
 *}

{extends 'component@button.button'}

{block 'button_options' append}
    {$classes = "$classes toolbar-button"}
    {$mods = "$mods icon"}
{/block}