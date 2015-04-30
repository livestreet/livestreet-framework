{**
 * Обрезка загруженного изображения
 *
 * @param string $title
 * @param string $desc
 * @param string $image
 * @param integer $width
 * @param integer $height
 * @param integer $originalWidth
 * @param integer $originalHeight
 *
 * TODO: Возможность задавать размеры превью
 * TODO: Разделить на модальное и на контейнер с кропом
 *}

{capture 'modal_content'}
    {$desc = $smarty.local.desc|escape}
    {$usePreview = $smarty.local.usePreview}
    {$componentCrop = 'ls-crop'}

    {if $desc}
        <p class="{$componentCrop}-desc">{$desc}</p>
    {/if}

    {$image = "{$smarty.local.image|escape}?v{rand( 0, 10e10 )}"}

    <div class="{$componentCrop} js-crop" data-crop-width="{$smarty.local.originalWidth}" data-crop-height="{$smarty.local.originalHeight}">
        {* Изображение *}
        <div class="{$componentCrop}-image-holder js-crop-image-holder">
            <img src="{$image}" width="{$smarty.local.width}" height="{$smarty.local.height}" class="{$componentCrop}-image js-crop-image">
        </div>

        {* Превью *}
        {if $usePreview}
            <div class="{$componentCrop}-previews js-crop-previews">
                {foreach [ 100, 64, 48 ] as $size}
                    <div style="width: {$size}px; height: {$size}px;" class="{$componentCrop}-preview js-crop-preview">
                        <img src="{$image}" class="js-crop-preview-image" data-size="{$size}">
                    </div>
                {/foreach}
            </div>
        {/if}
    </div>
{/capture}

{component 'modal'
    title         = $smarty.local.title|escape|default:{lang 'crop.title'}
    content       = $smarty.capture.modal_content
    mods          = 'uploader'
    primaryButton  = [
        'text'    => {lang 'common.save'},
        'classes' => 'js-crop-submit'
    ]}