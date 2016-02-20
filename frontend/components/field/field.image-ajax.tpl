{**
 * Выбор файла
 *}


{$component = 'ls-field-image-ajax'}
{component_define_params params=[ 'targetType', 'targetId', 'modalTitle', 'label', 'mods', 'classes', 'attributes' ]}

<div class="{$component} {cmods name=$component mods=$mods} {$classes} fieldset" {cattr list=$attributes}
    data-param-target_type="{$targetType}"
    data-param-target_id="{$targetId}"
    {if $imagePreviewItems}data-param-id="{$imagePreviewItems[0]->getMediaId()}"{/if}>

    <div class="fieldset-header">
        <h2 class="fieldset-title">{$label}</h2>
    </div>

    <div class="fieldset-body">
        <div class="{$component}-image js-field-image-ajax-image" {( ! $imagePreviewItems ) ? 'style="display: none"' : false}>
            {component 'field' template='image-ajax-items' imagePreviewItems=$imagePreviewItems}
        </div>

        {component 'button'
            type    = 'button'
            text    = {lang 'common.remove'}
            classes = 'js-field-image-ajax-remove' attributes=[ 'style' => ( ! $imagePreviewItems ) ? 'display: none' : '' ]}

        {component 'button'
            type    = 'button'
            text    = {lang 'common.choose'}
            classes = 'js-field-image-ajax-show-modal' attributes=[ 'style' => ( $imagePreviewItems ) ? 'display: none' : '' ]}

        {component 'uploader' template='modal'
            classes = 'js-field-image-ajax-modal'
            title   = $modalTitle}
    </div>
</div>