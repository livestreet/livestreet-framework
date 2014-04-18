<?php
/**
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
 * Абстрактный объект бекенда кеша, от него должны наследоваться все конечные бекенды
 */
abstract class ModuleCache_EntityBackend {
	/**
	 * Объект бекенда кеша
	 *
	 * @var null|object
	 */
	protected $oCacheBackend=null;

	/**
	 * Инициализация бекенда
	 *
	 * @param array $aParams
	 *
	 * @return mixed
	 */
	abstract public function Init($aParams=array());
	/**
	 * Проверяет доступность использования текущего бекенда
	 *
	 * @return mixed
	 */
	abstract public function IsAvailable();
	/**
	 * Проверяет доступность использование мульти-get запросов к кешу (указывать сразу несколько ключей)
	 *
	 * @return mixed
	 */
	abstract public function IsAllowMultiGet();
	/**
	 * Получить значение из кеша
	 *
	 * @param string $sName	Имя ключа
	 * @return mixed|bool
	 */
	abstract public function Get($sName);
	/**
	 * Записать значение в кеш
	 *
	 * @param  mixed  $mData	Данные для хранения в кеше
	 * @param  string $sName	Имя ключа
	 * @param  array  $aTags	Список тегов, для возможности удалять сразу несколько кешей по тегу
	 * @param  int|bool    $iTimeLife	Время жизни кеша в секундах
	 * @return bool
	 */
	abstract public function Set($mData,$sName,$aTags=array(),$iTimeLife=false);
	/**
	 * Удаляет значение из кеша по ключу(имени)
	 *
	 * @param string $sName	Имя ключа
	 * @return bool
	 */
	abstract public function Delete($sName);
	/**
	 * Чистит кеши
	 *
	 * @param string $cMode	Режим очистки кеша
	 * @param array $aTags	Список тегов, актуально для режима Zend_Cache::CLEANING_MODE_MATCHING_TAG
	 * @return bool
	 */
	abstract public function Clean($cMode=Zend_Cache::CLEANING_MODE_ALL,$aTags=array());

}