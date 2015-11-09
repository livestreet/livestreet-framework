{**
 * Теги
 *}

{extends 'component@block.block'}

{block 'block_options' append}
    {$mods = "$mods tags"}
    {$title = $title|default:{lang 'tags.block_tags.title'}}
    {$content  = {component 'tags' template='cloud' tags=$smarty.local.tags}}
{/block}