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

require_once(Config::Get('path.framework.libs_vendor.server') . '/DklabCache/config.php');
require_once(LS_DKCACHE_PATH . 'Zend/Cache.php');
require_once(LS_DKCACHE_PATH . 'Cache/Backend/Profiler.php');

/**
 * Модуль кеширования.
 * Для реализации кеширования используетс библиотека Zend_Cache с бэкэндами File, Memcache и XCache.
 * Т.к. в memcache нет встроенной поддержки тегирования при кешировании, то для реализации тегов используется враппер от Дмитрия Котерова - Dklab_Cache_Backend_TagEmuWrapper.
 *
 * Пример использования:
 * <pre>
 *    // Получает пользователя по его логину
 *    public function GetUserByLogin($sLogin) {
 *        // Пытаемся получить значение из кеша
 *        if (false === ($oUser = $this->Cache_Get("user_login_{$sLogin}"))) {
 *            // Если значение из кеша получить не удалось, то обращаемся к базе данных
 *            $oUser = $this->oMapper->GetUserByLogin($sLogin);
 *            // Записываем значение в кеш
 *            $this->Cache_Set($oUser, "user_login_{$sLogin}", array(), 60*60*24*5);
 *        }
 *        return $oUser;
 *    }
 *
 *    // Обновляет пользователя в БД
 *    public function UpdateUser($oUser) {
 *        // Удаляем кеш конкретного пользователя
 *        $this->Cache_Delete("user_login_{$oUser->getLogin()}");
 *        // Удалем кеш со списком всех пользователей
 *        $this->Cache_Clean(Zend_Cache::CLEANING_MODE_MATCHING_TAG,array('user_update'));
 *        // Обновлем пользовател в базе данных
 *        return $this->oMapper->UpdateUser($oUser);
 *    }
 *
 *    // Получает список всех пользователей
 *    public function GetUsers() {
 *        // Пытаемся получить значение из кеша
 *        if (false === ($aUserList = $this->Cache_Get("users"))) {
 *            // Если значение из кеша получить не удалось, то обращаемся к базе данных
 *            $aUserList = $this->oMapper->GetUsers();
 *            // Записываем значение в кеш
 *            $this->Cache_Set($aUserList, "users", array('user_update'), 60*60*24*5);
 *        }
 *        return $aUserList;
 *    }
 * </pre>
 *
 * @package framework.modules
 * @since 1.0
 */
class ModuleCache extends Module
{

    /**
     * Список бекендов кеширования
     *
     * @var array
     */
    protected $aCacheBackends = array();
    /**
     * Дефолтный тип кеширования
     *
     * @var string|null
     */
    protected $sCacheType = null;
    /**
     * Разрешать или нет кеширование
     *
     * @var bool
     */
    protected $bAllowUse = false;
    /**
     * Возможность принудительно использовать кешировоание, даже если оно отключено в конфиге
     *
     * @var bool
     */
    protected $bAllowForce = true;
    /**
     * Статистика кеширования
     *
     * @var array
     */
    protected $aStats = array(
        'time'      => 0,
        'count'     => 0,
        'count_get' => 0,
        'count_set' => 0,
    );
    /**
     * Префикс для "умного" кеширования
     * @see SmartSet
     * @see SmartGet
     *
     * @var string
     */
    protected $sPrefixSmartCache = 'for-smart-cache-';

    /**
     * Инициализация
     */
    public function Init()
    {
        $this->InitParams();
    }

    /**
     * Инициализация необходимых параметров модуля
     */
    public function InitParams()
    {
        $this->bAllowUse = (bool)Config::Get('sys.cache.use');
        if (is_bool(Config::Get('sys.cache.force'))) {
            $this->bAllowForce = Config::Get('sys.cache.force');
        }
        $this->sCacheType = strtolower(Config::Get('sys.cache.type'));
    }

    /**
     * Удаляет старый кеш в случайном порядке
     * Рекомендуется запускать раз в пару дней из под крона
     */
    public function ClearOldCache()
    {
        $this->Clean(Zend_Cache::CLEANING_MODE_OLD);
    }

