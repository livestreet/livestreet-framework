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
 * Модуль управления плагинами - установка, обновление, удаление
 *
 * @package framework.modules
 * @since 2.0
 */
class ModulePluginManager extends ModuleORM
{
    /**
     * Путь к директории с плагинами
     *
     * @var string
     */
    protected $sPluginsDir;

    /**
     * Инициализация модуля
     *
     */
    public function Init()
    {
        parent::Init();
        $this->sPluginsDir = Config::Get('path.application.plugins.server') . '/';
    }

    /**
     * Выполняет активацию плагина
     *
     * @param $sPlugin
     *
     * @return bool
     */
    public function ActivatePlugin($sPlugin)
    {
        if (!$this->CheckPluginsFileWritable()) {
            $this->Message_AddError($this->Lang_Get('admin.plugins.notices.activation_file_write_error'),
                $this->Lang_Get('error'), true);
            return false;
        }
        $sPlugin = strtolower($sPlugin);
        /**
         * Получаем xml информацию
         */
        if (!$oXml = $this->GetPluginXmlInfo($sPlugin)) {
            return false;
        }
        $sClassPlugin = 'Plugin' . func_camelize($sPlugin);
        if (!class_exists($sClassPlugin)) {
            return false;
        }
        $aPluginItemsActive = $this->GetPluginsActive();
        $oPlugin = new $sClassPlugin;

        if (in_array($sPlugin, $aPluginItemsActive)) {
            $this->Message_AddError($this->Lang_Get('admin.plugins.notices.activation_already_error'),
                $this->Lang_Get('error'), true);
            return false;
        }
        /**
         * Проверяем совместимость с версией LS
         */
        if (defined('LS_VERSION')
            and version_compare(LS_VERSION, (string)$oXml->requires->livestreet, '<')
        ) {
            $this->Message_AddError(
                $this->Lang_Get('admin.plugins.notices.activation_version_error',
                    array('version' => $oXml->requires->livestreet)),
                $this->Lang_Get('error'), true
            );
            return false;
        }
        /**
         * Проверяем наличие require-плагинов
         */
        if ($oXml->requires->plugins) {
            $iConflict = 0;
            foreach ($oXml->requires->plugins->children() as $sReqPlugin) {
                if (!in_array($sReqPlugin, $aPluginItemsActive)) {
                    $iConflict++;
                    $this->Message_AddError(
                        $this->Lang_Get('admin.plugins.notices.activation_requires_error',
                            array('plugin' => func_camelize($sReqPlugin))),
                        $this->Lang_Get('error'), true
                    );
                }
            }
            if ($iConflict) {
                return false;
            }
        }
        /**
         * Проверяем на конфликт делегатов
         */
        $aPluginDelegates = $oPlugin->GetDelegates();
        $aPluginInherits = $oPlugin->GetInherits();
        $aAllDelegates = $this->Plugin_GetDelegatesAll();
        $aAllInherits = $this->Plugin_GetInheritsAll();
        /**
         * Проверяем, не вступает ли данный плагин в конфликт с уже активированными
         * (по поводу объявленных делегатов)
         */
        $iConflict = 0;
        foreach ($aAllDelegates as $sGroup => $aReplaceList) {
            $iCount = 0;
            if (isset($aPluginDelegates[$sGroup])
                and is_array($aPluginDelegates[$sGroup])
                and $iCount = count($aOverlap = array_intersect_key($aReplaceList, $aPluginDelegates[$sGroup]))
            ) {
                $iConflict += $iCount;
                foreach ($aOverlap as $sResource => $aConflict) {
                    $this->Message_AddError(
                        $this->Lang_Get('admin.plugins.notices.activation_overlap', array(
                            'resource' => $sResource,
                            'delegate' => $aConflict['delegate'],
                            'plugin'   => $aConflict['sign']
                        )), $this->Lang_Get('error'), true
                    );
                }
            }
            if (isset($aPluginInherits[$sGroup])
                and is_array($aPluginInherits[$sGroup])
                and $iCount = count($aOverlap = array_intersect_key($aReplaceList, $aPluginInherits[$sGroup]))
            ) {
                $iConflict += $iCount;
                foreach ($aOverlap as $sResource => $aConflict) {
                    $this->Message_AddError(
                        $this->Lang_Get('admin.plugins.notices.activation_overlap', array(
                            'resource' => $sResource,
                            'delegate' => $aConflict['delegate'],
                            'plugin'   => $aConflict['sign']
                        )), $this->Lang_Get('error'), true
                    );
                }
            }
            if ($iCount) {
                return false;
            }
        }
        /**
         * Проверяем на конфликт с наследуемыми классами
         */
        $iConflict = 0;
        foreach ($aPluginDelegates as $sGroup => $aReplaceList) {
            foreach ($aReplaceList as $sResource => $aConflict) {
                if (isset($aAllInherits[$sResource])) {
                    $iConflict += count($aAllInherits[$sResource]['items']);
                    foreach ($aAllInherits[$sResource]['items'] as $aItem) {
                        $this->Message_AddError(
                            $this->Lang_Get('admin.plugins.notices.activation_overlap_inherit', array(
                                'resource' => $sResource,
                                'plugin'   => $aItem['sign']
                            )),
                            $this->Lang_Get('error'), true
                        );
                    }
                }
            }
        }
        if ($iConflict) {
            return false;
        }
        /**
         * Кастомный функционал активации плагина
         */
        if ($bResult = $oPlugin->Activate()) {
            /**
             * Выполняем актулизацию БД плагина - миграции
             */
            $this->ApplyPluginUpdate($sPlugin);
            /**
             * Записываем в файл
             */
            $aPluginItemsActive[] = $sPlugin;
            if (!$this->WriteActivePlugins($aPluginItemsActive)) {
                return false;
            }
        }
        return $bResult;
    }

