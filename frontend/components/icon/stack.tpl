{**
 * Комбинированная иконка
 *
 * @param string $icon
 * @param string $in
 * @param string $classes
 * @param string $mods
 * @param array  $attributes
 *}

{$component = 'fa'}
{component_define_params params=[ 'icon', 'in', 'mods', 'classes', 'attributes' ]}

<span class="{$component}-stack {$component}-lg {cmods name=$component mods=$mods delimiter='-'} {$classes}" {cattr list=$attributes}>
    {if is_array($icon)}
        {$icon.mods = "stack-2x {$icon.mods}"}
        {component 'icon' params=$icon}
    {elseif $icon}
        {component 'icon' mods='stack-2x' icon=$icon}
    {/if}
    {if is_array($in)}
        {$in.mods = "stack-1x {$in.mods}"}
        {component 'icon' params=$in}
    {elseif $in}
        {component 'icon' mods='stack-1x' icon=$in}
    {/if}
</span>
