{**
 * Базовый шаблон блоков
 *}

{$component = 'block'}

{block 'block_options'}
    {$mods = $smarty.local.mods}
    {$title = $smarty.local.title}
    {$content = $smarty.local.content}
    {$footer = $smarty.local.footer}
    {$classes = $smarty.local.classes}
    {$list = $smarty.local.list}
    {$attributes = $smarty.local.attributes}
    {$show = $smarty.local.show|default:true}
{/block}

{if $show}
    {block 'block_before'}{/block}

    <div class="{$component} {cmods name=$component mods=$mods} {$classes}" {cattr list=$attributes}>
        {* Шапка *}
        {if $title}
            <header class="{$component}-header">
                <h3 class="{$component}-title">
                    {$title}
                </h3>

                {block 'block_header_end'}{/block}
            </header>
        {/if}

        {block 'block_header_after'}{/block}

        {* Навигация *}
        {block 'block_nav' hide}
            <nav class="{$component}-nav">
                {$smarty.block.child}
            </nav>
        {/block}

        {block 'block_nav_after'}{/block}

        {* Содержимое *}
        {if $content}
            <div class="{$component}-content">
                {$content}
            </div>
        {/if}

        {block 'block_content_after'}{/block}

        {* List group *}
        {if is_array( $list )}
            {component 'item' template='group' params=$list}
        {elseif $list}
            {$list}
        {/if}

        {* Tabs *}
        {if is_array( $tabs )}
            {component 'tabs' classes='js-tabs-block' params=$tabs}
        {elseif $tabs}
            {$tabs}
        {/if}

        {* Подвал *}
        {if $footer}
            <div class="{$component}-footer">
                {$footer}
            </div>
        {/if}

        {block 'block_footer_after'}{/block}
    </div>

    {block 'block_after'}{/block}
{/if}