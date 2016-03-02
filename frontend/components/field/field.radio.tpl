{**
 * Radio input
 *}

{extends 'component@field.field'}

{block 'field_options' prepend}
    {$mods = "$mods checkbox"}
    {$getValueFromForm = false}
{/block}

{block 'field_input'}
    {if $name && $form}
        {$checked = $value == {field_get_value form=$form name=$name}}
    {/if}

    <input type="radio" {field_input_attr_common} {if $checked}checked{/if} />
{/block}