{**
 * Теги
 *
 * @styles css/blocks.css
 *}

{component 'block'
    mods     = 'tags'
    title    = {lang 'tags.block_tags.title'}
    content  = {component 'tags' template='cloud' tags=$smarty.local.tags url=$smarty.local.url}}