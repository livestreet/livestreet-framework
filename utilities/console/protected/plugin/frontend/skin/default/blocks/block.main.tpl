{**
 * Блок в сайдбаре
 *}

{capture 'block_content'}
    {component 'example:p-test' template='bar' number=1234}
{/capture}

{component 'block'
    mods    = 'info'
    title   = 'Название блока'
    content = $smarty.capture.block_content}