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
 * Абстракция плагина, от которой наследуются все плагины
 * Файл плагина должен находиться в каталоге /plugins/plgname/ и иметь название PluginPlgname.class.php
 *
 * @package framework.engine
 * @since 1.0
 */
abstract class Plugin extends LsObject
{
    /**
     * Путь к шаблонам с учетом наличия соответствующего skin`a
     *
     * @var array
     */
    static protected $aTemplatePath = array();
    /**
     * Web-адрес директорий шаблонов с учетом наличия соответствующего skin`a
     *
     * @var array
     */
    static protected $aTemplateWebPath = array();
    /**
     * Массив делегатов плагина
     *
     * @var array
     */
    protected $aDelegates = array();
    /**
     * Массив наследуемых классов плагина
     *
     * @var array
     */
    protected $aInherits = array();

    /**
     * Метод инициализации плагина
     *
     */
    public function Init()
    {
    }

    /**
     * Метод, который вызывается перед самой инициализацией ядра
     */
    public function BeforeInitEngine()
    {

    }

    /**
     * Передает информацию о делегатах в модуль ModulePlugin
     * Вызывается Engine перед инициализацией плагина
     * @see Engine::LoadPlugins
     */
    final function Delegate()
    {
        $aDelegates = $this->GetDelegates();
        foreach ($aDelegates as $sObjectName => $aParams) {
            foreach ($aParams as $sFrom => $sTo) {
                $this->Plugin_Delegate($sObjectName, $sFrom, $sTo, get_class($this));
            }
        }

        $aInherits = $this->GetInherits();
        foreach ($aInherits as $sObjectName => $aParams) {
            foreach ($aParams as $sFrom => $sTo) {
                $this->Plugin_Inherit($sFrom, $sTo, get_class($this));
            }
        }
    }

    /**
     * Возвращает массив наследников
     *
     * @return array
     */
    final function GetInherits()
    {
        $aReturn = array();
        if (is_array($this->aInherits) and count($this->aInherits)) {
            foreach ($this->aInherits as $sObjectName => $aParams) {
                if (is_array($aParams) and count($aParams)) {
                    foreach ($aParams as $sFrom => $sTo) {
                        if (is_int($sFrom)) {
                            $sFrom = $sTo;
                            $sTo = null;
                        }
                        list($sFrom, $sTo) = $this->MakeDelegateParams($sObjectName, $sFrom, $sTo);
                        $aReturn[$sObjectName][$sFrom] = $sTo;
                    }
                }
            }
        }
        return $aReturn;
    }

    /**
     * Возвращает массив делегатов
     *
     * @return array
     */
    final function GetDelegates()
    {
        $aReturn = array();
        if (is_array($this->aDelegates) and count($this->aDelegates)) {
            foreach ($this->aDelegates as $sObjectName => $aParams) {
                if (is_array($aParams) and count($aParams)) {
                    foreach ($aParams as $sFrom => $sTo) {
                        if (is_int($sFrom)) {
                            $sFrom = $sTo;
                            $sTo = null;
                        }
                        list($sFrom, $sTo) = $this->MakeDelegateParams($sObjectName, $sFrom, $sTo);
                        $aReturn[$sObjectName][$sFrom] = $sTo;
                    }
                }
            }
        }
        return $aReturn;
    }

    /**
     * Преобразовывает краткую форму имен делегатов в полную
     *
     * @param $sObjectName    Название типа объекта делегата
     * @see ModulePlugin::aDelegates
     * @param $sFrom    Что делегируем
     * @param $sTo        Что делегирует
     * @return array
     */
    public function MakeDelegateParams($sObjectName, $sFrom, $sTo)
    {
        /**
         * Если не указан делегат TO, считаем, что делегатом является
         * одноименный объект текущего плагина
         */
        if ($sObjectName == 'template') {
            if (!$sTo) {
                $sTo = self::GetTemplatePath(get_class($this)) . $sFrom;
            } else {
                $sTo = preg_replace("/^_/", $this->GetTemplatePath(get_class($this)), $sTo);
            }
        } else {
            if (!$sTo) {
                $sTo = get_class($this) . '_' . $sFrom;
            } else {
                $sTo = preg_replace("/^_/", get_class($this) . '_', $sTo);
            }
        }
        return array($sFrom, $sTo);
    }

    /**
     * Метод активации плагина
     *
     * @return bool
     */
    public function Activate()
    {
        return true;
    }

    /**
     * Метод деактивации плагина
     *
     * @return bool
     */
    public function Deactivate()
    {
        return true;
    }

    /**
     * Метод удаления плагина
     *
     * @return bool
     */
    public function Remove()
    {
        return true;
    }

    /**
     * Транслирует на базу данных запросы из указанного файла
     * @see ModuleDatabase::ExportSQL
     *
     * @param  string $sFilePath Полный путь до файла с SQL
     * @return array
     */
    protected function ExportSQL($sFilePath)
    {
        return $this->Database_ExportSQL($sFilePath);
    }

    /**
     * Выполняет SQL
     * @see ModuleDatabase::ExportSQLQuery
     *
     * @param string $sSql Строка SQL запроса
     * @return array
     */
    protected function ExportSQLQuery($sSql)
    {
        return $this->Database_ExportSQLQuery($sSql);
    }

    /**
     * Проверяет наличие таблицы в БД
     * @see ModuleDatabase::IsTableExists
     *
     * @param string $sTableName Название таблицы, необходимо перед именем таблицы добавлять "prefix_", это позволит учитывать произвольный префикс таблиц у пользователя
     * <pre>
     * prefix_topic
     * </pre>
     * @return bool
     */
    protected function IsTableExists($sTableName)
    {
        return $this->Database_IsTableExists($sTableName);
    }

