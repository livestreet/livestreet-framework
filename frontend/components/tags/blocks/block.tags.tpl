{**
 * Теги
 *}

{extends 'component@block.block'}

{block 'block_options' append}
    {component_define_params params=[ 'tags' ]}

    {$mods = "$mods tags"}
    {$title = $title|default:{lang 'tags.block_tags.title'}}
    {$content  = {component 'tags' template='cloud' tags=$tags}}
{/block}