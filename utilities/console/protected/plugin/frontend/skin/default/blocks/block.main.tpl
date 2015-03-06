{**
 * Блок в сайдбаре
 *}

{capture 'block_content'}
    Содержание блока
{/capture}

{component 'block'
    mods    = 'info'
    title   = 'Название блока'
    content = $smarty.capture.block_content}