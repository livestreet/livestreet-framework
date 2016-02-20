{**
 * Drag & drop загрузка
 *}

{$component = 'ls-field-upload-area'}
{component_define_params params=[ 'isMultiple', 'inputAttributes', 'inputClasses', 'inputName', 'label', 'mods', 'classes', 'attributes' ]}

<label class="{$component} {cmods name=$component mods=$mods} {$classes}" {cattr list=$attributes}>
    <span>{$label|default:{lang name='field.upload_area.label'}}</span>
    <input type="file" name="{$inputName|default:'file'}" class="{$inputClasses}" {cattr list=$inputAttributes} {$isMultiple|default:'multiple'}>
</label>