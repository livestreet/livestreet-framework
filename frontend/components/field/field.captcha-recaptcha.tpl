{**
 * Каптча
 *}

{$component = 'ls-field'}
{component_define_params params=[ 'id', 'captchaName', 'name' ]}

{$uid = $id|default:($component|cat:rand(0, 10e10))}

<div class="{$component}" id="{$uid}" data-type="recaptcha" data-lsrecaptcha-captcha-name="{$captchaName}" data-lsrecaptcha-name="{$name}"></div>
<a href="#" id="{$uid}-reset">Обновить каптчу</a><br/>