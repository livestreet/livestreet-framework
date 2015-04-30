{**
 * Список тегов
 *}

{$component = 'ls-tags'}

{if $smarty.local.tags}
	<ul class="{$component} js-tags-topic-{$smarty.local.targetId}" data-type="{$smarty.local.targetType}" data-id="{$smarty.local.targetId}">
		<li class="{$component}-item {$component}-item-label">{$aLang.tags.tags}:</li>

		{strip}
			{block 'tags_list'}
				{foreach $smarty.local.tags as $tag}
					<li class="{$component}-item {$component}-item-tag">
						{if ! $tag@first}, {/if}<a rel="tag" href="{router page='tag'}{$tag|escape:'url'}/">{$tag|escape}</a>
					</li>
				{/foreach}
			{/block}
		{/strip}
	</ul>
{/if}