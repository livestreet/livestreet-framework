{**
 * Каптча
 *}
{$type=$smarty.local.type}
{include "Component@field.captcha-{$type}"
	captchaName=$smarty.local.captchaName
	name=$smarty.local.name
	label=$smarty.local.label
}