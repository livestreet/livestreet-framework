{**
 * Аякс пагинация
 *
 * @param string $classes    Дополнительные классы
 * @param string $mods       Список классов-модификаторов
 * @param array  $attributes Атрибуты
 *}

{* Название компонента *}
{$component = 'ls-pagination'}

{component_define_params params=[ 'mods', 'classes', 'attributes' ]}

{$mods = "$mods ajax"}

<nav class="{$component} {cmods name=$component mods=$mods} {$classes}" {cattr list=$attributes}></nav>