{**
 * Текстовое поле
 *}

{extends 'component@field.field'}

{block 'field_input'}
    <input type="{$smarty.local.type|default:'text'}" {field_input_attr_common} />
{/block}