    /**
     * Выполняет деактивацию плагина
     *
     * @param $sPlugin
     *
     * @return bool
     */
    public function DeactivatePlugin($sPlugin)
    {
        if (!$this->CheckPluginsFileWritable()) {
            $this->Message_AddError($this->Lang_Get('admin.plugins.notices.activation_file_write_error'),
                $this->Lang_Get('error'), true);
            return false;
        }
        $sPlugin = strtolower($sPlugin);
        /**
         * Получаем xml информацию
         */
        if (!$oXml = $this->GetPluginXmlInfo($sPlugin)) {
            return false;
        }
        $sClassPlugin = 'Plugin' . func_camelize($sPlugin);
        if (!class_exists($sClassPlugin)) {
            return false;
        }
        $aPluginItemsActive = $this->GetPluginsActive();
        $oPlugin = new $sClassPlugin;

        if (!in_array($sPlugin, $aPluginItemsActive)) {
            $this->Message_AddError($this->Lang_Get('admin.plugins.notices.deactivation_already_error'),
                $this->Lang_Get('error'), true);
            return false;
        }
        /**
         * Проверяем на зависимость других плагинов через опцию requires
         */
        $iConflict = 0;
        foreach ($aPluginItemsActive as $sPlugnCheck) {
            foreach ($oXml->requires->plugins->children() as $sReqPlugin) {
                if ($sReqPlugin == $sPlugin) {
                    $iConflict++;
                    $this->Message_AddError(
                        $this->Lang_Get('admin.plugins.notices.deactivation_requires_error',
                            array('plugin' => func_camelize($sPlugnCheck))),
                        $this->Lang_Get('error'), true
                    );
                }
            }
        }
        if ($iConflict) {
            return false;
        }
        /**
         * Кастомный функционал деактивации плагина
         */
        if ($bResult = $oPlugin->Deactivate()) {
            if (false !== ($iIndex = array_search($sPlugin, $aPluginItemsActive))) {
                unset($aPluginItemsActive[$iIndex]);
                if (!$this->WriteActivePlugins($aPluginItemsActive)) {
                    return false;
                }
            }
        }
        return $bResult;
    }

    /**
     * Удаляет физически плагин с сервера
     *
     * @param $sPlugin
     *
     * @return bool
     */
    public function RemovePlugin($sPlugin)
    {
        $sPlugin = strtolower($sPlugin);
        $aPluginItemsActive = $this->GetPluginsActive();
        /**
         * Если плагин активен, деактивируем его
         */
        if (in_array($sPlugin, $aPluginItemsActive)) {
            if (!$this->DeactivatePlugin($sPlugin)) {
                return false;
            }
        }
        $sClassPlugin = 'Plugin' . func_camelize($sPlugin);
        $oPlugin = new $sClassPlugin;
        /**
         * Сначала очищаем данные БД от плагина, а затем выполняем кастомный метод удаления плагина
         */
        if ($oPlugin->Remove()) {
            /**
             * Делаем откат изменений БД, которые делал плагин (откат миграций)
             */
            $this->PurgePluginUpdate($sPlugin);
            /**
             * Удаляем директорию с плагином
             */
            func_rmdir($this->sPluginsDir . $sPlugin);
            return true;
        }
        return false;
    }