    /**
     * Возвращает объект бекенда кеша
     *
     * @param string|null $sCacheType Тип кеша
     *
     * @return ModuleCache_EntityBackend    Объект бекенда кеша
     * @throws Exception
     */
    protected function GetCacheBackend($sCacheType = null)
    {
        if ($sCacheType) {
            $sCacheType = strtolower($sCacheType);
        } else {
            $sCacheType = $this->sCacheType;
        }
        /**
         * Устанавливает алиас memory == memcached
         */
        if ($sCacheType == 'memory') {
            $sCacheType = 'memcached';
        }
        if (isset($this->aCacheBackends[$sCacheType])) {
            return $this->aCacheBackends[$sCacheType];
        }
        $sCacheTypeCam = func_camelize($sCacheType);
        /**
         * Формируем имя класса бекенда
         */
        $sClass = "ModuleCache_EntityBackend{$sCacheTypeCam}";
        $sClass = Engine::GetEntityClass($sClass);
        if (class_exists($sClass)) {
            /**
             * Создаем объект и проверяем доступность его использования
             */
            $oBackend = new $sClass;
            if (true === ($mResult = $oBackend->IsAvailable())) {
                $oBackend->Init(array('stats_callback' => array($this, 'CalcStats')));
                $this->aCacheBackends[$sCacheType] = $oBackend;
                return $oBackend;
            } else {
                throw new Exception("Cache '{$sCacheTypeCam}' not available: {$mResult}");
            }
        }
        throw new Exception("Not found class for cache type: " . $sCacheTypeCam);
    }

    /**
     * Формирует хеш от имени ключа кеша
     *
     * @param string $sName Имя ключа кеша
     *
     * @return string
     */
    protected function HashName($sName)
    {
        return md5(Config::Get('sys.cache.prefix') . $sName);
    }

    /**
     * Получить значение из кеша
     *
     * @param string|array $sName Имя ключа
     * @param string|null $sCacheType Тип кеша
     * @param bool $bForce Принудительно использовать кеширование, даже если оно отключено в конфиге
     * @param bool $bKeepInMemory Если true, то данные дополнительно будут сохранены в памяти на время выполнения запроса (скрипта).
     *                                В результате чего повторные Get() запросы к кешу будут значительно быстрее. Параметр не поддерживает мульти-запросы к кешу.
     *                                Данные параметр следует использовать очень осторожно, т.к. кешированные данные нельзя будет обновить/удалить до конца выполнения запроса.
     *
     * @return mixed|bool
     */
    public function Get($sName, $sCacheType = null, $bForce = false, $bKeepInMemory = false)
    {
        if (!$this->bAllowUse and !($this->bAllowForce and $bForce)) {
            return false;
        }
        /**
         * Запрос сразу на несколько ключей?
         */
        if (is_array($sName)) {
            return $this->MultiGet($sName, $sCacheType);
        }
        /**
         * При необходимости смотрим в памяти
         */
        if ($bKeepInMemory) {
            $sKeyMemory = 'auto_keep_in_memory_' . $sName;
            if (false !== ($mData = $this->GetLife($sKeyMemory))) {
                return $mData;
            }
        }

        $oCacheBackend = $this->GetCacheBackend($sCacheType);
        $mData = $oCacheBackend->Get($this->HashName($sName));
        /**
         * Сохраняем в памяти
         */
        if ($bKeepInMemory and $mData !== false) {
            $this->SetLife($mData, $sKeyMemory);
        }
        return $mData;
    }

