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
 * Бекенд сессионного кеша
 *
 * @package framework.modules
 * @since 2.0
 */
class ModuleCache_EntityBackendLife extends ModuleCache_EntityBackend
{

    protected $aStoreLife = array();

    /**
     * Проверяет доступность использования текущего бекенда
     *
     * @return mixed
     */
    public function IsAvailable()
    {
        return true;
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
     * Инициализация бекенда
     *
     * @param array $aParams
     *
     * @return mixed
     */
    public function Init($aParams = array())
    {
        $this->aStoreLife = array();
    }

    /**
     * Получить значение из кеша
     *
     * @param string $sName Имя ключа
     * @return mixed|bool
     */
    public function Get($sName)
    {
        if (array_key_exists($sName, $this->aStoreLife)) {
            return @unserialize($this->aStoreLife[$sName]);
        }
        return false;
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
        $this->aStoreLife[$sName] = serialize($mData);
    }

    /**
     * Удаляет значение из кеша по ключу(имени)
     *
     * @param string $sName Имя ключа
     * @return bool
     */
    public function Delete($sName)
    {
        unset($this->aStoreLife[$sName]);
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
        if ($cMode == Zend_Cache::CLEANING_MODE_ALL) {
            $this->aStoreLife = array();
        }
    }
}