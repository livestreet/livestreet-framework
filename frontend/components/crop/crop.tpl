{**
 * Обрезка загруженного изображения
 *
 * @param string  $title
 * @param string  $desc
 * @param string  $image
 * @param integer $width
 * @param integer $height
 * @param integer $originalWidth
 * @param integer $originalHeight
 *}

{$component = 'ls-crop'}
{component_define_params params=[ 'desc', 'image', 'originalWidth', 'originalHeight', 'width', 'height', 'title', 'mods', 'classes', 'attributes' ]}

{block 'crop_modal_options'}{/block}

{capture 'modal_content'}
    {$desc = $desc|escape}

    {if $desc}
        <p class="{$component}-desc">{$desc}</p>
    {/if}

    <img src="{$image|escape}?v{mt_rand()}" width="{$width}" height="{$height}" class="{$component}-image js-crop" data-crop-width="{$originalWidth}" data-crop-height="{$originalHeight}">
{/capture}

{component 'modal'
    title         = $title|escape|default:{lang 'crop.title'}
    content       = $smarty.capture.modal_content
    mods          = 'crop'
    primaryButton  = [
        'text'    => {lang 'common.save'},
        'classes' => 'js-crop-submit'
    ]}