    /**
     * Получения значения из "умного" кеша для борьбы с конкурирующими запросами
     * Если кеш "протух", и за ним обращаются много запросов, то только первый запрос вернет FALSE, остальные будут получать чуть устаревшие данные из временного кеша, пока их не обновит первый запрос
     * Текущая реализация имеет недостаток - размер кеша увеличивается в два раза
     *
     * @param string $sName Имя ключа
     * @param string|null $sCacheType Тип кеша
     * @param bool $bForce Принудительно использовать кеширование, даже если оно отключено в конфиге
     *
     * @return bool|mixed
     */
    public function SmartGet($sName, $sCacheType = null, $bForce = false)
    {
        if (!$this->bAllowUse and !($this->bAllowForce and $bForce)) {
            return false;
        }
        /**
         * Если данных в основном кеше нет, то перекладываем их из временного
         */
        if (($data = $this->Get($sName, $sCacheType, $bForce)) === false) {
            $this->Set($this->Get($this->sPrefixSmartCache . $sName, $sCacheType, $bForce), $sName, array(), 60,
                $sCacheType, $bForce); // храним данные из временного в основном не долго
        }
        return $data;
    }

    /**
     * Поддержка мульти-запросов к кешу
     * Такие запросы поддерживает только memcached, поэтому для остальных типов делаем эмуляцию
     *
     * @param  array $aName Имя ключа
     * @param  string|null $sCacheType Тип кеша
     * @return bool|array
     */
    protected function MultiGet($aName, $sCacheType = null)
    {
        if (!count($aName)) {
            return false;
        }
        $oCacheBackend = $this->GetCacheBackend($sCacheType);
        if ($oCacheBackend->IsAllowMultiGet()) {
            $aKeys = array();
            $aKv = array();
            foreach ($aName as $sName) {
                $sHash = $this->HashName($sName);
                $aKeys[] = $sHash;
                $aKv[$sHash] = $sName;
            }
            $data = $oCacheBackend->Get($aKeys);
            if ($data and is_array($data)) {
                $aData = array();
                foreach ($data as $key => $value) {
                    $aData[$aKv[$key]] = $value;
                }
                if (count($aData) > 0) {
                    return $aData;
                }
            }
            return false;
        } else {
            $aData = array();
            foreach ($aName as $sName) {
                if ((false !== ($data = $oCacheBackend->Get($this->HashName($sName))))) {
                    $aData[$sName] = $data;
                }
            }
            if (count($aData) > 0) {
                return $aData;
            }
            return false;
        }
    }

    /**
     * Записать значение в кеш
     *
     * @param  mixed $mData Данные для хранения в кеше
     * @param  string $sName Имя ключа
     * @param  array|string $aTags Список тегов, для возможности удалять сразу несколько кешей по тегу
     * @param  int|bool $iTimeLife Время жизни кеша в секундах
     * @param string|null $sCacheType Тип кеша
     * @param bool $bForce Принудительно использовать кеширование, даже если оно отключено в конфиге
     *
     * @return bool
     */
    public function Set($mData, $sName, $aTags = array(), $iTimeLife = false, $sCacheType = null, $bForce = false)
    {
        if (!$this->bAllowUse and !($this->bAllowForce and $bForce)) {
            return false;
        }
        if (!is_array($aTags)) {
            $aTags = array($aTags);
        }
        /**
         * Переводим теги с нижний регистр
         */
        $aTags = array_map('strtolower', $aTags);
        /**
         * Сохраняем данные в кеш
         */
        $oCacheBackend = $this->GetCacheBackend($sCacheType);
        return $oCacheBackend->Set($mData, $this->HashName($sName), $aTags, $iTimeLife);
    }

    /**
     * Устанавливаем значение в "умном" кеша для борьбы с конкурирующими запросами
     * Дополнительно сохраняет значение во временном кеше на чуть большее время
     *
     * @param mixed $data Данные для хранения в кеше
     * @param string $sName Имя ключа
     * @param array $aTags Список тегов, для возможности удалять сразу несколько кешей по тегу
     * @param int|bool $iTimeLife Время жизни кеша в секундах
     * @param string|null $sCacheType Тип кеша
     * @param bool $bForce Принудительно использовать кеширование, даже если оно отключено в конфиге
     *
     * @return bool
     */
    public function SmartSet($data, $sName, $aTags = array(), $iTimeLife = false, $sCacheType = null, $bForce = false)
    {
        $this->Set($data, $this->sPrefixSmartCache . $sName, array(), $iTimeLife !== false ? $iTimeLife + 60 : false,
            $sCacheType, $bForce);
        return $this->Set($data, $sName, $aTags, $iTimeLife, $sCacheType, $bForce);
    }

