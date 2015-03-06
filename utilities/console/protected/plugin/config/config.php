<?php
/**
 * Конфиг плагина
 */

/**
 * Переопределение роутера на наш новый Action - добавляем свой урл  http://domain.com/example
 * Обратите внимание на '$root$' - говорит о том, что конфиг применяется к корневым настройкам движка, а не плагина
 *
 */
//$config['$root$']['router']['page']['example'] = 'PluginExample_ActionIndex';

/**
 * Пример параметра с количеством элементов на страницу
 * В плагине к нему можно получить доступ через Config::Get('plugin.example.per_page')
 */
$config['per_page'] = 15;

/**
 * Добавляем новую таблицу в конфиг, она будет автоматически подхвачена ORM механизмом
 * Параметр формируется как [plugin]_[module]_[entity]
 */
$config['$root$']['db']['table']['example_main_some'] = '___db.table.prefix___example_some';

/**
 * Добавляем вывод блока на главную страницу
 */
$config['$root$']['block']['rule_index_blog'] = array(
    'action' => array(
        // Экшен Index
        'index',
    ),
    'blocks' => array(
        'right' => array(
            // Блок Main
            'main' => array(
                'params'   => array('plugin' => 'example'),
                'priority' => 1000
            )
        )
    ),
    'clear'  => false,
);

return $config;