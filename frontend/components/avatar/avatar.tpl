{**
 * Аватар
 *
 * @param string $image
 * @param string $size
 * @param string $alt
 * @param string $url
 * @param string $mods
 * @param string $classes
 * @param array  $attributes
 *}

{* Название компонента *}
{$component = 'avatar'}

{* Генерируем копии локальных переменных, *}
{* чтобы их можно было изменять в дочерних шаблонах *}
{foreach [ 'image', 'size', 'url', 'alt', 'mods', 'classes', 'attributes' ] as $param}
    {assign var="$param" value=$smarty.local.$param}
{/foreach}

{$size = $size|default:'default'}
{$sizes = [ 'large', 'default', 'small', 'xsmall', 'inline' ]}

{block 'avatar_options'}{/block}

{if in_array($size, $sizes)}
    {$mods = "$mods size-$size"}
{/if}

{* Avatar *}
<div class="{$component} {cmods name=$component mods=$mods} {$classes}" {cattr list=$attributes}>
    {block 'avatar_inner'}
        {if $url}<a href="{$url}" class="{$component}-image-link">{/if}
            <img src="{$image}" alt="{$alt}" class="{$component}-image">
        {if $url}</a>{/if}
    {/block}
</div>