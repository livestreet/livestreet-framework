{**
 * Badge
 *
 * @param string $value
 * @param string $mods
 * @param string $classes
 * @param array  $attributes
 *}

{* Название компонента *}
{$component = 'ls-badge'}
{component_define_params params=[ 'value', 'mods', 'classes', 'attributes' ]}

{block 'badge_options'}{/block}

{* Text *}
{if $value}
    <div class="{$component} {cmods name=$component mods=$mods} {$classes}" {cattr list=$attributes}>
        {$value}
    </div>
{/if}