{**
 * Каптча
 *}

{extends 'component@field.text'}

{block 'field_input' prepend}
    <span data-type="captcha" data-lscaptcha-name="{$smarty.local.captchaName}" class="ls-field--captcha-image"></span>

    {$rules = [
        'required'      => true,
        'remote'        => {router page='ajax/captcha/validate'},
        'remote-options' => [ 'data' => [ 'name' => $smarty.local.captchaName ] ]
    ]}

    {$inputClasses = "$inputClasses ls-width-100"}
{/block}
