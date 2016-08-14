{**
 * Иконка
 *
 * @param string $icon
 * @param string $classes
 * @param string $mods
 * @param array  $attributes
 *}

{$component = 'fa'}
{component_define_params params=[ 'icon', 'mods', 'classes', 'attributes' ]}

<i class="{$component} fa-{$icon} {cmods name=$component mods=$mods delimiter='-'} {$classes}" {cattr list=$attributes}></i>