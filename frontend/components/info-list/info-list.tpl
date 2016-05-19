{**
 * Список с информацией
 *
 * @param array $list (null) Массив в формате [ label, content ]
 * @param string $title (null) Заголовок
 *}

{* Название компонента *}
{$component = 'ls-info-list'}
{component_define_params params=[ 'title', 'list', 'mods', 'classes', 'attributes' ]}

{block 'info_list_options'}{/block}

{if $list}
    <div class="{$component} {cmods name=$component mods=$mods} {$classes}" {cattr list=$attributes}>
        {* Заголовок *}
        {if $title}
            <h2 class="{$component}-title">{$title}</h2>
        {/if}

        {* Список *}
        <ul class="{$component}-list">
            {foreach $list as $item}
                <li class="{$component}-item {$item['classes']}" {cattr list=$item['attributes']}>
                    <div class="{$component}-item-label">
                        {$item['label']}
                    </div>
                    <strong class="{$component}-item-content">{$item['content']}</strong>
                </li>
            {/foreach}
        </ul>
    </div>
{/if}