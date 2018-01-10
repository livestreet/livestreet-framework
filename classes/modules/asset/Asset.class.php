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
 * Модуль управления статическими файлами css стилей и js сриптов
 * Позволяет сжимать и объединять файлы для более быстрой загрузки
 *
 * @package framework.modules
 * @since 2.0
 */
class ModuleAsset extends Module
{
    /**
     * Тип для файлов стилей
     */
    const ASSET_TYPE_CSS = 'css';
    /**
     * Тип для файлов скриптов
     */
    const ASSET_TYPE_JS = 'js';
    /**
     * Каталог для проверки блокировки
     *
     * @var null|string
     */
    protected $sDirMergeLock = null;
    /**
     * Список файлов по типам
     * @see Init
     *
     * @var array
     */
    protected $aAssets = array();

    /**
     * Инициалищация модуля
     */
    public function Init()
    {
        /**
         * Задаем начальную структуру для хранения списка файлов по типам
         */
        $this->InitAssets();
    }

    /**
     * Задает начальную структуры для хранения списка файлов по типам
     */
    protected function InitAssets()
    {
        $this->aAssets = array(
            self::ASSET_TYPE_CSS => array(
                /**
                 * Список файлов для добавления в конец списка
                 * В качестве ключей используется путь до файла либо уникальное имя, в качестве значений - дополнительные параметры
                 */
                'append'  => array(),
                /**
                 * Список файлов для добавления в начало списка
                 */
                'prepend' => array(),
            ),
            self::ASSET_TYPE_JS  => array(
                'append'  => array(),
                'prepend' => array(),
            ),
        );
    }

    /**
     * Добавляет новый файл
     *
     * @param string $sFile Полный путь до файла
     * @param array $aParams Дополнительные параметры
     * @param string $sType Тип файла
     * @param bool $bPrepend Добавлять файл в начало общего списка или нет
     * @param bool $bReplace Если такой файл уже добавлен, то заменяет его
     *
     * @return bool
     */
    protected function Add($sFile, $aParams, $sType, $bPrepend = false, $bReplace = false)
    {
        if (!$this->CheckAssetType($sType)) {
            return false;
        }
        $aParams['file'] = $sFile;
        /**
         * Подготавливаем параметры
         */
        $aParams = $this->PrepareParams($aParams);
        /**
         * В качестве уникального ключа использется имя или путь до файла
         */
        $sFileKey = $aParams['name'] ? $aParams['name'] : $aParams['file'];
        /**
         * Проверям на необходимость замены
         */
        foreach (array('prepend', 'append') as $sTypeAdd) {
            if (isset($this->aAssets[$sType][$sTypeAdd][$sFileKey])) {
                if ($bReplace) {
                    unset($this->aAssets[$sType][$sTypeAdd][$sFileKey]);
                } else {
                    return false;
                }
            }
            /**
             * Дополнительно проверим на путь к файлу, если в качестве ключа использовалось имя
             * todo: при таком подходе смысла в ключах массива нет и можно искать на дубли только по значению
             */
            if ($aParams['name']) {
                foreach ($this->aAssets[$sType][$sTypeAdd] as $sFindKey => $aFileParams) {
                    if ($aParams['file'] == $aFileParams['file']) {
                        if ($bReplace) {
                            unset($this->aAssets[$sType][$sTypeAdd][$sFindKey]);
                        } else {
                            return false;
                        }
                    }
                }
            }
        }

        $this->aAssets[$sType][$bPrepend ? 'prepend' : 'append'][$sFileKey] = $aParams;
        return true;
    }

    /**
     * Добавляет файл css стиля
     *
     * @param string $sFile Полный путь до файла
     * @param array $aParams Дополнительные параметры
     * @param bool $bPrepend Добавлять файл в начало общего списка или нет
     * @param bool $bReplace Если такой файл уже добавлен, то заменяет его
     *
     * @return bool
     */
    public function AddCss($sFile, $aParams, $bPrepend = false, $bReplace = false)
    {
        return $this->Add($sFile, $aParams, self::ASSET_TYPE_CSS, $bPrepend, $bReplace);
    }

