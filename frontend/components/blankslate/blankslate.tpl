{**
 * blankslate
 *
 * @param string $title
 * @param string $text
 * @param boolean $visible
 * @param string $mods
 * @param string $classes
 * @param array  $attributes
 *}

{* Название компонента *}
{$component = 'ls-blankslate'}
{component_define_params params=[ 'title', 'text', 'visible', 'mods', 'classes', 'attributes' ]}

{$visible = $visible|default:true}

{block 'blankslate_options'}{/block}

<div class="{$component} {cmods name=$component mods=$mods} {$classes}" {cattr list=$attributes}
    {if ! $visible}style="display: none;"{/if}>

    {* Заголовок *}
    {if $title}
        <h3 class="{$component}-title">
            {$title}
        </h3>
    {/if}

    {* Подзаголовок *}
    {if $text}
        <div class="{$component}-text">
            {$text}
        </div>
    {/if}
</div>