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
 * Абстрактный класс блока
 * Это те блоки которые обрабатывают шаблоны Smarty перед выводом(например блок "Облако тегов")
 *
 * @package framework.engine
 * @since 1.0
 */
abstract class Block extends LsObject {
	/**
	 * Список параметров блока
	 *
	 * @var array
	 */
	protected $aParams=array();
	/**
	 * Шаблон блока
	 *
	 * @var string|null
	 */
	protected $sTemplate=null;

	/**
	 * При создании блока передаем в него параметры
	 *
	 * @param array $aParams Список параметров блока
	 */
	public function __construct($aParams) {
		parent::__construct();
		$this->aParams=$aParams;
	}
	/**
	 * Возвращает параметр по имени
	 *
	 * @param string $sName	Имя параметра
	 * @param null|mixed $def	Дефолтное значение параметра, возвращается если такого параметра нет
	 * @return mixed
	 */
	protected function GetParam($sName,$def=null) {
		if (isset($this->aParams[$sName])) {
			return $this->aParams[$sName];
		} else {
			return $def;
		}
	}
	/**
	 * Возврашает шаблон блока
	 *
	 * @return null|string
	 */
	public function GetTemplate() {
		return $this->sTemplate;
	}
	/**
	 * Устанавливает шаблон блока
	 *
	 * @param string $sTemplate Путь до файла шаблона
	 */
	public function SetTemplate($sTemplate) {
		$this->sTemplate=$sTemplate;
	}
	/**
	 * Метод запуска обработки блока.
	 * Его необходимо определять в конкретном блоге.
	 *
	 * @abstract
	 */
	abstract public function Exec();
}