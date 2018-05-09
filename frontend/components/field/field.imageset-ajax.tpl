{**
 * Выбор файла
 *}


{$component = 'ls-field-imageset-ajax'}
{component_define_params params=[ 'targetType', 'targetId', 'modalTitle', 'label', 'mods', 'classes', 'attributes', 'images', 'name' ]}

<div class="{$component} js-field-imageset-ajax {cmods name=$component mods=$mods} {$classes} fieldset" {cattr list=$attributes}
    data-param-target_type="{$targetType}"
    data-param-target_id="{$targetId}">

    <div class="fieldset-header">
        <h2 class="fieldset-title">{$label}</h2>
    </div>

    <div class="fieldset-body">
        
        <ul class="{$component}-container js-field-imageset-items">
            
        </ul>

        {component 'button'
            type    = 'button'
            text    = {lang 'common.add'}
            classes = "js-field-imageset-but-show-modal" attributes=[ 'style' => ( $imagePreviewItems ) ? 'display: none' : '' ]}

        {component 'uploader' template='modal'
            classes = "js-field-imageset-modal"
            title   = $modalTitle}
            
        {component 'field.hidden' name=$name value=$targetId attributes=['data-imageset-input' => null]}
    </div>
</div>