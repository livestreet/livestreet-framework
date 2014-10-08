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
 * Валидатор перечислений
 *
 * @package framework.modules.validate
 * @since 1.0
 */
class ModuleValidate_EntityValidatorEnum extends ModuleValidate_EntityValidator {
	/**
	 * Допускать или нет пустое значение
	 *
	 * @var bool
	 */
	public $allowEmpty=true;
	/**
	 * Массив разрешенных элементов
	 *
	 * @var array
	 */
	public $enum=array();

	/**
	 * Запуск валидации
	 *
	 * @param mixed $sValue    			Данные для валидации
	 * @return bool|string
	 */
	public function validate($sValue) {
		/**
		 * Проверка типа значения
		 */
		if (!is_scalar($sValue)) {
			return $this->getMessage($this->Lang_Get('validate.enum.invalid',null,false),'msg');
		}
		/**
		 * Разрешение на пустое значение
		 */
		if ($this->allowEmpty and $this->isEmpty($sValue)) {
			return true;
		}
		/**
		 * Проверка на вхождение в перечисление
		 */
		if (!in_array($sValue,$this->enum)) {
			return $this->getMessage($this->Lang_Get('validate.enum.not_allowed',null,false),'msg',array('value'=>htmlspecialchars($sValue)));
		}
		/**
		 * Значение корректно
		 */
		return true;
	}
}