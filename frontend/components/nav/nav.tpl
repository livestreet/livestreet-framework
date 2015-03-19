{**
 * Навигация
 *
 * @param string  $name
 * @param array   $items
 * @param string  $activeItem
 * @param integer $hookParams
 * @param boolean $showSingle
 * @param boolean $isSubnav
 * @param string  $mods
 * @param string  $classes
 * @param array   $attributes
 *}

{* Название компонента *}
{$component = 'nav'}

{* Генерируем копии локальных переменных, *}
{* чтобы их можно было изменять в дочерних шаблонах *}
{foreach [ 'name', 'items', 'activeItem', 'showSingle', 'isSubnav', 'items', 'mods', 'classes', 'attributes' ] as $param}
    {assign var="$param" value=$smarty.local.$param}
{/foreach}

{* Уникальное имя меню *}
{$name = ( $name ) ? $name : rand(0, 10e10)}

{* Получаем пункты установленные плагинами *}
{hook run="{$component}_{$name}" assign='itemsHook' params=$hookParams items=$items array=true}

{$items = ( $itemsHook ) ? $itemsHook : $items}

{* Считаем кол-во неактивных пунктов *}
{$disabledItemsCounter = 0}

{foreach $items as $item}
    {$disabledItemsCounter = $disabledItemsCounter + ( ! $item['is_enabled']|default:true && $item['name'] != '-' )}
{/foreach}

{$classes = "{$classes} clearfix"}

{if $isSubnav}
    {$mods = "$mods sub"}
{/if}

{* Smarty-блок для изменения опций *}
{block 'nav_options'}{/block}

{* Отображем меню только если есть активные пункты *}
{if count( $items ) - $disabledItemsCounter - ( ( $showSingle|default:false ) ? 0 : 1 )}
    <ul class="{$component} {cmods name=$component mods=$mods} {$classes}" {cattr list=$smarty.local.attributes}>
        {foreach $items as $item}
            {$isEnabled = $item[ 'is_enabled' ]}

            {if $isEnabled|default:true}
                {if $item['name'] != '-'}
                    {component 'nav' template='item'
                        isRoot   = !$isSubnav
                        isActive = ($smarty.local.activeItem == $item['name'])
                        params   = $item}
                {else}
                    {* Разделитель *}
                    <li class="{$component}-separator"></li>
                {/if}
            {/if}
        {/foreach}
    </ul>
{/if}