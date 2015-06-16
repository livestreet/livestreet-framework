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
     * Кеш для данных компонентов - json и каталоги
     * Для каждого компонента есть ключи paths и json
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
             * Считываем данные компонента
             */
            $aData = $this->GetComponentData($sName);
            $aData = $aData['json'];
            /**
             * Проверяем зависимости
             */
            if (isset($aData['dependencies']) and is_array($aData['dependencies'])) {
                foreach ($aData['dependencies'] as $mKey => $mValue) {
                    if (!is_int($mKey) and $mValue === false) {
                        /**
                         * Пропускаем отмененную зависимость
                         */
                        continue;
                    }
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
         * Json данные
         */
        $aData = $this->GetComponentData($sName);
        $aDataMeta = $aData['json'];
        /**
         * Подключаем стили
         */
        if (isset($aDataMeta['styles']) and is_array($aDataMeta['styles'])) {
            foreach ($aDataMeta['styles'] as $mName => $mAsset) {
                $aParams = array();
                if (is_array($mAsset)) {
                    $sAsset = isset($mAsset['file']) ? $mAsset['file'] : 'not_found_file_param';
                    unset($mAsset['file']);
                    $aParams = $mAsset;
                } else {
                    $sAsset = $mAsset;
                }
                if ($sAsset === false) {
                    continue;
                }
                /**
                 * Смотрим в каком каталоге есть файл
                 */
                foreach ($aData['paths'] as $sPath) {
                    $sFile = $sPath . '/' . $sAsset;
                    if (file_exists($sFile)) {
                        break;
                    }
                }
                $sFileName = (is_int($mName) ? $sAsset : $mName);
                $aParams['name'] = "component.{$sName}.{$sFileName}";
                $this->Viewer_PrependStyle($sFile, $aParams);
            }
        }
        /**
         * Подключаем скрипты
         */
        if (isset($aDataMeta['scripts']) and is_array($aDataMeta['scripts'])) {
            foreach ($aDataMeta['scripts'] as $mName => $mAsset) {
                $aParams = array();
                if (is_array($mAsset)) {
                    $sAsset = isset($mAsset['file']) ? $mAsset['file'] : 'not_found_file_param';
                    unset($mAsset['file']);
                    $aParams = $mAsset;
                } else {
                    $sAsset = $mAsset;
                }
                if ($sAsset === false) {
                    continue;
                }
                /**
                 * Смотрим в каком каталоге есть файл
                 */
                foreach ($aData['paths'] as $sPath) {
                    $sFile = $sPath . '/' . $sAsset;
                    if (file_exists($sFile)) {
                        break;
                    }
                }
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
     * Удаляет компонент из списка загрузки
     *
     * @param $sName
     */
    public function Remove($sName)
    {
        $sName = strtolower($sName);
        unset($this->aComponentsList[$sName]);
    }

    /**
     * Удаляет все компоненты из загрузки
     */
    public function RemoveAll()
    {
        $this->aComponentsList = array();
    }

    /**
     * Возвращает полные серверные пути до компонента
     *
     * @param string $sName Имя компонента. Может содержать название плагина, например, "page:alert" - компонент alert плагина page
     * @return string
     */
    public function GetPaths($sName)
    {
        $aData = $this->GetComponentData($sName);
        return $aData['paths'];
    }

    /**
     * Возвращает полный серверный путь до компонента
     * Т.к. путей может быть несколько, то возвращаем первый по приоритету
     *
     * @param string $sName Имя компонента. Может содержать название плагина, например, "page:alert" - компонент alert плагина page
     * @return string
     */
    public function GetPath($sName)
    {
        $aPaths = $this->GetPaths($sName);
        return reset($aPaths);
    }

    /**
     * Возвращает полный web путь до компонента с учетом текущей схемы (http/https)
     * Т.к. путей может быть несколько, то возвращаем первый по приоритету
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
        $aData = $this->GetComponentData($sNameFull);
        $aDataJson = $aData['json'];
        foreach ($aData['paths'] as $sPath) {
            if (isset($aDataJson['templates'][$sTemplate])) {
                $sTpl = $aDataJson['templates'][$sTemplate];
            } else {
                $sTpl = "{$sTemplate}.tpl";
            }
            $sFile = $sPath . '/' . $sTpl;
            if (file_exists($sFile)) {
                return $sFile;
            }
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
        $aData = $this->GetComponentData($sNameFull);

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
        $aDataJson = $aData['json'];
        if (isset($aDataJson[$sAssetType][$sAssetName])) {
            $sAsset = $aDataJson[$sAssetType][$sAssetName];
        } else {
            $sAsset = "{$sAssetName}.{$sAssetExt}";
        }
        if ($sAsset === false) {
            return false;
        }
        foreach ($aData['paths'] as $sPath) {
            $sFile = $sPath . '/' . $sAsset;
            if (file_exists($sFile)) {
                return $sFile;
            }
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
     * Возвращает данные компонента
     *
     * @param $sName
     * @return array
     */
    protected function GetComponentData($sName)
    {
        /**
         * Смотрим в кеше
         */
        if (isset($this->aComponentsData[$sName])) {
            return $this->aComponentsData[$sName];
        }
        /**
         * Получаем список каталогов, где находится компонент и json мета информацию
         */
        $aPaths = $this->GetComponentPaths($sName);
        $this->aComponentsData[$sName] = array(
            'json'  => $this->GetComponentJson($aPaths),
            'paths' => $aPaths,

        );
        return $this->aComponentsData[$sName];
    }

    /**
     * Возвращает список каталогов, где находится компонент.
     * Каталоги возвращаются согласно приоритету - сначала идут самые приоритетные.
     *
     * @param $sName
     * @return array
     */
    protected function GetComponentPaths($sName)
    {
        list($sPlugin, $sName) = $this->ParseName($sName);
        $sPath = 'components/' . $sName;
        $aPaths = array();
        if ($sPlugin) {
            /**
             * Проверяем наличие компонента в каталоге текущего шаблона плагина
             */
            $sPathTemplate = Plugin::GetTemplatePath($sPlugin);
            if (file_exists($sPathTemplate . $sPath)) {
                $aPaths[] = $sPathTemplate . $sPath;
            }
            /**
             * Проверяем наличие компонента в общем каталоге плагина
             */
            $sPathTemplate = Config::Get('path.application.plugins.server') . "/{$sPlugin}/frontend";
            if (file_exists($sPathTemplate . '/' . $sPath)) {
                $aPaths[] = $sPathTemplate . '/' . $sPath;
            }
        } else {
            /**
             * Проверяем наличие компонента в каталоге текущего шаблона
             */
            $sPathTemplate = $this->Fs_GetPathServerFromWeb(Config::Get('path.skin.web'));
            if (file_exists($sPathTemplate . '/' . $sPath)) {
                $aPaths[] = $sPathTemplate . '/' . $sPath;
            }
        }

        /**
         * Проверяем на компонент приложения
         */
        $sPathTemplate = Config::Get('path.application.server') . '/frontend';
        if (file_exists($sPathTemplate . '/' . $sPath)) {
            $aPaths[] = $sPathTemplate . '/' . $sPath;
        }
        /**
         * Проверяем на компонент фреймворка
         */
        $sPathTemplate = Config::Get('path.framework.server') . '/frontend';
        if (file_exists($sPathTemplate . '/' . $sPath)) {
            $aPaths[] = $sPathTemplate . '/' . $sPath;
        }
        return $aPaths;
    }

    /**
     * Возвращает json данные компонента с учетом наследования
     *
     * @param $aPaths
     * @return array|mixed
     */
    protected function GetComponentJson(&$aPaths)
    {
        /**
         * Получаем пути в обратном порядке, т.к. будем мержить данные
         */
        $aPaths = array_reverse($aPaths);
        $aPathsNew = array();
        $aJson = array();
        foreach ($aPaths as $sPath) {
            $sFileJson = $sPath . '/component.json';
            if (file_exists($sFileJson)) {
                if ($sContent = @file_get_contents($sFileJson) and $aData = @json_decode($sContent, true)) {
                    if (isset($aData['mode']) and $aData['mode'] == 'delegate') {
                        $aJson = $aData;
                        /**
                         * Удаляем прошлые каталоги
                         */
                        $aPathsNew = array();
                    } else {
                        $aJson = func_array_merge_assoc($aJson, $aData);
                    }
                }
            }
            $aPathsNew[] = $sPath;
        }
        /**
         * Подменяем пути
         */
        $aPaths = array_reverse($aPathsNew);
        return $aJson;
    }

    /**
     * Возвращает отрендеренный шаблон компонента
     *
     * @param string $sComponent Имя компонента
     * @param string|null $sTemplate Название шаблона, если null то будет использоваться шаблон по имени компонента
     * @param array $aParams Список параметров, которые необходимо прогрузить в шаблон. Параметры прогружаются как локальные.
     * @return string
     */
    public function Fetch($sComponent, $sTemplate = null, $aParams = array())
    {
        $oViewer = $this->Viewer_GetLocalViewer();
        $oViewer->Assign($aParams, null, true);
        return $oViewer->Fetch('component@' . $sComponent . ($sTemplate ? '.' . $sTemplate : ''));
    }
}
