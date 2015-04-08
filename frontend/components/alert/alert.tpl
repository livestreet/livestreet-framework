{**
 * Уведомления
 *
 * @param string  $title       (null)        Заголовок
 * @param mixed   $text        (null)        Массив либо строка с текстом уведомления. Массив должен быть в формате: `[ [ title, msg ], ... ]`
 * @param bool    $visible     (true)        Показывать или нет уведомление
 * @param bool    $dismissible (false)       Показывать или нет кнопку закрытия
 * @param string  $mods        (success)     Список модификторов основного блока (через пробел)
 * @param string  $classes     (null)        Список классов основного блока (через пробел)
 * @param array   $attributes  (null)        Список атрибутов основного блока
 *}

{* Название компонента *}
{$component = 'alert'}

{* Генерируем копии локальных переменных, *}
{* чтобы их можно было изменять в дочерних шаблонах *}
{foreach [ 'title', 'text', 'visible', 'dismissible', 'mods', 'classes', 'attributes' ] as $param}
    {assign var="$param" value=$smarty.local.$param}
{/foreach}

{* Дефолтные значения *}
{$uid = "{$component}{rand( 0, 10e10 )}"}
{$visible = $visible|default:true}

{if $dismissible}
    {$mods = "$mods dismissible"}
{/if}

{block 'alert_options'}{/block}

{* Уведомление *}
<div class="{$component} {cmods name=$component mods=$mods} {$classes} js-alert" role="alert" {if ! $visible}hidden{/if} {cattr list=$attributes}>
    {* Заголовок *}
    {if $title}
        <h4 class="{$component}-title">{$title}</h4>
    {/if}

    {* Контент *}
    {if $text}
        <div class="{$component}-body">
            {block 'alert_body'}
                {if is_array( $text )}
                    <ul class="{$component}-list">
                        {foreach $text as $alert}
                            <li class="{$component}-list-item">
                                {if $alert.title}
                                    <strong>{$alert.title}</strong>:
                                {/if}

                                {$alert.msg}
                            </li>
                        {/foreach}
                    </ul>
                {else}
                    {$text}
                {/if}
            {/block}
        </div>
    {/if}

    {* Кнопка закрытия *}
    {if $dismissible}
        <button class="{$component}-close js-alert-close" aria-labelledby="{$uid}">
            <span class="icon-remove"></span>
            <span id="{$uid}" aria-hidden="true" hidden>{lang 'common.close'}</span>
        </button>
    {/if}
</div>