{**
 * Аватар
 *
 * @param string $image
 * @param string $size
 * @param string $name
 * @param string $alt
 * @param string $url
 * @param string $mods
 * @param string $classes
 * @param array  $attributes
 *}

{* Название компонента *}
{$component = 'ls-avatar'}
{component_define_params params=[ 'image', 'size', 'name', 'url', 'alt', 'mods', 'classes', 'attributes' ]}

{$size = $size|default:'default'}
{$sizes = [ 'large', 'default', 'small', 'xsmall', 'xxsmall', 'text' ]}

{block 'avatar_options'}{/block}

{if $name}
    {$mods = "$mods has-name"}
{/if}

{if in_array($size, $sizes)}
    {$mods = "$mods size-$size"}
{/if}

{* Аватар *}
<div class="{$component} {cmods name=$component mods=$mods} {$classes}" {cattr list=$attributes}>
    {block 'avatar_inner'}
        {* Изображение *}
        {if $url}<a href="{$url}" class="{$component}-image-link">{/if}
            <img src="{$image}" alt="{$alt}" class="{$component}-image">
        {if $url}</a>{/if}

        {* Имя объекта *}
        {if $name}
            <div class="{$component}-name">
                {if $url}<a href="{$url}" class="{$component}-name-link">{/if}
                    {$name}
                {if $url}</a>{/if}
            </div>
        {/if}
    {/block}
</div>