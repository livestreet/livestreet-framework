{**
 * Text
 *
 * @param string $text
 * @param string $mods
 * @param string $classes
 * @param array  $attributes
 *}

{* Название компонента *}
{$component = 'ls-text'}
{component_define_params params=[ 'text', 'mods', 'classes', 'attributes' ]}

{block 'text_options'}{/block}

{* Text *}
<div class="{$component} {cmods name=$component mods=$mods} {$classes}" {cattr list=$attributes}>
    {$text}
</div>