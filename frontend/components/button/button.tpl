{**
 * Кнопка
 *
 * @param string  $type       (submit)   Тип кнопки (submit, reset, button)
 * @param string  $text       (null)     Текст кнопки
 * @param string  $url        (null)     Ссылка
 * @param string  $id         (null)     Атрибут id
 * @param string  $name       (null)     Атрибут name
 * @param boolean $isDisabled (false)    Атрибут disabled
 * @param string  $form       (null)     ID формы для сабмита
 * @param string  $icon       (null)     Название иконки
 * @param string  $mods       (null)     Список модификторов основного блока (через пробел)
 * @param string  $classes    (null)     Список классов основного блока (через пробел)
 * @param array   $attributes (null)     Список атрибутов основного блока
 *}

{* Название компонента *}
{$component = 'button'}

{* Генерируем копии локальных переменных, *}
{* чтобы их можно было изменять в дочерних шаблонах *}
{foreach [ 'type', 'text', 'value', 'url', 'id', 'name', 'isDisabled', 'form', 'icon', 'mods', 'classes', 'attributes' ] as $param}
    {assign var="$param" value=$smarty.local.$param}
{/foreach}

{* Дефолтные значения *}
{if $icon && ! $text}
    {$mods = "$mods icon"}
{/if}

{$tag = ( $url ) ? 'a' : 'button'}
{$type = $type|default:'submit'}

{* Если указана ссылка url то заменяем тег <button> на <a> *}
<{$tag}
        {if ! $url}
            type="{$type}"
            value="{if $value}{$value}{elseif isset( $_aRequest[ $name ] )}{$_aRequest[ $name ]}{/if}"
            {if $isDisabled}disabled{/if}
            {if $form}form="{$form}"{/if}
        {else}
            href="{$url}"
            role="button"
        {/if}
        {if $id}id="{$id}"{/if}
        {if $name}name="{$name}"{/if}
        class="{$component} {cmods name=$component mods=$mods} {$classes}"
        {cattr list=$attributes}>
    {* Иконка *}
    {if $icon}
        {component 'icon' icon=$icon attributes=[ 'aria-hidden' => 'true' ]}
    {/if}

    {* Текст *}
    {$text}
</{$tag}>