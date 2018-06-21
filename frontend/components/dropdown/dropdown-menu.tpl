{**
 * Выпадающее меню
 *
 * @param string name
 * @param string text
 * @param string activeItem
 * @param array  items
 *}

{component_define_params params=[ 'items', 'name', 'text', 'activeItem', 'mods', 'classes', 'attributes', 'isSubnav' ]}

{component 'nav'
    isSubnav=$isSubnav
    name       = ( $name ) ? "{$name}_menu" : ''
    activeItem = $activeItem
    mods       = 'stacked dropdown'
    showSingle = true
    classes    = "ls-dropdown-menu js-ls-dropdown-menu {$classes}"
    attributes = array_merge( $attributes|default:[], [
        'role' => 'menu',
        'aria-hidden' => 'true'
    ])
    items      = $items}