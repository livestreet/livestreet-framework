<?php

/**
 * Запрещаем напрямую через браузер обращение к этому файлу.
 */
if (!class_exists('ModuleMigration_EntityAbstract')) {
    die('Hacking attempt!');
}

/**
 * Основной класс плагина
 */
class {{className}} extends ModuleMigration_EntityAbstract
{
    public function up() {

    }

    public function down() {

    }
}