    /**
     * Возвращает список плагинов с XML описанием
     *
     * @param array $aFilter
     *
     * @return array
     */
    public function GetPluginsItems($aFilter = array())
    {
        $aPluginItemsReturn = array();
        $aPluginCodes = func_list_plugins(true);
        $aPluginItemsActive = $this->GetPluginsActive();

        /**
         * Получаем версии из БД для всех плагинов
         */
        if ($aPluginCodes) {
            $aVersionItems = $this->GetVersionItemsByFilter(array('code in' => $aPluginCodes, '#index-from' => 'code'));
        } else {
            $aVersionItems = array();
        }

        foreach ($aPluginCodes as $sPluginCode) {
            /**
             * Получаем из XML файла описания
             */
            if ($oXml = $this->GetPluginXmlInfo($sPluginCode)) {
                if (isset($aVersionItems[$sPluginCode])) {
                    $sVersionDb = $aVersionItems[$sPluginCode]->getVersion();
                } else {
                    $sVersionDb = null;
                }
                $aInfo = array(
                    'code'         => $sPluginCode,
                    'is_active'    => in_array($sPluginCode, $aPluginItemsActive),
                    'property'     => $oXml,
                    'apply_update' => (is_null($sVersionDb) or version_compare($sVersionDb, (string)$oXml->version,
                            '<')) ? true : false,
                );
                $aPluginItemsReturn[$sPluginCode] = $aInfo;
            }
        }
        /**
         * Если нужно сортировать плагины
         */
        if (isset($aFilter['order'])) {
            if ($aFilter['order'] == 'name') {
                uasort($aPluginItemsReturn, function ($a, $b) {
                    if ((string)$a['property']->name->data == (string)$b['property']->name->data) {
                        return 0;
                    }
                    return ((string)$a['property']->name->data < (string)$b['property']->name->data) ? -1 : 1;
                });
            }
        }
        return $aPluginItemsReturn;
    }

    /**
     * Возвращает XML объект описания плагина
     *
     * @param $sPlugin
     *
     * @return null|SimpleXMLElement
     */
    public function GetPluginXmlInfo($sPlugin)
    {
        /**
         * Считываем данные из XML файла описания
         */
        $sPluginXML = $this->sPluginsDir . $sPlugin . '/plugin.xml';
        if ($oXml = @simplexml_load_file($sPluginXML)) {
            /**
             * Обрабатываем данные, считанные из XML-описания
             */
            $sLang = $this->Lang_GetLang();

            $this->Xlang($oXml, 'name', $sLang);
            $this->Xlang($oXml, 'author', $sLang);
            $this->Xlang($oXml, 'description', $sLang);
            $oXml->homepage = $this->Text_Parser((string)$oXml->homepage);
            $oXml->settings = preg_replace('/{([^}]+)}/', Router::GetPath('$1'), $oXml->settings);
            return $oXml;
        }
        return null;
    }

    /**
     * Выполняет установку плагина из магазина LS
     *
     * @param $sPlugin
     *
     * @return bool
     */
    public function InstallPluginFromCatalogLS($sPlugin)
    {
        var_dump('install from catalog - ' . $sPlugin);
        return true;
    }

    /**
     * Выполняет установку плагина из локальной директории
     *
     * @param $sPlugin
     * @param $sDir
     *
     * @return bool
     */
    public function InstallPluginFromDir($sPlugin, $sDir)
    {
        var_dump('install from catalog - ' . $sPlugin);
        return true;
    }


