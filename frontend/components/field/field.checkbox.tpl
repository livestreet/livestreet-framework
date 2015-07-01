{**
 * Чекбокс
 *}

{extends './field.tpl'}

{block 'field' prepend}
	{$mods = "$mods checkbox"}
	{$value = ( $value ) ? $value : '1'}
{/block}

{block 'field_input'}
	<input type="checkbox" {field_input_attr_common} {if $smarty.local.checked}checked{else}{if {field_get_value form=$form name=$name} == 1}checked{/if}{/if} />
{/block}