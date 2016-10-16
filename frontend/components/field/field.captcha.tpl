{**
 * Каптча
 *}

{component_define_params params=[ 'label', 'captchaName', 'name', 'captchaType', 'mods', 'attributes', 'classes' ]}

{component 'field' template="captcha-{$captchaType}" params=$params}