{**
 * Accordion item
 *
 * @param string $title
 * @param string $content
 * @param string $mods
 * @param string $classes
 * @param array  $attributes
 *}

{* Название компонента *}
{$component = 'accordion-item'}

{* Генерируем копии локальных переменных, *}
{* чтобы их можно было изменять в дочерних шаблонах *}
{foreach [ 'title', 'content', 'mods', 'classes', 'attributes' ] as $param}
    {assign var="$param" value=$smarty.local.$param}
{/foreach}

{block 'accordion_item_options'}{/block}

{* Item *}
<h3 class="{$component}-title">{$title}</h3>
<div class="{$component}-content">{$content}</div>