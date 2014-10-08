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
 * Если значение не установлено, то присваивает ему дефолтное значение
 * Данный метод валидации применим только к валадции сущностей (Entity)
 *
 * @package framework.modules.validate
 * @since 1.0
 */
class ModuleValidate_EntityValidatorDefault extends ModuleValidate_EntityValidator {
	/**
	 * Допускать или нет пустое значение
	 *
	 * @var bool
	 */
	public $allowEmpty=true;
	/**
	 * Дефолтное значение
	 *
	 * @var mixed
	 */
	public $value=null;

	/**
	 * Запуск валидации
	 *
	 * @param mixed $sValue    			Данные для валидации
	 * @return bool|string
	 */
	public function validate($sValue) {
		/**
		 * Выставляем дефолтное значение
		 */
		if ($this->isEmpty($sValue) and $this->oEntityCurrent) {
			$this->setValueOfCurrentEntity($this->sFieldCurrent,$this->value);
		}
		return true;
	}
}