    /**
     * Проверяет наличие поля в таблице
     * @see ModuleDatabase::IsFieldExists
     *
     * @param string $sTableName Название таблицы, необходимо перед именем таблицы добавлять "prefix_", это позволит учитывать произвольный префикс таблиц у пользователя
     * @param string $sFieldName Название поля в таблице
     * @return bool
     */
    protected function IsFieldExists($sTableName, $sFieldName)
    {
        return $this->Database_IsFieldExists($sTableName, $sFieldName);
    }

    /**
     * Добавляет новый тип в поле enum(перечисление)
     * @see ModuleDatabase::AddEnumType
     *
     * @param string $sTableName Название таблицы, необходимо перед именем таблицы добавлять "prefix_", это позволит учитывать произвольный префикс таблиц у пользователя
     * @param string $sFieldName Название поля в таблице
     * @param string $sType Название типа
     */
    protected function AddEnumType($sTableName, $sFieldName, $sType)
    {
        $this->Database_AddEnumType($sTableName, $sFieldName, $sType);
    }

    /**
     * Удаляет тип в поле таблицы с типом enum
     * @see ModuleDatabase::RemoveEnumType
     *
     * @param string $sTableName Название таблицы, необходимо перед именем таблицы добавлять "prefix_", это позволит учитывать произвольный префикс таблиц у пользователя
     * @param string $sFieldName Название поля в таблице
     * @param string $sType Название типа
     */
    protected function RemoveEnumType($sTableName, $sFieldName, $sType)
    {
        $this->Database_RemoveEnumType($sTableName, $sFieldName, $sType);
    }

    /**
     * Возвращает версию плагина
     *
     * @return string|null
     */
    public function GetVersion()
    {
        if ($oXml = $this->PluginManager_GetPluginXmlInfo(self::GetPluginCode($this))) {
            return (string)$oXml->version;
        }
        return null;
    }

    /**
     * Возвращает полный серверный путь до плагина
     *
     * @param string $sName
     * @return string
     */
    static public function GetPath($sName)
    {
        $sName = self::GetPluginCode($sName);

        return Config::Get('path.application.plugins.server') . '/' . $sName . '/';
    }

    /**
     * Возвращает полный web-адрес до плагина
     *
     * @param string $sName
     * @return string
     */
    static public function GetWebPath($sName)
    {
        $sName = self::GetPluginCode($sName);

        return Router::GetPathRootWeb() . '/application/plugins/' . $sName . '/';
    }

    /**
     * Возвращает правильный серверный путь к директории шаблонов с учетом текущего шаблона
     * Если пользователь использует шаблон которого нет в плагине, то возвращает путь до шабона плагина 'default'
     *
     * @param string $sName Название плагина или его класс
     * @return string|null
     */
    static public function GetTemplatePath($sName)
    {
        $sName = self::GetPluginCode($sName);
        if (!isset(self::$aTemplatePath[$sName])) {
            $aPaths = glob(Config::Get('path.application.plugins.server') . '/' . $sName . '/frontend/skin/*',
                GLOB_ONLYDIR);
            $sTemplateName = ($aPaths and in_array(Config::Get('view.skin'), array_map('basename', $aPaths)))
                ? Config::Get('view.skin')
                : 'default';

            $sDir = Config::Get('path.application.plugins.server') . "/{$sName}/frontend/skin/{$sTemplateName}/";
            self::$aTemplatePath[$sName] = is_dir($sDir) ? $sDir : null;
        }
        return self::$aTemplatePath[$sName];
    }

    /**
     * Возвращает правильный web-адрес директории шаблонов
     * Если пользователь использует шаблон которого нет в плагине, то возвращает путь до шабона плагина 'default'
     *
     * @param string $sName Название плагина или его класс
     * @return string
     */
    static public function GetTemplateWebPath($sName)
    {
        $sName = self::GetPluginCode($sName);
        if (!isset(self::$aTemplateWebPath[$sName])) {
            $aPaths = glob(Config::Get('path.application.plugins.server') . '/' . $sName . '/frontend/skin/*',
                GLOB_ONLYDIR);
            $sTemplateName = ($aPaths and in_array(Config::Get('view.skin'), array_map('basename', $aPaths)))
                ? Config::Get('view.skin')
                : 'default';

            self::$aTemplateWebPath[$sName] = Router::GetFixPathWeb(Config::Get('path.application.plugins.web')) . "/{$sName}/frontend/skin/{$sTemplateName}/";
        }
        return self::$aTemplateWebPath[$sName];
    }

    /**
     * Устанавливает значение серверного пути до шаблонов плагина
     *
     * @param  string $sName Имя плагина
     * @param  string $sTemplatePath Серверный путь до шаблона
     * @return bool
     */
    static public function SetTemplatePath($sName, $sTemplatePath)
    {
        if (!is_dir($sTemplatePath)) {
            return false;
        }
        self::$aTemplatePath[$sName] = $sTemplatePath;
        return true;
    }

    /**
     * Устанавливает значение web-пути до шаблонов плагина
     *
     * @param  string $sName Имя плагина
     * @param  string $sTemplatePath Серверный путь до шаблона
     */
    static public function SetTemplateWebPath($sName, $sTemplatePath)
    {
        self::$aTemplateWebPath[$sName] = $sTemplatePath;
    }

    /**
     * Возвращает код плагина
     *
     * @param string|object $mPlugin Объект любого класса плагина или название плагина
     *
     * @return string
     */
    static public function GetPluginCode($mPlugin)
    {
        if (is_object($mPlugin)) {
            $mPlugin = get_class($mPlugin);
        }
        return preg_match('/^Plugin([\w]+)(_[\w]+)?$/Ui', $mPlugin, $aMatches)
            ? func_underscore($aMatches[1])
            : func_underscore($mPlugin);
    }
}