<?php
/*
 * LiveStreet CMS
 * Copyright © 2013 OOO "ЛС-СОФТ"
 *
 * ------------------------------------------------------
 *
 * Official site: www.livestreetcms.com
 * Contact e-mail: office@livestreetcms.com
 *
 * GNU General Public License, version 2:
 * http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 *
 * ------------------------------------------------------
 *
 * @link http://www.livestreetcms.com
 * @copyright 2013 OOO "ЛС-СОФТ"
 * @author Maxim Mzhelskiy <rus.engine@gmail.com>
 *
 */

/**
 * Модуль управления компонентами frontenda'а - независимые единицы (кирпичики) шаблона, состоящие из tpl, css, js
 *
 * @package framework.modules
 * @since 2.0
 */
class ModuleComponent extends Module
{

    /**
     * Список компонентов для подключения
     * В качестве ключей указывается название компонента, а в значениях возможные параметры
     *
     * @var array
     */
    protected $aComponentsList = array();
    /**
     * Кеш для json данных компонентов
     *
     * @var array
     */
    protected $aComponentsData = array();
    /**
     * Служебный счетчик для предотвращения зацикливания
     *
     * @var int
     */
    protected $iCountDependsRecursive = 0;

    /**
     * Инициализация модуля
     */
    public function Init()
    {
        $this->InitComponentsList();
    }

    /**
     * Инициализация начального списка необходимых для загрузки компонентов
     */
    public function InitComponentsList()
    {
        if ($aList = Config::Get('components') and is_array($aList)) {
            func_array_simpleflip($aList, array());
            $this->aComponentsList = array_merge_recursive($this->aComponentsList, $aList);
        }
    }

    /**
     * Выполняет загрузку необходимых компонентов
     * Под загрузкой понимается автоматическое подключение необходимых css, js
     */
    public function LoadAll()
    {
        /**
         * Строим дерево компонентов с учетом зависимостей
         */
        $aTree = array();
        /**
         * Для каждого компонента считываем данные из json
         */
        $aComponentsName = array_keys($this->aComponentsList);
        foreach ($aComponentsName as $sName) {
            $aTree[$sName] = array();
            /**
             * Считываем данные из json файла компонента
             */
            $aData = $this->GetJsonData($sName);
            /**
             * Проверяем зависимости
             */
            if (isset($aData['dependencies']) and is_array($aData['dependencies'])) {
                foreach ($aData['dependencies'] as $mKey => $mValue) {
                    $aTree[$sName][] = strtolower(is_int($mKey) ? $mValue : $mKey);
                }
            }
        }
        /**
         * Сортируем компоненты с учетом зависимостей
         */
        $this->iCountDependsRecursive = 0;
        $aTree = $this->GetSortedByDepends($aTree);
        /**
         * Подключаем каждый компонент
         */
        foreach ($aTree as $sName => $aDepends) {
            $this->Load($sName);
        }
    }

    /**
     * Загружает/подключает компонент
     *
     * @param $sName
     */
    public function Load($sName)
    {
        /**
         * Получаем путь до компонента
         */
        $sPath = $this->GetPath($sName);
        /**
         * Json данные
         */
        $aData = $this->GetJsonData($sName);
        /**
         * Подключаем стили
         */
        if (isset($aData['styles']) and is_array($aData['styles'])) {
            foreach ($aData['styles'] as $mName => $mAsset) {
                $aParams = array();
                if (is_array($mAsset)) {
                    $sAsset = isset($mAsset['file']) ? $mAsset['file'] : 'not_found_file_param';
                    unset($mAsset['file']);
                    $aParams = $mAsset;
                } else {
                    $sAsset = $mAsset;
                }
                $sFile = $sPath . '/' . $sAsset;
                $sFileName = (is_int($mName) ? $sAsset : $mName);
                $aParams['name'] = "component.{$sName}.{$sFileName}";
                $this->Viewer_PrependStyle($sFile, $aParams);
            }
        }
        /**
         * Подключаем скрипты
         */
        if (isset($aData['scripts']) and is_array($aData['scripts'])) {
            foreach ($aData['scripts'] as $mName => $mAsset) {
                $aParams = array();
                if (is_array($mAsset)) {
                    $sAsset = isset($mAsset['file']) ? $mAsset['file'] : 'not_found_file_param';
                    unset($mAsset['file']);
                    $aParams = $mAsset;
                } else {
                    $sAsset = $mAsset;
                }
                $sFile = $sPath . '/' . $sAsset;
                $sFileName = (is_int($mName) ? $sAsset : $mName);
                $aParams['name'] = "component.{$sName}.{$sFileName}";
                $this->Viewer_PrependScript($sFile, $aParams);
            }
        }
    }

