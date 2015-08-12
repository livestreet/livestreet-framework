{**
 * Выпадающий список
 *}

{extends './field.tpl'}

{block 'field_input'}
    {$items = $smarty.local.items}
    {$isMultiple = $smarty.local.isMultiple}
    {$selectedValue = $smarty.local.selectedValue}

    {if $name && $form}
        {field_get_value form=$form name=$name assign=selectedValue}

        {if $isMultiple && ! isset($items)}
            {$items = $selectedValue}
        {/if}
    {/if}

    {function field_select_loop items=[]}
        {foreach $items as $item}
            {if is_array( $item.value )}
                <optgroup label="{$item.text}">
                    {field_select_loop items=$item.value}
                </optgroup>
            {else}
                {$isSelected = ( $form && $isMultiple ) || ( ( is_array( $selectedValue ) ) ? in_array( $item.value, $selectedValue ) : ( $item.value == $selectedValue ) )}

                <option value="{$item.value}" {if $isSelected}selected{/if} {cattr list=$item.attributes} {cdata name=$component data=$item.data}>
                    {$item.text|indent:( $item.level * 5 ):'&nbsp;'}
                </option>
            {/if}
        {/foreach}
    {/function}

    {* data-placeholder нужен для плагина chosen *}
    <select {field_input_attr_common useValue=false} {cdata name=$component list=$inputData} {if $smarty.local.placeholder}data-placeholder="{$smarty.local.placeholder}"{/if} {if $isMultiple}multiple{/if}>
        {field_select_loop items=$items}
    </select>
{/block}