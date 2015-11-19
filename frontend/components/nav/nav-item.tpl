{**
 * nav-item
 *
 * @param string  $title
 * @param array   $menu
 * @param string  $url
 * @param string  $text
 * @param boolean $showZeroCounter
 * @param string  $activeItem
 * @param string  $count
 * @param boolean $isRoot
 * @param boolean $isActive
 * @param string  $show
 * @param string  $data
 * @param string  $target
 * @param string  $mods
 * @param string  $classes
 * @param array   $attributes
 *}

{* Название компонента *}
{$component = 'ls-nav-item'}

{* Генерируем копии локальных переменных, *}
{* чтобы их можно было изменять в дочерних шаблонах *}
{foreach [ 'menu', 'url', 'text', 'icon', 'showZeroCounter', 'activeItem', 'count', 'isRoot', 'isActive', 'show', 'data', 'mods', 'classes', 'attributes', 'target' ] as $param}
    {assign var="$param" value=$smarty.local.$param}
{/foreach}

{* Дефолтные значения *}
{$show = $show|default:true}
{$url = $url|default:'#'}

{* Установка модификаторов и классов *}
{$mods = ($menu) ? "$mods has-children" : $mods}
{$mods = ($count) ? "$mods has-badge" : $mods}
{$classes = ($isActive) ? "$classes active" : $classes}
{$target = ($target) ? "target=\"$target\"" : ''}

{* Smarty-блок для изменения опций *}
{block 'nav_item_options'}{/block}

{* nav-item *}
{if $show}
    <li class="{$component} {cmods name=$component mods=$mods} {$classes}" {cattr list=$attributes} role="menuitem"
        {foreach $data as $dataItem}
            data-{$dataItem@key}={$dataItem@value}
        {/foreach}>

        {* Ссылка *}
        <a href="{$url}" class="{$component}-link" {$target}>
            {* Иконка *}
            {if is_array($icon)}
                {component 'icon' attributes=[ 'aria-hidden' => 'true' ] params=$icon}
            {elseif $icon}
                {component 'icon' icon=$icon attributes=[ 'aria-hidden' => 'true' ]}
            {/if}

            {* Текст *}
            {$text}

            {* Счетчик *}
            {if isset($count) && ( $showZeroCounter || ( ! $showZeroCounter && $count > 0 ) )}
                {component 'badge' value=$count classes="{$component}-badge"}
            {/if}

            {* Стрелка *}
            {if $menu}
                <div class="ls-caret {if ! $isRoot}ls-caret--right{/if} {$component}-caret"></div>
            {/if}
        </a>

        {* Подменю *}
        {if $menu}
            {component 'nav'
                activeItem = $activeItem
                mods       = 'stacked'
                isSubnav   = true
                items      = $menu}
        {/if}
    </li>
{/if}