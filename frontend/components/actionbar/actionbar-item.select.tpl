{**
 * Экшнбар / Контрол выбора объектов
 *
 * @param string $target
 * @param string $items
 *}

{component_define_params params=[ 'target', 'items', 'mods', 'classes', 'attributes' ]}

{* Дефолтные пункты меню *}
{$menu = [
    [ 'name' => 'all',      'text' => {lang 'actionbar.select.menu.all'},      'data' => [ 'select-item' => $target ] ],
    [ 'name' => 'deselect', 'text' => {lang 'actionbar.select.menu.deselect'}, 'data' => [ 'select-item' => $target, 'select-filter' => ':not(*)' ] ],
    [ 'name' => 'invert',   'text' => {lang 'actionbar.select.menu.invert'},   'data' => [ 'select-item' => $target, 'select-filter' => ':not(.selected)' ] ],
    [ 'name' => '-',        'is_enabled' => !! $items ]
]}

{* Добавляем кастомные пункты меню *}
{foreach $items as $item}
    {$menu[] = [
        'text' => $item['text'],
        'data' => [ 'select-item' => $target, 'select-filter' => $item['filter'] ]
    ]}
{/foreach}

{* Выпадающее меню *}
{component 'dropdown'
    classes = "ls-actionbar-item-link {$classes}"
    text    = {lang 'actionbar.select.title'}
    menu    = $menu
    params  = $params}