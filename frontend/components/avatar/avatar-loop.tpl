{**
 * Аватар
 *
 * @param string|array $items
 * @param string $mods
 * @param string $classes
 * @param array  $attributes
 *}

{component_define_params params=[ 'items' ]}

{if is_array( $items )}
    {foreach $items as $avatar}
        {if is_array( $avatar )}
            {block 'avatar_loop_avatar'}
                {component 'avatar' params=$avatar}
            {/block}
        {else}
            {$avatar}
        {/if}
    {/foreach}
{else}
    {$items}
{/if}