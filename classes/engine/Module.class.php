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
 * Абстракция модуля, от которой наследуются все модули
 * Модули предназначены для объединения часто используемого функционала, т.е. некие аналоги внутренних библиотек.
 *
 * @package framework.engine
 * @since 1.0
 */
abstract class Module extends LsObject {
	/**
	 * Указывает на то, была ли проведенна инициализация модуля
	 *
	 * @var bool
	 */
	protected $bIsInit=false;

	/**
	 * Блокируем копирование/клонирование объекта
	 *
	 */
	public function __clone() {
		throw new Exception('Not allow clone module');
	}
	/**
	 * Абстрактный метод инициализации модуля, должен быть переопределен в модуле
	 *
	 */
	abstract public function Init();
	/**
	 * Метод срабатывает при завершении работы ядра
	 *
	 */
	public function Shutdown() {

	}
	/**
	 * Возвращает значение флага инициализации модуля
	 *
	 * @return bool
	 */
	public function isInit() {
		return $this->bIsInit;
	}
	/**
	 * Помечает модуль как инициализированный
	 *
	 * @return null
	 */
	public function SetInit() {
		$this->bIsInit=true;
	}
}