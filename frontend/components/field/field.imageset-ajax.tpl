{**
 * Выбор файла
 *}

{extends 'component@field.field'}

{block 'field_options' append}
    {component_define_params params=[ 'targetType', 'targetId', 'modalTitle', 'label', 'mods', 'classes', 'images' ]}
    {$value=$targetId}
    
    {$attributes['data-imageset-count'] = null}
    
    {$nameTarget=$name}
    
    {$name="imageset_count"}

    {$mods = "$mods imageset"}
{/block}

{block 'field_input'}
    
    <div class="{$component}-imageset-ajax js-field-imageset-ajax {cmods name=$component mods=$mods} {$classes} fieldset" {cattr list=$attributes}
        data-param-target_type="{$targetType}"
        data-param-target_id="{$targetId}">


        <div class="fieldset-body">

            <ul class="{$component}-imageset-ajax-container js-field-imageset-items">

            </ul>

            {component 'button'
                type    = 'button'
                text    = {lang 'common.add'}
                classes = "js-field-imageset-but-show-modal" attributes=[ 'style' => ( $imagePreviewItems ) ? 'display: none' : '' ]}

            {component 'uploader' template='modal'
                classes = "js-field-imageset-modal"
                title   = $modalTitle}

            {component 'field.hidden' name=$nameTarget value=$value attributes = ['data-imageset-input' => null]}

            <input type="text" class="field-count-image" style="display: none;" value="0" {field_input_attr_common useValue=false} />

        </div>
    </div>

    
{/block}
