{**
 * Каптча
 *}

{component_define_params params=[ 'label', 'captchaName', 'name', 'type' ]}

{component 'field' template="captcha-{$type}"
    captchaName=$captchaName
    name=$name
    label=$label}