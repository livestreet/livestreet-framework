<?php
/*-------------------------------------------------------
*
*   LiveStreet Engine Social Networking
*   Copyright © 2008 Mzhelskiy Maxim
*
*--------------------------------------------------------
*
*   Official site: www.livestreet.ru
*   Contact e-mail: rus.engine@gmail.com
*
*   GNU General Public License, version 2:
*   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*
---------------------------------------------------------
*/

/**
 * Основные константы
 */
define('LS_VERSION_FRAMEWORK', '2.0.0');
/**
 * Operations with Config object
 */
require_once(dirname(dirname(__FILE__)) . "/libs/application/ConfigSimple/Config.class.php");
/**
 * Загружаем основной конфиг фреймворка
 */
Config::LoadFromFile(dirname(__FILE__) . '/config.php');
/**
 * Загружаем основной конфиг приложения
 */
Config::LoadFromFile(Config::Get('path.application.server') . '/config/config.php', false);
/**
 * Вспомогательная функция
 */
$fGetConfig = create_function('$sPath', '$config=array(); return include $sPath;');
/**
 * Получаем текущее окружение
 */
$sEnvironmentCurrent = Engine::GetEnvironment();

/**
 * Инклудим все *.php файлы из каталога {path.root.framework}/include/ - это файлы ядра
 */
$sDirInclude = Config::get('path.framework.server') . '/include/';
if ($hDirInclude = opendir($sDirInclude)) {
    while (false !== ($sFileInclude = readdir($hDirInclude))) {
        $sFileIncludePathFull = $sDirInclude . $sFileInclude;
        if ($sFileInclude != '.' and $sFileInclude != '..' and is_file($sFileIncludePathFull)) {
            $aPathInfo = pathinfo($sFileIncludePathFull);
            if (isset($aPathInfo['extension']) and strtolower($aPathInfo['extension']) == 'php') {
                require_once($sDirInclude . $sFileInclude);
            }
        }
    }
    closedir($hDirInclude);
}

/**
 * Загружает конфиги модулей вида /config/modules/[module_name]/config.php
 */
$sDirConfig = Config::get('path.application.server') . '/config/modules/';
if (is_dir($sDirConfig) and $hDirConfig = opendir($sDirConfig)) {
    while (false !== ($sDirModule = readdir($hDirConfig))) {
        if ($sDirModule != '.' and $sDirModule != '..' and is_dir($sDirConfig . $sDirModule)) {
            $sFileConfig = $sDirConfig . $sDirModule . '/config.php';
            if (file_exists($sFileConfig)) {
                $aConfig = $fGetConfig($sFileConfig);
                if (!empty($aConfig) && is_array($aConfig)) {
                    // Если конфиг этого модуля пуст, то загружаем массив целиком
                    $sKey = "module.$sDirModule";
                    if (!Config::isExist($sKey)) {
                        Config::Set($sKey, $aConfig);
                    } else {
                        // Если уже существую привязанные к модулю ключи,
                        // то сливаем старые и новое значения ассоциативно
                        Config::Set(
                            $sKey,
                            func_array_merge_assoc(Config::Get($sKey), $aConfig)
                        );
                    }
                }
            }
        }
    }
    closedir($hDirConfig);
}

/**
 * Инклудим все *.php файлы из каталога {path.root.application}/include/ - пользовательские файлы
 */
$sDirInclude = Config::get('path.application.server') . '/include/';
if (is_dir($sDirInclude) and $hDirInclude = opendir($sDirInclude)) {
    while (false !== ($sFileInclude = readdir($hDirInclude))) {
        $sFileIncludePathFull = $sDirInclude . $sFileInclude;
        if ($sFileInclude != '.' and $sFileInclude != '..' and is_file($sFileIncludePathFull)) {
            $aPathInfo = pathinfo($sFileIncludePathFull);
            if (isset($aPathInfo['extension']) and strtolower($aPathInfo['extension']) == 'php') {
                require_once($sDirInclude . $sFileInclude);
            }
        }
    }
    closedir($hDirInclude);
}

/**
 * Подгружаем конфиг окружения
 */
if (file_exists(Config::Get('path.application.server') . "/config/config.{$sEnvironmentCurrent}.php")) {
    Config::LoadFromFile(Config::Get('path.application.server') . "/config/config.{$sEnvironmentCurrent}.php", false);
}

/**
 * Загружает конфиги плагинов вида /plugins/[plugin_name]/config/*.php
 * и include-файлы /plugins/[plugin_name]/include/*.php
 */
$sPluginsDir = Config::Get('path.application.plugins.server');
$sPluginsListFile = $sPluginsDir . '/' . Config::Get('sys.plugins.activation_file');
if ($aPluginsList = @file($sPluginsListFile)) {
    $aPluginsList = array_map('trim', $aPluginsList);
    foreach ($aPluginsList as $sPlugin) {
        $aConfigFiles = glob($sPluginsDir . '/' . $sPlugin . '/config/*.php');
        if ($aConfigFiles and count($aConfigFiles) > 0) {
            foreach ($aConfigFiles as $sPath) {
                $aConfig = $fGetConfig($sPath);
                if (!empty($aConfig) && is_array($aConfig)) {
                    // Если конфиг этого плагина пуст, то загружаем массив целиком
                    $sKey = "plugin.$sPlugin";
                    if (!Config::isExist($sKey)) {
                        Config::Set($sKey, $aConfig);
                    } else {
                        // Если уже существую привязанные к плагину ключи,
                        // то сливаем старые и новое значения ассоциативно
                        Config::Set(
                            $sKey,
                            func_array_merge_assoc(Config::Get($sKey), $aConfig)
                        );
                    }
                }
            }
        }
        /**
         * Смотрим конфиг плагина в /application/config/plugins/[plugin_name]/config.php
         */
        $sFileUserConfig = Config::get('path.application.server') . "/config/plugins/{$sPlugin}/config.php";
        if (file_exists($sFileUserConfig)) {
            $aConfig = $fGetConfig($sFileUserConfig);
            // Если конфиг этого плагина пуст, то загружаем массив целиком
            $sKey = "plugin.$sPlugin";
            if (!Config::isExist($sKey)) {
                Config::Set($sKey, $aConfig);
            } else {
                // Если уже существую привязанные к плагину ключи,
                // то сливаем старые и новое значения ассоциативно
                Config::Set(
                    $sKey,
                    func_array_merge_assoc(Config::Get($sKey), $aConfig)
                );
            }
        }
        /**
         * Смотрим конфиг плагина текущего окружения в /application/config/plugins/[plugin_name]/config.[environment].php
         */
        $sFileUserConfig = Config::get('path.application.server') . "/config/plugins/{$sPlugin}/config.{$sEnvironmentCurrent}.php";
        if (file_exists($sFileUserConfig)) {
            $aConfig = $fGetConfig($sFileUserConfig);
            // Если конфиг этого плагина пуст, то загружаем массив целиком
            $sKey = "plugin.$sPlugin";
            if (!Config::isExist($sKey)) {
                Config::Set($sKey, $aConfig);
            } else {
                // Если уже существую привязанные к плагину ключи,
                // то сливаем старые и новое значения ассоциативно
                Config::Set(
                    $sKey,
                    func_array_merge_assoc(Config::Get($sKey), $aConfig)
                );
            }
        }
        /**
         * Подключаем include-файлы
         */
        $aIncludeFiles = glob($sPluginsDir . '/' . $sPlugin . '/include/*.php');
        if ($aIncludeFiles and count($aIncludeFiles)) {
            foreach ($aIncludeFiles as $sPath) {
                require_once($sPath);
            }
        }
    }
}
