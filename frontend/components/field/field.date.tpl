{**
 * Выбор даты
 *}

{extends './field.text.tpl'}

{block 'field_options' append}
    {$mods = "$mods date"}
{/block}