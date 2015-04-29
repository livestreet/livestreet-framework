{**
 * Группировка кнопок
 *
 * @param string        $role          (group) ARIA role (group|toolbar)
 * @param array         $buttonParams  (null)  Общие параметры для всех кнопок в группе
 * @param array|string  $buttons       (null)  Массив кнопок, либо строка с html кодом кнопок
 * @param string        $mods          (null)  Список модификторов основного блока (через пробел)
 * @param string        $classes       (null)  Список классов основного блока (через пробел)
 * @param array         $attributes    (null)  Список атрибутов основного блока
 *}

{* Название компонента *}
{$component = 'ls-button-group'}

{* Генерируем копии локальных переменных, *}
{* чтобы их можно было изменять в дочерних шаблонах *}
{foreach [ 'role', 'buttons', 'buttonParams', 'classes', 'mods', 'attributes' ] as $param}
    {assign var="$param" value=$smarty.local.$param}
{/foreach}

{block 'button_group_options'}{/block}

{$role = $role|default:'group'}

{* Делаем группировку по умолчанию горизонтальной *}
{if ! in_array( 'vertical', explode( ' ', $mods ) )}
    {$mods = "$mods horizontal"}
{/if}

{if $buttons}
    <div class="{$component} {cmods name=$component mods=$mods} {$classes} clearfix" {cattr list=$attributes} role="{$role}">
        {if is_array( $buttons )}
            {foreach $buttons as $button}
                {if is_array( $button )}
                    {block 'button_group_button'}
                        {component 'button' params=array_merge( $buttonParams|default:[], $button )}
                    {/block}
                {else}
                    {$button}
                {/if}
            {/foreach}
        {else}
            {$buttons}
        {/if}
    </div>
{/if}