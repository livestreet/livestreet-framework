{**
 * Аякс пагинация
 *
 * @param string $classes    Дополнительные классы
 * @param string $mods       Список классов-модификаторов
 * @param array  $attributes Атрибуты
 *}

{* Название компонента *}
{$component = 'ls-pagination'}

{* Генерируем копии локальных переменных, *}
{* чтобы их можно было изменять в дочерних шаблонах *}
{foreach [ 'mods', 'classes', 'attributes' ] as $param}
    {assign var="$param" value=$smarty.local.$param}
{/foreach}

{$mods = "$mods ajax"}

<nav class="{$component} {cmods name=$component mods=$mods} {$classes}" {cattr list=$attributes}></nav>