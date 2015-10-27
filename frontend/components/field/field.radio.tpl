{**
 * 
 *}

{extends './field.tpl'}

{block 'field' prepend}
    {$mods = "$mods checkbox"}
{/block}

{block 'field_input'}
    <input type="radio" {field_input_attr_common} {if $checked}checked{else}{if {field_get_value form=$form name=$name} == 1}checked{/if}{/if} />
{/block}