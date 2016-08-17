{**
 * Базовый шаблон модальных окон
 *}

{* Название компонента *}
{$component = 'ls-modal'}
{component_define_params params=[ 'title', 'content', 'tabs', 'body', 'id', 'options', 'showFooter', 'primaryButton', 'mods', 'classes', 'attributes' ]}

{* Дефолтные значения *}
{$showFooter = $showFooter|default:true}

{block 'modal_options'}{/block}

{* Модальное окно *}
<div class="{$component} {cmods name=$component mods=$mods} {$classes}" {cattr list=$attributes}
    id="{$id}"
    data-type="modal"
    {cattr prefix='data-lsmodal-' list=$options}>

    <div class="{$component}-dialog">
        {* Шапка *}
        {block 'modal_title'}
            <header class="{$component}-header">
                {* Заголовок *}
                {if $title}
                    <h3 class="{$component}-title">{$title}</h3>
                {/if}

                {* Кнопка закрытия *}
                <button class="{$component}-close" data-type="modal-close">
                    {component 'icon' icon='remove' attributes=[ 'aria-hidden' => 'true' ]}
                </button>
            </header>
        {/block}

        {block 'modal_header_after'}{/block}

        {* Содержимое *}
        {block 'modal_content'}
            {if $content}
                <div class="{$component}-body">
                    {$content}{$smarty.block.child}
                </div>
            {/if}
        {/block}

        {* Tabs *}
        {( is_array( $tabs ) ) ? {component 'tabs' classes="{$component}-tabs js-{$component}-tabs" params=$tabs} : $tabs}

        {$body}

        {* Подвал *}
        {block 'modal_footer'}
            {if $showFooter}
                <div class="{$component}-footer">
                    {block 'modal_footer_inner'}
                        {* Кнопка закрытия окна *}
                        {component 'button' type='button' text={lang 'common.cancel'} attributes=[ 'data-type' => 'modal-close' ]}

                        {* Кнопка отвечающее за основное действие *}
                        {( is_array( $primaryButton ) ) ? {component 'button' mods='primary' params=$primaryButton} : $primaryButton}
                    {/block}
                </div>
            {/if}
        {/block}
    </div>
</div>