    /**
     * Добавляет файл js скрипта
     *
     * @param string $sFile Полный путь до файла
     * @param array $aParams Дополнительные параметры
     * @param bool $bPrepend Добавлять файл в начало общего списка или нет
     * @param bool $bReplace Если такой файл уже добавлен, то заменяет его
     *
     * @return bool
     */
    public function AddJs($sFile, $aParams, $bPrepend = false, $bReplace = false)
    {
        return $this->Add($sFile, $aParams, self::ASSET_TYPE_JS, $bPrepend, $bReplace);
    }

    /**
     * Проверяет корректность типа файла
     *
     * @param $sType
     *
     * @return bool
     */
    public function CheckAssetType($sType)
    {
        return in_array($sType, array(self::ASSET_TYPE_CSS, self::ASSET_TYPE_JS));
    }

    /**
     * Производит предварительную обработку параметров
     *
     * @param $aParams
     *
     * @return array
     */
    public function PrepareParams($aParams)
    {
        $aResult = array();

        $aResult['merge'] = (isset($aParams['merge']) and !$aParams['merge']) ? false : true;
        $aResult['compress'] = (isset($aParams['compress']) and !$aParams['compress']) ? false : true;
        $aResult['browser'] = (isset($aParams['browser']) and $aParams['browser']) ? $aParams['browser'] : null;
        $aResult['plugin'] = (isset($aParams['plugin']) and $aParams['plugin']) ? $aParams['plugin'] : null;
        $aResult['name'] = (isset($aParams['name']) and $aParams['name']) ? strtolower($aParams['name']) : null;
        $aResult['defer'] = (isset($aParams['defer']) and $aParams['defer']) ? true : false;
        $aResult['async'] = (isset($aParams['async']) and $aParams['async']) ? true : false;
        if (isset($aParams['file'])) {
            $aResult['file'] = $this->GetFileWeb($aParams['file'], $aParams);
        } else {
            $aResult['file'] = null;
        }
        return $aResult;
    }

    /**
     * Возвращает корректный WEB путь до файла
     *
     * @param string $sFile Исходный путь до файла, обычно он задается в конфиге при подключении css/js, либо через методы Asset_Add*
     * @param array $aParams
     *
     * @return string
     */
    public function GetFileWeb($sFile, $aParams = array())
    {
        return $this->NormalizeFilePath($sFile, $aParams);
    }

    /**
     * Приводит путь до файла к единому виду
     *
     * @param       $sFile
     * @param array $aParams
     *
     * @return string
     */
    protected function NormalizeFilePath($sFile, $aParams = array())
    {
        /**
         * По дефолту считаем, что это локальный абсолютный путь до файла: /var/www/site.com  или c:\server\root\site.com
         */
        $sProtocol = '';
        $sPath = $sFile;
        $sSeparate = DIRECTORY_SEPARATOR;
        /**
         * Проверяем на URL https://site.com или http://site.com
         */
        if (preg_match('#^(https?://)(.*)#i', $sFile, $aMatch)) {
            $sProtocol = $aMatch[1];
            $sPath = $aMatch[2];
            $sSeparate = '/';
            /**
             * Если необходимо, то меняем протокол на https
             */
            if (Router::GetIsSecureConnection() and strtolower($sProtocol) == 'http://' and Config::Get('module.asset.force_https')) {
                $sProtocol = 'https://';
            }
            /**
             * Проверяем на //site.com
             */
        } elseif (strpos($sFile, '//') === 0) {
            $sProtocol = '//';
            $sPath = substr($sFile, 2);
            $sSeparate = '/';
            /**
             * Проверяем на относительный путь без протокола и без первого слеша
             */
        } elseif (strpos($sFile, '/') !== 0 and strpos($sFile, ':') === false) {
            /**
             * Считаем, что указывался путь относительно корня текущего шаблона
             */
            $sSeparate = '/';
            if (isset($aParams['plugin']) and $aParams['plugin']) {
                /**
                 * Относительно шаблона плагина
                 */
                $sPath = Plugin::GetTemplateWebPath($aParams['plugin']) . $sFile;
            } else {
                $sPath = Router::GetFixPathWeb(Config::Get('path.skin.web')) . $sSeparate . $sFile;
            }
            return $sPath;
        }
        /**
         * Могут встречаться двойные слеши, поэтому делаем замену
         */
        $sPath = preg_replace("#([\\\/])+#", $sSeparate, $sPath);
        /**
         * Возвращаем результат
         */
        return $sProtocol . $sPath;
    }

