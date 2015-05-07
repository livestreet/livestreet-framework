{**
 * Каптча
 *
 * @scripts <framework>/js/livestreet/recaptcha.js
 *}

{$component = 'ls-field'}
{$_uid = $smarty.local.id|default:($component|cat:rand(0, 10e10))}

<div class="{$component}" id="{$_uid}" data-type="recaptcha" data-lsrecaptcha-captcha-name="{$smarty.local.captchaName}" data-lsrecaptcha-name="{$smarty.local.name}"></div>
<a href="#" id="{$_uid}-reset">Обновить каптчу</a><br/>