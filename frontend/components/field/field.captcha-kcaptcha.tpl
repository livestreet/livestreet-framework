{**
 * Каптча
 *}

{extends 'component@field.text'}

{block 'field_options' append}
    {component_define_params params=[ 'captchaName' ]}
{/block}

{block 'field_input' prepend}
    <span data-type="captcha" data-lscaptcha-name="{$captchaName}" class="ls-field--captcha-image"></span>

    {$rules = [
        'required' => true
    ]}

    {$inputClasses = "$inputClasses ls-width-100"}
{/block}
