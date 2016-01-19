{**
 * Иконка
 *
 * @param string $icon
 * @param string $classes
 * @param string $mods
 * @param array  $attributes
 *}

{$component = 'ls-icon'}
{component_define_params params=[ 'icon', 'mods', 'classes', 'attributes' ]}

<i class="fa fa-{$icon} {$component} {cmods name=$component mods=$mods} {$classes}" {cattr list=$attributes}></i>