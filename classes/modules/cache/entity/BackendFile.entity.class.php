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
 * Бекенд файлового кеша
 *
 * @package framework.modules
 * @since 2.0
 */
class ModuleCache_EntityBackendFile extends ModuleCache_EntityBackend
{
    /**
     * Проверяет доступность использования текущего бекенда
     *
     * @return mixed
     */
    public function IsAvailable()
    {
        $sDirCache = $this->GetCacheDir();
        if (!is_dir($sDirCache)) {
            @mkdir($sDirCache, 0777, true);
        }
        if (is_writable($sDirCache)) {
            return true;
        }
        return "cache dir '{$sDirCache}' is not writable";
    }

    /**
     * Проверяет доступность использование мульти-get запросов к кешу (указывать сразу несколько ключей)
     *
     * @return mixed
     */
    public function IsAllowMultiGet()
    {
        return false;
    }

    /**
     * Возвращает каталог для кеша
     *
     * @return string
     */
    protected function GetCacheDir()
    {
        return Config::Get('sys.cache.dir') . '/system/';
    }

    /**
     * Инициализация бекенда
     *
     * @param array $aParams
     *
     * @return mixed
     */
    public function Init($aParams = array())
    {
        require_once(LS_DKCACHE_PATH . 'Zend/Cache/Backend/File.php');
        $sDirCache = $this->GetCacheDir();
        $oCahe = new Zend_Cache_Backend_File(
            array(
                'cache_dir' => $sDirCache,
                'file_name_prefix' => Config::Get('sys.cache.prefix'),
                'read_control_type' => 'crc32',
                'hashed_directory_level' => Config::Get('sys.cache.directory_level'),
                'read_control' => true,
                'file_locking' => true,
            )
        );
        if (isset($aParams['stats_callback'])) {
            $this->oCacheBackend = new Dklab_Cache_Backend_Profiler($oCahe, $aParams['stats_callback']);
        } else {
            $this->oCacheBackend = $oCahe;
        }
    }

    /**
     * Получить значение из кеша
     *
     * @param string $sName Имя ключа
     * @return mixed|bool
     */
    public function Get($sName)
    {
        $mData = $this->oCacheBackend->load($sName);
        if ($mData and is_string($mData)) {
            return unserialize($mData);
        }
        return $mData;
    }

    /**
     * Записать значение в кеш
     *
     * @param  mixed $mData Данные для хранения в кеше
     * @param  string $sName Имя ключа
     * @param  array $aTags Список тегов, для возможности удалять сразу несколько кешей по тегу
     * @param  int|bool $iTimeLife Время жизни кеша в секундах
     * @return bool
     */
    public function Set($mData, $sName, $aTags = array(), $iTimeLife = false)
    {
        return $this->oCacheBackend->save(serialize($mData), $sName, $aTags, $iTimeLife);
    }

    /**
     * Удаляет значение из кеша по ключу(имени)
     *
     * @param string $sName Имя ключа
     * @return bool
     */
    public function Delete($sName)
    {
        return $this->oCacheBackend->remove($sName);
    }

    /**
     * Чистит кеши
     *
     * @param string $cMode Режим очистки кеша
     * @param array $aTags Список тегов, актуально для режима Zend_Cache::CLEANING_MODE_MATCHING_TAG
     * @return bool
     */
    public function Clean($cMode = Zend_Cache::CLEANING_MODE_ALL, $aTags = array())
    {
        return $this->oCacheBackend->clean($cMode, $aTags);
    }
}