    /**
     * Записывает список активных плагинов в файл PLUGINS.DAT
     *
     * @param array|string $aPlugins Список плагинов
     * @return bool
     */
    public function WriteActivePlugins($aPlugins)
    {
        if (!$this->CheckPluginsFileWritable()) {
            return false;
        }
        if (!is_array($aPlugins)) {
            $aPlugins = array($aPlugins);
        }
        $aPlugins = array_unique(array_map('trim', $aPlugins));
        /**
         * Записываем данные в файл PLUGINS.DAT
         */
        if (@file_put_contents($this->sPluginsDir . Config::Get('sys.plugins.activation_file'),
                implode(PHP_EOL, $aPlugins)) !== false
        ) {
            /**
             * Сбрасываем весь кеш, т.к. могут быть закешированы унаследованые плагинами сущности
             */
            $this->Cache_Clean();
            /**
             * Сбрасываем отдельный кеш ORM
             */
            $this->Cache_Clean(Zend_Cache::CLEANING_MODE_ALL, array(), 'file_orm', true);
            /**
             * Очищаем компиленые шаблоны от Smarty
             */
            $this->Viewer_ClearCompiledTemplates();
            return true;
        }
        return false;
    }

    /**
     * Проверяет доступность файла plugins.dat на запись
     *
     * @return bool
     */
    public function CheckPluginsFileWritable()
    {
        if (@is_writable($this->sPluginsDir . Config::Get('sys.plugins.activation_file'))) {
            return true;
        }
        /**
         * Возможно файла еще не существует
         */
        if (!file_exists($this->sPluginsDir . Config::Get('sys.plugins.activation_file'))) {
            if (false !== @file_put_contents($this->sPluginsDir . Config::Get('sys.plugins.activation_file'), '')) {
                @chmod($this->sPluginsDir . Config::Get('sys.plugins.activation_file'), 0666);
                return true;
            }
        }
        return false;
    }

    /**
     * Возвращает список активных плагинов
     *
     * @return array
     */
    public function GetPluginsActive()
    {
        return array_keys(Engine::getInstance()->GetPlugins());
    }

    /**
     * Получает значение параметра из XML на основе языковой разметки
     *
     * @param SimpleXMLElement $oXml XML узел
     * @param string $sProperty Свойство, которое нужно вернуть
     * @param string $sLang Название языка
     */
    protected function Xlang($oXml, $sProperty, $sLang)
    {
        $sProperty = trim($sProperty);

        if (!count($data = $oXml->xpath("{$sProperty}/lang[@name='{$sLang}']"))) {
            /**
             * Пробуем получить язык в старом полном формате (ru -> russian)
             */
            $sLangOld = Config::Get('module.lang.i18n_mapping.' . $sLang);
            if (!$sLangOld or !count($data = $oXml->xpath("{$sProperty}/lang[@name='{$sLangOld}']"))) {
                $data = $oXml->xpath("{$sProperty}/lang[@name='default']");
            }
        }
        $oXml->$sProperty->data = $this->Text_Parser(trim((string)array_shift($data)));
    }

    /**
     * Выполняет актулизацию данных БД для плагина (применение миграций)
     * Обратный по действию метод - PurgePluginUpdate (@see PurgePluginUpdate)
     *
     * @param $sPlugin
     */
    public function ApplyPluginUpdate($sPlugin)
    {
        $sPlugin = strtolower($sPlugin);
        /**
         * Получаем текущую версию плагина из XML описания
         */
        if (!$oXml = $this->GetPluginXmlInfo($sPlugin)) {
            return;
        }
        $sVersionByFile = (string)$oXml->version;
        /**
         * Получаем текущую версию плагина из БД
         */
        if ($oVersion = $this->GetVersionByCode($sPlugin)) {
            $sVersionByDb = $oVersion->getVersion();
        } else {
            $sVersionByDb = null;
        }

        if ($sVersionByFile == $sVersionByDb) {
            return;
        }
        if (!$oVersion) {
            $oVersion = Engine::GetEntity('ModulePluginManager_EntityVersion');
            $oVersion->setCode($sPlugin);
        }
        /**
         * Получаем новые файлы обновлений
         */
        $aVersionFiles = $this->GetUpdateNewFiles($sPlugin, $sVersionByDb);
        foreach ($aVersionFiles as $sVersion => $aFiles) {
            /**
             * Выполняем файлы
             */
            if ($aFiles) {
                foreach ($aFiles as $aFile) {
                    require_once($aFile['path']);
                    $sClass = 'Plugin' . func_camelize($sPlugin) . '_Update_' . $aFile['name'];
                    $oUpdate = new $sClass;
                    $oUpdate->up();
                    /**
                     * Сохраняем в БД
                     */
                    if (!$this->GetMigrationByCodeAndFile($sPlugin, $aFile['file'])) {
                        $oMigration = Engine::GetEntity('ModulePluginManager_EntityMigration');
                        $oMigration->setCode($sPlugin);
                        $oMigration->setVersion($sVersion);
                        $oMigration->setFile($aFile['file']);
                        $oMigration->Add();
                    }
                }
            }
        }
        /**
         * Проставляем версию из описания плагина
         */
        $oVersion->setVersion($sVersionByFile);
        $oVersion->Save();
    }

