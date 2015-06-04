{**
 * Иконка
 *
 * @param string $icon
 * @param string $classes
 * @param string $mods
 * @param array  $attributes
 *}

{$component = 'ls-icon'}

<i class="fa fa-{$smarty.local.icon} {cmods name=$component mods=$smarty.local.mods} {$smarty.local.classes}" {cattr list=$smarty.local.attributes}></i>