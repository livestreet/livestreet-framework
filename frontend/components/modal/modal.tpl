{**
 * Базовый шаблон модальных окон
 *}

{* Название компонента *}
{$component = 'modal'}

{* Генерируем копии локальных переменных, *}
{* чтобы их можно было изменять в дочерних шаблонах *}
{foreach [ 'title', 'content', 'tabs', 'body', 'id', 'options', 'showFooter', 'primaryButton', 'mods', 'classes', 'attributes' ] as $param}
    {assign var="$param" value=$smarty.local.$param}
{/foreach}

{* Дефолтные значения *}
{$showFooter = $showFooter|default:true}

{block 'modal_options'}{/block}

{* Модальное окно *}
<div class="{$component} {cmods name=$component mods=$mods} {$classes}" {cattr list=$attributes}
    id="{$id}"
    data-type="modal"
    {cattr prefix='data-lsmodal-' list=$options}>

    {* Шапка *}
    {block 'modal_title'}
        <header class="modal-header">
            {* Заголовок *}
            <h3 class="modal-title">{$title}</h3>

            {* Кнопка закрытия *}
            <button class="modal-close" data-type="modal-close">
                {component 'icon' icon='remove' attributes=[ 'aria-hidden' => 'true' ]}
            </button>
        </header>
    {/block}

    {block 'modal_header_after'}{/block}

    {* Содержимое *}
    {block 'modal_content'}
        {if $content}
            <div class="modal-body">
                {$content}{$smarty.block.child}
            </div>
        {/if}
    {/block}

    {* Tabs *}
    {( is_array( $tabs ) ) ? {component 'tabs' classes='modal-tabs js-modal-tabs' params=$tabs} : $tabs}

    {$body}

    {* Подвал *}
    {block 'modal_footer'}
        {if $showFooter}
            <div class="modal-footer">
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