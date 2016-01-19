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
{component_define_params params=[ 'desc', 'usePreview', 'image', 'originalWidth', 'originalHeight', 'width', 'height', 'title', 'mods', 'classes', 'attributes' ]}

{capture 'modal_content'}
    {$desc = $desc|escape}
    {$usePreview = $usePreview}

    {if $desc}
        <p class="{$component}-desc">{$desc}</p>
    {/if}

    {$image = "{$image|escape}?v{rand( 0, 10e10 )}"}

    <div class="{$component} js-crop" data-crop-width="{$originalWidth}" data-crop-height="{$originalHeight}">
        {* Изображение *}
        <div class="{$component}-image-holder js-crop-image-holder">
            <img src="{$image}" width="{$width}" height="{$height}" class="{$component}-image js-crop-image">
        </div>

        {* Превью *}
        {if $usePreview}
            <div class="{$component}-previews js-crop-previews">
                {foreach [ 100, 64, 48 ] as $size}
                    <div style="width: {$size}px; height: {$size}px;" class="{$component}-preview js-crop-preview">
                        <img src="{$image}" class="js-crop-preview-image" data-size="{$size}">
                    </div>
                {/foreach}
            </div>
        {/if}
    </div>
{/capture}

{component 'modal'
    title         = $title|escape|default:{lang 'crop.title'}
    content       = $smarty.capture.modal_content
    mods          = 'crop'
    primaryButton  = [
        'text'    => {lang 'common.save'},
        'classes' => 'js-crop-submit'
    ]}