    /**
     * Возвращает HTML код подключения файлов в HEAD'ер страницы
     *
     * @return array    Список HTML оберток подключения файлов
     */
    public function BuildHeadItems()
    {
        /**
         * Запускаем обработку
         */
        $aAssets = $this->Processing();

        $aHeader = array_combine(array_keys($this->aAssets), array('', ''));
        foreach ($aAssets as $sType => $aFile) {
            if ($oType = $this->CreateObjectType($sType)) {
                foreach ($aFile as $aParams) {
                    $sFile = $this->Fs_GetPathWeb($aParams['file']);
                    $aHeader[$sType] .= $oType->getHeadHtml($sFile, $aParams) . PHP_EOL;
                }
            }
        }
        return $aHeader;
    }

    /**
     * Производит обработку файлов
     *
     * @return array    Возвращает список результирующих файлов вида array( 'css'=>array( 'name'=>$aParams, ... ), ... )
     */
    public function Processing()
    {
        $aTypes = array_keys($this->aAssets);
        $aFilesMain = $aFilesTemplate = $aResult = array_combine($aTypes, array_pad(array(), count($aTypes), array()));
        /**
         * Сначала добавляем файлы из конфига
         */
        $aConfigAssets = (array)Config::Get('head.default');
        foreach ($aConfigAssets as $sType => $aAssets) {
            if (!$this->CheckAssetType($sType)) {
                continue;
            }
            /**
             * Перебираем файлы
             */
            foreach ($aAssets as $sFile => $aParams) {
                if (is_numeric($sFile)) {
                    $sFile = $aParams;
                    $aParams = array();
                }
                $aParams['file'] = $sFile;
                /**
                 * Подготавливаем параметры
                 */
                $aParams = $this->PrepareParams($aParams);
                /**
                 * В качестве уникального ключа использется имя или путь до файла
                 */
                $sFileKey = $aParams['name'] ? $aParams['name'] : $aParams['file'];
                $aFilesMain[$sType][$sFileKey] = $aParams;
            }
        }
        /**
         * Формируем файлы из шаблона
         */
        $aConfigAssets = (array)Config::Get('head.template');
        foreach ($aConfigAssets as $sType => $aAssets) {
            if (!$this->CheckAssetType($sType)) {
                continue;
            }
            /**
             * Перебираем файлы
             */
            foreach ($aAssets as $sFile => $aParams) {
                if (is_numeric($sFile)) {
                    $sFile = $aParams;
                    $aParams = array();
                }
                $aParams['file'] = $sFile;
                /**
                 * Подготавливаем параметры
                 */
                $aParams = $this->PrepareParams($aParams);
                /**
                 * В качестве уникального ключа использется имя или путь до файла
                 */
                $sFileKey = $aParams['name'] ? $aParams['name'] : $aParams['file'];
                $aFilesTemplate[$sType][$sFileKey] = $aParams;
            }
        }

        foreach ($aTypes as $sType) {
            /**
             * Объединяем списки
             */
            $aFilesMain[$sType] = array_merge(
                $this->aAssets[$sType]['prepend'],
                $aFilesMain[$sType],
                $this->aAssets[$sType]['append'],
                $aFilesTemplate[$sType]
            );
            /**
             * Выделяем файлы для конкретных браузеров
             */
            $aFilesBrowser = array_filter(
                $aFilesMain[$sType],
                function ($aParams) {
                    return $aParams['browser'] ? true : false;
                }
            );
            /**
             * Выделяем файлы с атрибутом defer
             */
            $aFilesDefer = array_filter(
                $aFilesMain[$sType],
                function ($aParams) {
                    return $aParams['defer'] ? true : false;
                }
            );
            /**
             * Выделяем файлы с атрибутом async
             */
            $aFilesAsync = array_filter(
                $aFilesMain[$sType],
                function ($aParams) {
                    return $aParams['async'] ? true : false;
                }
            );
            /**
             * Исключаем файлы из основного списка
             */
            $aFilesMain[$sType] = array_diff_key($aFilesMain[$sType], $aFilesBrowser);
            /**
             * Если необходимо сливать файлы, то выделяем исключения
             */
            $aFilesNoMerge = array();
            if (Config::Get("module.asset.{$sType}.merge")) {
                $aFilesNoMerge = array_filter(
                    $aFilesMain[$sType],
                    function ($aParams) {
                        return !$aParams['merge'];
                    }
                );
                /**
                 * Исключаем файлы из основного списка
                 */
                $aFilesMain[$sType] = array_diff_key($aFilesMain[$sType], $aFilesNoMerge);
            }
            /**
             * Обрабатываем основной список
             * Проверка необходимости мержа файлов
             */
            $bMergeComplete = false;
            if (Config::Get("module.asset.{$sType}.merge")) {
                /**
                 * Список файлов для основного мержа
                 */
                $aFileNeedMerge = array_diff_key($aFilesMain[$sType], $aFilesDefer, $aFilesAsync);
                if ($sFilePathMerge = $this->Merge($aFileNeedMerge, $sType,
                    (bool)Config::Get("module.asset.{$sType}.compress"))
                ) {
                    $aResult[$sType][$sFilePathMerge] = array('file' => $sFilePathMerge);

                    /**
                     * Список файлов для мержа с атрибутом defer
                     */
                    $bMergeDeferComplete = false;
                    $aFileNeedMerge = array_diff_key($aFilesDefer, $aFilesNoMerge);
                    if ($aFileNeedMerge) {
                        if ($sFilePathMerge = $this->Merge($aFileNeedMerge, $sType,
                            (bool)Config::Get("module.asset.{$sType}.compress"))
                        ) {
                            $aResult[$sType][$sFilePathMerge] = array('file' => $sFilePathMerge, 'defer' => true);
                            $bMergeDeferComplete = true;
                        }
                    } else {
                        $bMergeDeferComplete = true;
                    }

                    /**
                     * Список файлов для мержа с атрибутом async
                     */
                    $bMergeAsyncComplete = false;
                    $aFileNeedMerge = array_diff_key($aFilesAsync, $aFilesNoMerge);
                    if ($aFileNeedMerge) {
                        if ($sFilePathMerge = $this->Merge($aFileNeedMerge, $sType,
                            (bool)Config::Get("module.asset.{$sType}.compress"))
                        ) {
                            $aResult[$sType][$sFilePathMerge] = array('file' => $sFilePathMerge, 'async' => true);
                            $bMergeAsyncComplete = true;
                        }
                    } else {
                        $bMergeAsyncComplete = true;
                    }

                    if ($bMergeDeferComplete and $bMergeAsyncComplete) {
                        $bMergeComplete = true;
                    }
                }
            }
            if (!$bMergeComplete) {
                $aResult[$sType] = array_merge($aResult[$sType], $aFilesMain[$sType]);
            }
            /**
             * Обрабатываем список исключения объединения
             */
            $aResult[$sType] = array_merge($aResult[$sType], $aFilesNoMerge);
            /**
             * Обрабатываем список для отдельных браузеров
             */
            $aResult[$sType] = array_merge($aResult[$sType], $aFilesBrowser);
        }
        return $aResult;
    }

