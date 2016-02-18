{**
 * Текстовое поле
 *}

{extends 'component@field.field'}

{block 'field_input'}
    <textarea {field_input_attr_common useValue=false} rows="{$rows}">{field_input_attr_value}</textarea>
{/block}