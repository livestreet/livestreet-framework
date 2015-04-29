{**
 * Блок
 *
 * @param string       $title       (null)        Заголовок
 * @param string       $content     (null)
 * @param boolean      $show        (true)
 * @param array|string $list        (null)
 * @param array|string $tabs        (null)
 * @param string       $mods        (success)     Список модификторов основного блока (через пробел)
 * @param string       $classes     (null)        Список классов основного блока (через пробел)
 * @param array        $attributes  (null)        Список атрибутов основного блока
 *}

{$component = 'ls-block'}

{* Генерируем копии локальных переменных, *}
{* чтобы их можно было изменять в дочерних шаблонах *}
{foreach [ 'title', 'content', 'show', 'footer', 'list', 'tabs', 'mods', 'classes', 'attributes' ] as $param}
    {assign var="$param" value=$smarty.local.$param}
{/foreach}

{block 'block_options'}{/block}

{$show = $show|default:true}

{if $show}
    <div class="{$component} {cmods name=$component mods=$mods} {$classes}" {cattr list=$attributes}>
        {* Шапка *}
        {if $title}
            <header class="{$component}-header">
                {block 'block_header_inner'}
                    <h3 class="{$component}-title">
                        {$title}
                    </h3>
                {/block}
            </header>
        {/if}

        {block 'block_header_after'}{/block}

        {* Содержимое *}
        {if $content}
            {block 'block_content'}
                <div class="{$component}-content">
                    {block 'block_content_inner'}
                        {$content}
                    {/block}
                </div>
            {/block}
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
            {block 'block_footer'}
                <div class="{$component}-footer">
                    {block 'block_footer_inner'}
                        {$footer}
                    {/block}
                </div>
            {/block}
        {/if}

        {block 'block_footer_after'}{/block}
    </div>
{/if}