    /**
     * Проверяет на блокировку
     * Если нет блокировки, то создает ее
     *
     * @return bool
     */
    protected function IsLockMerge()
    {
        $this->sDirMergeLock = Config::Get('path.tmp.server') . '/asset-merge-lock';
        if ($bResult = $this->Fs_IsLockDir($this->sDirMergeLock, 60 * 5)) {
            $this->sDirMergeLock = null;
        }
        return $bResult;
    }

    /**
     * Удаляет блокировку
     */
    protected function RemoveLockMerge()
    {
        if ($this->sDirMergeLock) {
            $this->Fs_RemoveLockDir($this->sDirMergeLock);
            $this->sDirMergeLock = null;
        }
    }

    /**
     * Производит объединение и сжатие файлов
     *
     * @param      $aAssetItems
     * @param      $sType
     * @param bool $bCompress
     *
     * @return string|bool Web путь до нового файла
     */
    protected function Merge($aAssetItems, $sType, $bCompress = false)
    {
        $sCacheDir = Config::Get('path.cache_assets.server') . "/" . Config::Get('view.skin');
        $sCacheFile = $sCacheDir . "/" . md5(serialize(array_keys($aAssetItems)) . '_head') . '.' . $sType;
        /**
         * Если файла еще нет, то создаем его
         */
        if (!file_exists($sCacheFile)) {
            /**
             * Но только в том случае, если еще другой процесс не начал его создавать - проверка на блокировку
             */
            if ($this->IsLockMerge()) {
                return false;
            }
            /**
             * Создаем директорию для кеша текущего скина,
             * если таковая отсутствует
             */
            if (!is_dir($sCacheDir)) {
                @mkdir($sCacheDir, 0777, true);
            }
            $sContent = '';
            foreach ($aAssetItems as $sFile => $aParams) {
                $sFile = isset($aParams['file']) ? $aParams['file'] : $aParams['file'];
                if (strpos($sFile, '//') === 0) {
                    /**
                     * Добавляем текущий протокол
                     */
                    $sFile = (Router::GetIsSecureConnection() ? 'https' : 'http') . ':' . $sFile;
                }
                $sFile = $this->Fs_GetPathServerFromWeb($sFile);
                /**
                 * Считываем содержимое файла
                 */
                if ($sFileContent = @file_get_contents($sFile)) {
                    /**
                     * Создаем объект
                     */
                    if ($oType = $this->CreateObjectType($sType)) {
                        $oType->setContent($sFileContent);
                        $oType->setFile($sFile);
                        unset($sFileContent);
                        $oType->prepare();
                        if ($bCompress and (!isset($aParams['compress']) or $aParams['compress'])) {
                            $oType->compress();
                        }
                        $sContent .= $oType->getContent();
                        unset($oType);
                    } else {
                        $sContent .= $sFileContent;
                    }
                }
            }
            /**
             * Создаем файл и сливаем туда содержимое
             */
            @file_put_contents($sCacheFile, $sContent);
            @chmod($sCacheFile, 0766);
            /**
             * Удаляем блокировку
             */
            $this->RemoveLockMerge();
        }
        return $this->Fs_GetPathWebFromServer($sCacheFile);
    }

