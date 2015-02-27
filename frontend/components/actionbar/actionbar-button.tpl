{**
 * Кнопка
 *}

{extends 'component@button.button'}

{block 'button_options' append}
    {$classes = "$classes actionbar-button"}
{/block}