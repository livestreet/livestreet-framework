{**
 * Каптча
 *}

{$component = 'ls-field'}
{component_define_params params=[ 'id', 'captchaName', 'name', 'mods', 'attributes', 'classes' ]}

{$uid = $id|default:($component|cat:rand(0, 10e10))}

<div class="{$component} {cmods name=$component mods=$mods} {$classes}" id="{$uid}" data-type="recaptcha" data-lsrecaptcha-captcha-name="{$captchaName}" data-lsrecaptcha-name="{$name}" {cattr list=$attributes}></div>
<a href="#" id="{$uid}-reset">Обновить каптчу</a><br/>