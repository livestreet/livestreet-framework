{**
 * Базовый шаблон поля формы
 *
 * @param string  $label     Текст лэйбла
 * @param string  $note      Подсказка (отображается под полем)
 *
 * @param object  $form
 * @param string  $rules     Правила валидации
 * @param object  $entity
 * @param object  $entityScenario
 *
 * @param boolean $escape   Экранировать параметр value или нет
 * @param string  $name      Имя поля (параметр name)
 * @param object  $placeholder
 * @param object  $isDisabled
 * @param object  $inputData
 * @param object  $inputClasses
 * @param object  $inputAttributes
 *}

{* Название компонента *}
{$component = 'ls-field'}

{component_define_params params=[ 'form', 'placeholder', 'isDisabled', 'entity', 'entityScenario', 'entityField', 'escape', 'data', 'label', 'name',
    'rules', 'value', 'id', 'inputClasses', 'inputAttributes', 'inputData', 'mods', 'classes', 'attributes', 'note' ]}

{* Уникальный ID *}
{$uid = $id|default:($component|cat:mt_rand())}

{* Дефолтные значения *}
{$rules = $rules|default:[]}
{$escape = $escape|default:true}
{$form = $form|default:$_aRequest}
{$getValueFromForm = true}

{* Правила валидации *}
{if $entity}
    {field_make_rule entity=$entity field=$entityField|default:$name scenario=$entityScenario assign=rules}
{/if}

{block 'field_options'}
    {* Получение значения атрибута value *}
    {if ! isset($value) && $getValueFromForm && $name && $form}
        {$value = {field_get_value form=$form name=$name}|default:$value}
    {/if}

    {* Escape *}
    {$value = ($escape) ? htmlspecialchars($value) : $value}
{/block}

{**
 * Общие атрибуты
 *}
{function field_input_attr_common useValue=true}
    id="{$uid}"
    class="{$component}-input {$inputClasses}"
    {if $useValue}value="{$value}"{/if}
    {if $name}name="{$name}"{/if}
    {if $placeholder}placeholder="{$placeholder}"{/if}
    {if $isDisabled}disabled{/if}
    {foreach $rules as $rule}
        {if is_bool( $rule@value ) && ! $rule@value}
            {continue}
        {/if}

        {if $rule@key === 'remote'}
            data-parsley-remote-validator="{$rules['remote-validator']|default:'livestreet'}"
            data-parsley-trigger="focusout"

            {* Default remote options *}
            {$json = [ 'type' => 'post', 'data' => [ 'security_ls_key' => $LIVESTREET_SECURITY_KEY ] ]}

            {if array_key_exists('remote-options', $rules)}
                {$json = array_merge_recursive($json, $rules['remote-options'])}
            {/if}

            data-parsley-remote-options='{json_encode($json)}'
        {/if}

        {if $rule@key === 'remote-options'}
            {continue}
        {/if}

        data-parsley-{$rule@key}="{$rule@value}"
    {/foreach}
    {cattr list=$inputAttributes}
    {cdata name=$component list=$inputData}
{/function}


{block 'field'}
    <div class="{$component} {cmods name=$component mods=$mods} ls-clearfix {$classes} {block 'field_classes'}{/block}"
        {cdata name=$component list=$data}
        {cattr list=$attributes}>

        {* Лэйбл *}
        {if $label}
            <label for="{$uid}" class="{$component}-label">{$label}</label>
        {/if}

        {* Блок с инпутом *}
        <div class="{$component}-holder">
            {block 'field_input'}{/block}
        </div>

        {* Подсказка *}
        {if $note}
            <small class="{$component}-note js-{$component}-note">{$note}</small>
        {/if}
    </div>
{/block}
