{**
 * Числовое поле
 *}

{extends 'component@field.field'}

{block 'field_options' append}
    {component_define_params params=[ 'type', 'min', 'max', 'step' ]}
{/block}

{block 'field_input'}
    <input type="{$type|default:'number'}" {if $min}min="{$min}"{/if} {if $max}max="{$max}"{/if} {if $step}step="{$step}"{/if}  {field_input_attr_common} />
{/block}