    /**
     * Метод для удобного обращения к кешу с одновременной записью в него (если данных не было в кеше)
     *
     * @param string $sName Имя ключа
     * @param callable $callback Коллбэк функция, которая должна возвращать данные
     * @param int $iTimeLife Время жизни кеша в секундах
     * @param array $aTags Список тегов, для возможности удалять сразу несколько кешей по тегу
     * @param null $sCacheType Тип кеша
     * @param bool $bForce Принудительно использовать кеширование, даже если оно отключено в конфиге
     * @return bool|mixed
     */
    public function Remember($sName, \Closure $callback, $iTimeLife = 0, $aTags = array(), $sCacheType = null, $bForce = false) {
        if (!$this->bAllowUse and !($this->bAllowForce and $bForce)) {
            return $callback();
        }

        if (false !== ($mData = $this->Get($sName, $sCacheType, $bForce))) {
            return $mData;
        }
        $this->Set($mData = $callback(), $sName, $aTags, $iTimeLife, $sCacheType, $bForce);
        return $mData;
    }

    /**
     * Удаляет значение из кеша по ключу(имени)
     *
     * @param string $sName
     * @param string|null $sCacheType
     * @param bool $bForce
     *
     * @return bool
     */
    public function Delete($sName, $sCacheType = null, $bForce = false)
    {
        if (!$this->bAllowUse and !($this->bAllowForce and $bForce)) {
            return false;
        }
        /**
         * Удаляем данные их кеша
         */
        $oCacheBackend = $this->GetCacheBackend($sCacheType);
        return $oCacheBackend->Delete($this->HashName($sName));
    }

    /**
     * Чистит кеши
     *
     * @param string $cMode Режим очистки кеша
     * @param array $aTags Список тегов, актуально для режима Zend_Cache::CLEANING_MODE_MATCHING_TAG
     * @param string|null $sCacheType Тип кеша
     * @param bool $bForce Принудительно использовать кеширование, даже если оно отключено в конфиге
     *
     * @return bool
     */
    public function Clean($cMode = Zend_Cache::CLEANING_MODE_ALL, $aTags = array(), $sCacheType = null, $bForce = false)
    {
        if (!$this->bAllowUse and !($this->bAllowForce and $bForce)) {
            return false;
        }
        if (!is_array($aTags)) {
            $aTags = array($aTags);
        }
        /**
         * Переводим теги с нижний регистр
         */
        $aTags = array_map('strtolower', $aTags);
        $oCacheBackend = $this->GetCacheBackend($sCacheType);
        return $oCacheBackend->Clean($cMode, $aTags);
    }

    /**
     * Получает значение из текущего кеша сессии
     *
     * @param string $sName Имя ключа
     * @return mixed
     */
    public function GetLife($sName)
    {
        return $this->Get($sName, 'life', true);
    }

    /**
     * Сохраняет значение в кеше на время исполнения скрипта(сессии), некий аналог Registry
     *
     * @param mixed $mData Данные для сохранения в кеше
     * @param string $sName Имя ключа
     */
    public function SetLife($mData, $sName)
    {
        $this->Set($mData, $sName, array(), false, 'life', true);
    }

    /**
     * Подсчет статистики использования кеша
     *
     * @param int $iTime Время выполнения метода
     * @param string $sMethod имя метода
     */
    public function CalcStats($iTime, $sMethod)
    {
        $this->aStats['time'] += $iTime;
        $this->aStats['count']++;
        if ($sMethod == 'Dklab_Cache_Backend_Profiler::load') {
            $this->aStats['count_get']++;
        }
        if ($sMethod == 'Dklab_Cache_Backend_Profiler::save') {
            $this->aStats['count_set']++;
        }
    }

    /**
     * Возвращает статистику использования кеша
     *
     * @return array
     */
    public function GetStats()
    {
        return $this->aStats;
    }
}