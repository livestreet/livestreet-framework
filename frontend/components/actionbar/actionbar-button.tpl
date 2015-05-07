{**
 * Кнопка
 *}

{extends 'component@button.button'}

{block 'button_options' append}
    {$classes = "$classes ls-actionbar-button"}
{/block}