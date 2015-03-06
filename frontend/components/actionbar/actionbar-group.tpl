{**
 * Группа кнопок
 *}

{extends 'component@button.group'}

{block 'button_group_options' append}
    {$classes = "$classes actionbar-group"}
{/block}

{block 'button_group_button'}
    {component 'actionbar' template='button' params=array_merge( $buttonParams|default:[], $button )}
{/block}