{**
 * Текстовое поле
 *}

{extends 'component@field.field'}

{block 'field_options' append}
    {component_define_params params=[ 'type' ]}
{/block}

{block 'field_input'}
    <input type="{$type|default:'text'}" {field_input_attr_common} />
{/block}