{**
 * Аватар с именем
 *
 * @param string $name
 *}

{extends 'component@avatar.avatar'}

{block 'avatar_options' append}
    {foreach [ 'name' ] as $param}
        {assign var="$param" value=$smarty.local.$param}
    {/foreach}

    {$classes = "$classes avatar-name"}
{/block}

{block 'avatar_inner' append}
    <div class="{$component}-name">
        {if $url}<a href="{$url}" class="{$component}-name-link">{/if}
            {$name}
        {if $url}</a>{/if}
    </div>
{/block}