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
 * CBooleanValidator class file.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @link http://www.yiiframework.com/
 * @copyright Copyright &copy; 2008-2011 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

/**
 * Валидатор булевых значений
 *
 * @package framework.modules.validate
 * @since 1.0
 */
class ModuleValidate_EntityValidatorBoolean extends ModuleValidate_EntityValidator {
	/**
	 * Значение true
	 *
	 * @var mixed
	 */
	public $trueValue='1';
	/**
	 * Значение false
	 *
	 * @var mixed
	 */
	public $falseValue='0';
	/**
	 * Строгое сравнение с учетом типов
	 *
	 * @var bool
	 */
	public $strict=false;
	/**
	 * Допускать или нет пустое значение
	 *
	 * @var bool
	 */
	public $allowEmpty=true;

	/**
	 * Запуск валидации
	 *
	 * @param mixed $sValue	Данные для валидации
	 *
	 * @return bool|string
	 */
	public function validate($sValue) {
		if($this->allowEmpty && $this->isEmpty($sValue)) {
			return true;
		}
		if(!$this->strict && $sValue!=$this->trueValue && $sValue!=$this->falseValue || $this->strict && $sValue!==$this->trueValue && $sValue!==$this->falseValue) {
			return $this->getMessage($this->Lang_Get('validate.boolean.invalid',null,false),'msg',array('true'=>$this->trueValue,'false'=>$this->falseValue));
		}
		return true;
	}
}