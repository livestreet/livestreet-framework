{**
 * Список аваторов
 *
 * @param string|array $items
 * @param string $mods
 * @param string $classes
 * @param array  $attributes
 * @param array  $blankslateParams
 *}

{* Название компонента *}
{$component = 'ls-avatar-list'}

{component_define_params params=[ 'items', 'blankslateParams', 'mods', 'classes', 'attributes' ]}

{$classes = "$classes ls-clearfix"}

{block 'avatar_list_options'}{/block}

<div class="{$component} {cmods name=$component mods=$mods} {$classes}" {cattr list=$attributes}>
    {block 'avatar_list_inner'}
        {if $items}
            {component 'avatar' template='loop' items=$items}
        {else}
            {component 'blankslate' text={lang 'common.empty'} params=$blankslateParams}
        {/if}
    {/block}
</div>