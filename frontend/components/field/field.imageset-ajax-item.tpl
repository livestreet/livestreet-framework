{**
 *  Изображение фотосета
 *}


{$component = 'ls-field-imageset-ajax-item'}
{component_define_params params=[ 'src', 'id', 'mods', 'classes', 'attributes' ]}

{if $id}
    {$attributes[] = ['data-id' => $id]}
{/if}

<div class="{$component} {cmods name=$component mods=$mods} js-field-imageset-ajax-image" {cattr list=$attributes} >
            <img  src="{$src}">
    {component 'button'
        type    = 'button'
        text    = {lang 'common.remove'}
        classes = "js-imageset-item-but-remove"}
</div>
