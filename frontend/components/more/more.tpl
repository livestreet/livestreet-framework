{**
 * Подгрузка контента
 *
 * @param string  $text
 * @param string  $target
 * @param integer $count
 * @param boolean $append
 * @param string  $mods
 * @param string  $classes
 * @param array   $attributes
 *}

{* Название компонента *}
{$component = 'ls-more'}
{component_define_params params=[ 'text', 'target', 'count', 'append', 'mods', 'classes', 'attributes', 'ajaxParams' ]}

{block 'more_options'}{/block}

<div class="{$component} {cmods name=$component mods=$mods} {$classes}"
    tabindex="0"
    {cattr list=$attributes}
    {cattr list=$ajaxParams prefix='data-param-'}
    {if $append}data-lsmore-append="{$append}"{/if}
    {if $target}data-lsmore-target="{$target}"{/if}>

    {* Текст *}
    {$text|default:{lang 'more.text'}}

    {* Счетчик *}
    {if $count}
        (<span class="js-more-count">{$count}</span>)
    {/if}
</div>