    /**
     * Выполняет откат изменений плагина к БД
     * Обратный по действию метод - ApplyPluginUpdate (@see ApplyPluginUpdate)
     *
     * @param $sPlugin
     */
    protected function PurgePluginUpdate($sPlugin)
    {
        $sPlugin = strtolower($sPlugin);
        $sPluginDir = Plugin::GetPath($sPlugin) . 'update/';
        /**
         * Получаем список выполненых миграций из БД
         */
        $aMigrationItemsGroup = $this->GetMigrationItemsByFilter(array(
            'code'         => $sPlugin,
            '#order'       => array('file' => 'asc'),
            '#index-group' => 'version'
        ));
        $aMigrationItemsGroup = array_reverse($this->SortVersions($aMigrationItemsGroup, true), true);
        foreach ($aMigrationItemsGroup as $sVersion => $aMigrationItems) {
            foreach ($aMigrationItems as $oMigration) {
                $sPath = $sPluginDir . $sVersion . '/' . $oMigration->getFile();
                if (file_exists($sPath)) {
                    require_once($sPath);
                    $sClass = 'Plugin' . func_camelize($sPlugin) . '_Update_' . basename($oMigration->getFile(),
                            '.php');
                    $oUpdate = new $sClass;
                    $oUpdate->down();
                }
                /**
                 * Удаляем запись из БД
                 */
                $oMigration->Delete();
            }
        }
        /**
         * Удаляем версию
         */
        if ($oVersion = $this->GetVersionByCode($sPlugin)) {
            $oVersion->Delete();
        }
        /**
         * Удаляем данные плагина из хранилища настроек
         */
        $this->Storage_RemoveAll('Plugin' . func_camelize($sPlugin));
        $this->Storage_Remove('__config__', 'Plugin' . func_camelize($sPlugin)); // хардим удаление конфига админки
    }

    /**
     * Возврашает список файлов миграций по версиям
     *
     * @param      $sPlugin
     * @param null $sVersionFrom
     *
     * @return array
     */
    public function GetUpdateNewFiles($sPlugin, $sVersionFrom = null)
    {
        $sPluginDir = Plugin::GetPath($sPlugin) . 'update/';
        /**
         * Получаем список каталогов-версий в /update/
         */
        $aVersions = array();
        $aPaths = glob($sPluginDir . '*', GLOB_ONLYDIR);
        if ($aPaths) {
            foreach ($aPaths as $sPath) {
                $aVersions[] = basename($sPath);
            }
        }
        $aVersions = $this->SortVersions($aVersions);
        /**
         * Оставляем только новые версии
         */
        if ($sVersionFrom and false !== ($iPos = array_search($sVersionFrom, $aVersions))) {
            $aVersions = array_slice($aVersions, ++$iPos);
        }
        /**
         * Получаем список файлов для каждой версии
         */
        $aResultFiles = array();
        foreach ($aVersions as $sVersion) {
            $aResultFiles[$sVersion] = array();
            $aFiles = glob($sPluginDir . "{$sVersion}/*");
            if ($aFiles) {
                foreach ($aFiles as $sFile) {
                    $aResultFiles[$sVersion][] = array(
                        'name' => basename($sFile, '.php'),
                        'file' => basename($sFile),
                        'path' => $sFile,
                    );
                }
            }
        }
        return $aResultFiles;
    }

    /**
     * Выполняет сортировку массива версий
     *
     * @param      $aVersions
     * @param bool $bUseKeys
     *
     * @return mixed
     */
    protected function SortVersions($aVersions, $bUseKeys = false)
    {
        $funcSort = function ($a, $b) {
            if ($a == $b) {
                return 0;
            }
            return version_compare($a, $b);
        };
        if ($bUseKeys) {
            uksort($aVersions, $funcSort);
        } else {
            usort($aVersions, $funcSort);
        }
        return $aVersions;
    }
}