    /**
     * Создает и возврашает объект типа
     *
     * @param string $sType
     *
     * @return bool|ModuleAsset_EntityType
     */
    public function CreateObjectType($sType)
    {
        /**
         * Формируем имя класса для типа
         */
        $sClass = "ModuleAsset_EntityType" . func_camelize($sType);
        if (class_exists(Engine::GetEntityClass($sClass))) {
            return Engine::GetEntity($sClass);
        }
        return false;
    }

    public function GetRealpath($sPath)
    {
        if (preg_match("@^(http|https):@", $sPath)) {
            $aUrl = parse_url($sPath);
            $sPath = $aUrl['path'];

            $aParts = array();
            $sPath = preg_replace('~/\./~', '/', $sPath);
            foreach (explode('/', preg_replace('~/+~', '/', $sPath)) as $sPart) {
                if ($sPart === "..") {
                    array_pop($aParts);
                } elseif ($sPart != "") {
                    $aParts[] = $sPart;
                }
            }
            return ((array_key_exists('scheme',
                $aUrl)) ? $aUrl['scheme'] . '://' . $aUrl['host'] : "") . "/" . implode("/", $aParts);
        } else {
            return realpath($sPath);
        }
    }

    public function Shutdown()
    {
        /**
         * Удаляем блокировку
         */
        $this->RemoveLockMerge();
    }
}