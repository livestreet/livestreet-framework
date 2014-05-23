<?php
/*-------------------------------------------------------
*
*   LiveStreet Engine Social Networking
*   Copyright © 2008 Mzhelskiy Maxim
*
*--------------------------------------------------------
*
*   Official site: www.livestreet.ru
*   Contact e-mail: rus.engine@gmail.com
*
*   GNU General Public License, version 2:
*   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*
---------------------------------------------------------
*/

/**
 * Если значение не установлено, то присваивает ему дефолтное значение
 * Данный метод валидации применим только к валадции сущностей (Entity)
 *
 * @package engine.modules.validate
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