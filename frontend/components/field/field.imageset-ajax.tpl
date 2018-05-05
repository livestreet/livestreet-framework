{**
 * Выбор файла
 *}


{$component = 'ls-field-imageset-ajax'}
{component_define_params params=[ 'targetType', 'targetId', 'modalTitle', 'label', 'mods', 'classes', 'attributes', 'images' ]}

<div class="{$component} {cmods name=$component mods=$mods} {$classes} fieldset" {cattr list=$attributes}
    data-param-target_type="{$targetType}"
    data-param-target_id="{$targetId}">

    <div class="fieldset-header">
        <h2 class="fieldset-title">{$label}</h2>
    </div>

    <div class="fieldset-body">
        
        {component 'field.imageset-ajax-item' classes="js-imageset-template-item" attributes=['style' => 'display:none;']}
        {foreach $images as $image}
            {component 'field.imageset-ajax-item' params=$image}
        {/foreach}

        {component 'button'
            type    = 'button'
            text    = {lang 'common.add'}
            classes = "js-{$component}-but-show-modal" attributes=[ 'style' => ( $imagePreviewItems ) ? 'display: none' : '' ]}

        {component 'uploader' template='modal'
            classes = "js-{$component}-but-modal"
            title   = $modalTitle}
            
        
    </div>
</div>