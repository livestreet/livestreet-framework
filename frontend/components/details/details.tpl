{**
 * Сворачиваемый блок
 *
 * @param string  $title
 * @param string  $content
 * @param string  $body
 * @param boolean $open
 * @param string  $mods
 * @param string  $classes
 * @param array   $attributes
 *}

{* Название компонента *}
{$component = 'details'}

{* Генерируем копии локальных переменных, *}
{* чтобы их можно было изменять в дочерних шаблонах *}
{foreach [ 'title', 'content', 'body', 'open', 'mods', 'classes', 'attributes' ] as $param}
    {assign var="$param" value=$smarty.local.$param}
{/foreach}

{* Проверяем нужно разворачивать блок или нет *}
{if $open}
    {$classes = "$classes is-open"}
{/if}

{block 'details_options'}{/block}

{* Item *}
<div class="{$component} {cmods name=$component mods=$mods} {$classes}" {cattr list=$attributes}>
    {* Заголовок *}
    <h3 class="{$component}-title js-{$component}-title">
        {$title}
    </h3>

    {* Основной блок *}
    <div class="{$component}-body js-{$component}-body">
        {* Содержимое *}
        {if $content}
            <div class="{$component}-content">
                {$content}
            </div>
        {/if}

        {$body}
    </div>
</div>