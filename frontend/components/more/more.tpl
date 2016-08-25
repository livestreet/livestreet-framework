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
{component_define_params params=[ 'text', 'text_count', 'target', 'count', 'append', 'mods', 'classes', 'attributes', 'ajaxParams' ]}

{block 'more_options'}{/block}

<div class="{$component} {cmods name=$component mods=$mods} {$classes}"
    tabindex="0"
    {cattr list=$attributes}
    {cattr list=$ajaxParams prefix='data-param-'}
    {if $append}data-lsmore-append="{$append}"{/if}
    {if $target}data-lsmore-target="{$target}"{/if}
    {if isset($count)}data-lsmore-count="{$count}"{/if}>

    <div class="{$component}-loader ls-loading"></div>

    {* Текст *}
    {block 'more_text'}
        <span class="{$component}-text js-more-text">
            {if isset($count)}
                {$text_count|default:{lang 'more.text_count' count=$count plural=true}}
            {else}
                {$text|default:{lang 'more.text'}}
            {/if}
        </span>
    {/block}
</div>