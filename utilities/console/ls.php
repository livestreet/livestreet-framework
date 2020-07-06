<?php

// Для эмуляции работы, т.к используется в конфиге
$_SERVER['HTTP_HOST'] = 'localhost';
/**
 * Подключаем ядро
 */
require_once(dirname(dirname(dirname(__DIR__))) . '/bootstrap/start.php');
/**
 * Инициализация загрузчика ядра
 */
Engine::getInstance();
/**
 * Команды
 */
require_once(__DIR__ . '/commands/base.php');
require_once(__DIR__ . '/commands/plugin.php');
require_once(__DIR__ . '/commands/migration.php');
/**
 * Запускаем консоль
 */
$console = new \ConsoleKit\Console(array(
    'plugin'    => 'LsConsoleCommandPlugin',
    'migration' => 'LsConsoleCommandMigration'
));
$console->run();