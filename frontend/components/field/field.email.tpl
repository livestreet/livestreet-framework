{**
 * E-mail
 *}

{extends 'component@field.text'}

{block 'field_options' append}
    {$name = $name|default:'mail'}
    {$label = $label|default:{lang name='field.email.label'}}
    {$rules = array_merge([ 'required' => true, 'type'=> 'email' ], $rules)}
{/block}