{**
 * Выбор даты
 *}

{extends './field.text.tpl'}

{block 'field_options' append}
    {$_inputClasses = "{$_inputClasses} width-150"}
{/block}