    /**
     * Добавляет новый компонент в список для загрузки
     *
     * @param $sName
     * @param $aParams
     */
    public function Add($sName, $aParams = array())
    {
        $sName = strtolower($sName);
        if (!array_key_exists($sName, $this->aComponentsList)) {
            $this->aComponentsList[$sName] = $aParams;
        }
    }

    /**
     * Возвращает полный серверный путь до компонента
     *
     * @param string $sName Имя компонента. Может содержать название плагина, например, "page:alert" - компонент alert плагина page
     * @return string
     */
    public function GetPath($sName)
    {
        list($sPlugin, $sName) = $this->ParseName($sName);
        $sPath = 'components/' . $sName;
        if ($sPlugin) {
            /**
             * Проверяем наличие компонента в каталоге текущего шаблона плагина
             */
            $sPathTemplate = Plugin::GetTemplatePath($sPlugin);
            if (file_exists($sPathTemplate . $sPath)) {
                return $sPathTemplate . $sPath;
            }
            /**
             * Проверяем наличие компонента в общем каталоге плагина
             */
            $sPathTemplate = Config::Get('path.application.plugins.server') . "/{$sPlugin}/frontend";
            if (file_exists($sPathTemplate . '/' . $sPath)) {
                return $sPathTemplate . '/' . $sPath;
            }
        } else {
            /**
             * Проверяем наличие компонента в каталоге текущего шаблона
             */
            $sPathTemplate = $this->Fs_GetPathServerFromWeb(Config::Get('path.skin.web'));
            if (file_exists($sPathTemplate . '/' . $sPath)) {
                return $sPathTemplate . '/' . $sPath;
            }
        }

        /**
         * Проверяем на компонент приложения
         */
        $sPathTemplate = Config::Get('path.application.server') . '/frontend';
        if (file_exists($sPathTemplate . '/' . $sPath)) {
            return $sPathTemplate . '/' . $sPath;
        }
        /**
         * Проверяем на компонент фреймворка
         */
        $sPathTemplate = Config::Get('path.framework.server') . '/frontend';
        if (file_exists($sPathTemplate . '/' . $sPath)) {
            return $sPathTemplate . '/' . $sPath;
        }

        /**
         * Не удалось найти компонент
         */
        return false;
    }

    /**
     * Возвращает полный web путь до компонента с учетом текущей схемы (http/https)
     *
     * @param $sName
     * @return bool
     */
    public function GetWebPath($sName)
    {
        if ($sPathServer = $this->GetPath($sName)) {
            return $this->Fs_GetPathWebFromServer($sPathServer);
        }
        return false;
    }

    /**
     * Возвращает путь до шаблона
     * Путь может быть как абсолютным, так и относительным корня шаблона
     * Метод учитывает возможное наследование плагинами, а также учитывает приоритет шаблона (tpl шаблона -> application -> framework)
     *
     * @param $sNameFull
     * @param $sTemplate
     * @param $bCheckDelegate
     * @return string
     */
    public function GetTemplatePath($sNameFull, $sTemplate = null, $bCheckDelegate = true)
    {
        list($sPlugin, $sName) = $this->ParseName($sNameFull);
        /**
         * По дефолту используем в качестве имени шаблона название компонента
         */
        if (!$sTemplate) {
            $sTemplate = $sName;
        }
        if ($bCheckDelegate) {
            /**
             * Базовое название компонента
             */
            $sNameBase = ($sPlugin ? "{$sPlugin}:" : '') . "component.{$sName}.{$sTemplate}";
            /**
             * Проверяем наследование по базовому имени
             */
            $sNameBaseInherit = $this->Plugin_GetDelegate('template', $sNameBase);
            if ($sNameBaseInherit != $sNameBase) {
                return $sNameBaseInherit;
            }
        }
        /**
         * Компонент не наследуется, поэтому получаем до него полный серверный путь
         */
        if ($sPath = $this->GetPath($sNameFull)) {
            /**
             * Получаем путь до файла шаблона из json
             */
            $aData = $this->GetJsonData($sNameFull);
            if (isset($aData['templates'][$sTemplate])) {
                return "{$sPath}/" . $aData['templates'][$sTemplate];
            }
            return "{$sPath}/{$sTemplate}.tpl";
        }
        return false;
    }

