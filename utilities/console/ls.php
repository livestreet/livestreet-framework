<?php

// Для эмуляции работы, т.к используется в конфиге
$_SERVER['HTTP_HOST']='localhost';
/**
 * Формируем путь до фреймворка
 */
$sPathToFramework=dirname(dirname(__DIR__));
/**
 * Подключаем ядро
 */
require_once($sPathToFramework."/classes/engine/Engine.class.php");
/**
 * Подключаем загрузчик конфигов
 */
require_once($sPathToFramework."/config/loader.php");

require_once(__DIR__.'/lsc.php');


LSC::Start();
