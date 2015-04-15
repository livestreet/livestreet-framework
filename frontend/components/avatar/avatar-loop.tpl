{**
 * Аватар
 *
 * @param string|array $items
 * @param string $mods
 * @param string $classes
 * @param array  $attributes
 *}

{foreach [ 'items' ] as $param}
    {assign var="$param" value=$smarty.local.$param}
{/foreach}

{if is_array( $items )}
    {foreach $items as $avatar}
        {if is_array( $avatar )}
            {block 'avatar_loop_avatar'}
                {component 'avatar' template='name' params=$avatar}
            {/block}
        {else}
            {$avatar}
        {/if}
    {/foreach}
{else}
    {$items}
{/if}