    /**
     * Возвращает полный серверный путь до css/js компонента
     *
     * @param $sNameFull
     * @param $sAssetType
     * @param $sAssetName
     * @return bool|string
     */
    public function GetAssetPath($sNameFull, $sAssetType, $sAssetName)
    {
        if ($sPath = $this->GetPath($sNameFull)) {
            if (in_array($sAssetType, array('scripts', 'js'))) {
                $sAssetType = 'scripts';
                $sAssetExt = 'js';
            } else {
                $sAssetType = 'styles';
                $sAssetExt = 'css';
            }
            /**
             * Получаем путь до файла из json
             */
            $aData = $this->GetJsonData($sNameFull);
            if (isset($aData[$sAssetType][$sAssetName])) {
                return "{$sPath}/" . $aData[$sAssetType][$sAssetName];
            }
            return "{$sPath}/{$sAssetName}.{$sAssetExt}";
        }
        return false;
    }

    /**
     * Парсит имя компонента
     * Имя может содержать название плагина - plugin:component
     *
     * @param $sName
     * @return array Массив из двух элементов, первый - имя плагина, воторой - имя компонента. Если плагина нет, то null вместо его имени
     */
    protected function ParseName($sName)
    {
        $sName = strtolower($sName);
        $aPath = explode(':', $sName);
        if (count($aPath) == 2) {
            return array($aPath[0], $aPath[1]);
        }
        //if (preg_match("#^\{([\w_-]+)\}/?([\w_-]+)$#", $sName, $aMatch)) {
        return array(null, $sName);
    }

    /**
     * Вспомогательный метод для сортировки компонентов по зависимостям
     *
     * @param $aComp
     * @param $aSorted
     * @param $sName
     * @return bool
     */
    protected function GetDepends($aComp, $aSorted, $sName)
    {
        if (isset($aComp[$sName])) {
            foreach ($aComp[$sName] as $sItem) {
                if (!isset($aSorted[$sItem])) {
                    $this->iCountDependsRecursive++;
                    if ($this->iCountDependsRecursive > 2000) {
                        return false;
                    } else {
                        return $this->GetDepends($aComp, $aSorted, $sItem);
                    }
                }
            }
        }
        return $sName;
    }

    /**
     * Сортирует компоненты по зависимостям - зависимые подключаются ниже
     *
     * @param $aComp
     * @return array|bool
     */
    protected function GetSortedByDepends($aComp)
    {
        $aSorted = array();
        foreach ($aComp as $sName => $void) {
            do {
                if ($sCompDepend = $this->GetDepends($aComp, $aSorted, $sName)) {
                    if (isset($aComp[$sCompDepend])) {
                        $aSorted[$sCompDepend] = $aComp[$sCompDepend];
                    } else {
                        $aSorted[$sCompDepend] = array();
                    }
                } else {
                    $aSorted = false;
                    break;
                }
            } while ($sCompDepend != $sName);
        }
        return $aSorted;
    }

    /**
     * Парсит и возвращает json данные компонента
     *
     * @param $sName
     * @return array
     */
    protected function GetJsonData($sName)
    {
        /**
         * Смотрим в кеше
         */
        if (isset($this->aComponentsData[$sName])) {
            return $this->aComponentsData[$sName];
        }
        /**
         * Считываем данные из json файла компонента
         */
        $sPath = $this->GetPath($sName);
        $sFileJson = $sPath . '/component.json';
        if (file_exists($sFileJson)) {
            if ($sContent = @file_get_contents($sFileJson) and $aData = @json_decode($sContent, true)) {
                return $this->aComponentsData[$sName] = $aData;
            }
        }
        return array();
    }
}
