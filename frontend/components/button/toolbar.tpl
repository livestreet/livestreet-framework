{**
 * Тулбар
 *
 * @param array   $groups     (null) Массив групп кнопок, либо строка с html кодом групп кнопок
 * @param string  $mods       (null) Список модификторов основного блока (через пробел)
 * @param string  $classes    (null) Список классов основного блока (через пробел)
 * @param array   $attributes (null) Список атрибутов основного блока
 *}

{* Название компонента *}
{$component = 'ls-button-toolbar'}

{* Генерируем копии локальных переменных, *}
{* чтобы их можно было изменять в дочерних шаблонах *}
{foreach [ 'groups', 'classes', 'mods', 'attributes' ] as $param}
    {assign var="$param" value=$smarty.local.$param}
{/foreach}

{block 'button_toolbar_options'}{/block}

{* Делаем группировку по умолчанию горизонтальной *}
{if in_array( 'vertical', explode( ' ', $mods ) )}
    {$groupMod = 'vertical'}
{/if}

<div class="{$component} {cmods name=$component mods=$mods} {$classes} clearfix" {cattr list=$attributes} role="toolbar">
    {if is_array( $groups )}
        {foreach $groups as $group}
            {if is_array( $group )}
                {block 'button_toolbar_group'}
                    {component 'button' template='group' role='group' mods=$groupMod params=$group}
                {/block}
            {else}
                {$group}
            {/if}
        {/foreach}
    {else}
        {$groups}
    {/if}
</div>