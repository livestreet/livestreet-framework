{**
 * Каптча
 *}

{component_define_params params=[ 'label', 'captchaName', 'name', 'type', 'mods', 'attributes', 'classes' ]}

{component 'field' template="captcha-{$type}" params=$params}