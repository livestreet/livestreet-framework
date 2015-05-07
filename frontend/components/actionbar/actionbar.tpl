{**
 * Экшнбар
 *
 * @param array  $items Массив с кнопками
 * @param string $mods
 * @param string $classes
 * @param array  $attributes
 *}

{extends 'Component@button.toolbar'}

{block 'button_toolbar_options' append}
    {$groups = $smarty.local.items}
    {$classes = "$classes ls-actionbar"}
{/block}

{block 'button_toolbar_group'}
    {component 'actionbar' template='group' params=$group}
{/block}