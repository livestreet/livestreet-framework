{**
 * Каптча
 *}

{$component = 'ls-field'}
{$uid = $smarty.local.id|default:($component|cat:rand(0, 10e10))}

<div class="{$component}" id="{$uid}" data-type="recaptcha" data-lsrecaptcha-captcha-name="{$smarty.local.captchaName}" data-lsrecaptcha-name="{$smarty.local.name}"></div>
<a href="#" id="{$uid}-reset">Обновить каптчу</a><br/>