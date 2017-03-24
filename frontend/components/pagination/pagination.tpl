{**
 * Пагинация
 *
 * @param integer $total
 * @param integer $current
 * @param string  $url
 * @param string  $padding (2)
 * @param bool    $showSingle (false)
 * @param bool    $showPager (false)
 *
 * @param string $classes    Дополнительные классы
 * @param string $mods       Список классов-модификаторов
 * @param array  $attributes Атрибуты
 *}

{* Название компонента *}
{$component = 'ls-pagination'}


{**
 * Элемент пагинации
 *
 * @param bool   $isActive (false) Если true, то элемент помечается как активный (текущая страница)
 * @param string $text             Текст
 * @param string $page             Страница
 * @param string $linkClasses      Дополнительные классы для ссылки
 *}
{function pagination_item page=0 text='' isActive=false}
    <li class="{$component}-item {if $isActive}active{/if}">
        {if $isActive || ! $page}
            <span class="{$component}-item-inner">{$text|default:$page}</span>
        {else}
            <a class="{$component}-item-inner {$component}-item-link {$linkClasses}" href="{str_replace('__page__', $page, $url)}">{$text|default:$page}</a>
        {/if}
    </li>
{/function}


{component_define_params params=[ 'total', 'showPager', 'showSingle', 'current', 'url', 'padding', 'mods', 'classes', 'attributes' ]}

{* Дефолтные значения *}
{$padding = $padding|default:2}

{block 'pagination_options'}{/block}

{if ( $showSingle && $total && $current ) || ( ! $showSingle && $total > 1 && $current )}
    {$current = (int)$current}
    {* Вычисляем следующую страницу *}
    {$next = ( $current == $total ) ? 0 : $current + 1}

    {* Вычисляем предыдущую страницу *}
    {$prev = $current - 1}

    {* Вычисляем стартовую и конечную страницы *}
    {$start = 1}
    {$end = $total}

    {* Проверяем нужно ли выводить разделители "..." или нет *}
    {if $total > $padding * 2 + 1}
        {$start = ( $current - $padding < 4 ) ? 1 : $current - $padding}
        {$end = ( $current + $padding > $total - 3 ) ? $total : $current + $padding}
    {/if}


    {* Пагинация *}
    <nav class="{$component} {cmods name=$component mods=$mods} {$classes}" {cattr list=$attributes}
        {if $next}data-pagination-next="{str_replace('__page__', $next, $url)}"{/if}
        {if $prev}data-pagination-prev="{str_replace('__page__', $prev, $url)}"{/if}>

        {if $showPager}
            <ul class="{$component}-list {$component}-pager">
                {* Предыдущая страница *}
                {pagination_item page=$prev text="&larr; {$aLang.pagination.previous}" linkClasses="{$component}-prev js-{$component}-prev"}

                {* Следущая страница *}
                {pagination_item page=$next text="{$aLang.pagination.next} &rarr;" linkClasses="{$component}-next js-{$component}-next"}
            </ul>
        {/if}

        <ul class="{$component}-list">
            {if $start > 2}
                {pagination_item page=1}
                {pagination_item text='...'}
            {/if}

            {section 'pagination' start=$start loop=$end + 1}
                {pagination_item page=$smarty.section.pagination.index isActive=( $smarty.section.pagination.index === $current )}
            {/section}

            {if $end < $total - 1}
                {pagination_item text='...'}
                {pagination_item page=$total}
            {/if}
        </ul>
    </nav>
{/if}