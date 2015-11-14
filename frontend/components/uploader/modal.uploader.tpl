{**
 * Загрузка медиа-файлов
 *}

{extends 'Component@modal.modal'}

{block 'modal_options' append}
    {$mods = "$mods uploader"}
    {$options = array_merge( $options|default:[], [ 'center' => 'false' ] )}
    {$content = {component 'uploader' classes='js-uploader-modal'}}

    {if $smarty.local.choosable|default:true}
        {$primaryButton = [
            'text' => {lang 'common.choose'},
            'classes' => 'js-uploader-modal-choose'
        ]}
    {/if}
{/block}