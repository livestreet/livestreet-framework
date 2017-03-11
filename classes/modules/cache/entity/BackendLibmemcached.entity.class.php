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
 * Бекенд Libmemcached
 *
 * @package framework.modules
 * @since 2.0
 */
class ModuleCache_EntityBackendLibmemcached extends ModuleCache_EntityBackend
{
    /**
     * Проверяет доступность использования текущего бекенда
     *
     * @return mixed
     */
    public function IsAvailable()
    {
        if (extension_loaded('memcached')) {
            return true;
        }
        return 'The memcached extension must be loaded for using this backend!';
    }

    /**
     * Проверяет доступность использование мульти-get запросов к кешу (указывать сразу несколько ключей)
     *
     * @return mixed
     */
    public function IsAllowMultiGet()
    {
        return true;
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
        require_once(LS_DKCACHE_PATH . 'Cache/Backend/TagEmuWrapper.php');
        require_once(LS_DKCACHE_PATH . 'Zend/Cache/Backend/Libmemcached.php');
        $aConfig = Config::Get('libmemcached');

        $oCahe = new Zend_Cache_Backend_Libmemcached(is_array($aConfig) ? $aConfig : array());
        if (isset($aParams['stats_callback'])) {
            $this->oCacheBackend = new Dklab_Cache_Backend_TagEmuWrapper(new Dklab_Cache_Backend_Profiler($oCahe,
                $aParams['stats_callback']));
        } else {
            $this->oCacheBackend = new Dklab_Cache_Backend_TagEmuWrapper($oCahe);
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
        return $this->oCacheBackend->load($sName);
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
        return $this->oCacheBackend->save($mData, $sName, $aTags, $iTimeLife);
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