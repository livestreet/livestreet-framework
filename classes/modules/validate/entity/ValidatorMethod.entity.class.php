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
 * Валидатор значений через вызов внешнего метода
 * В аргументах вызова метода передается один параметр - значение для валидации
 * Проверка происходит на нестрогое соответвие !=false результата выполнения метода
 *
 * @package framework.modules.validate
 * @since 1.0
 */
class ModuleValidate_EntityValidatorMethod extends ModuleValidate_EntityValidator
{
    /**
     * Допускать или нет пустое значение
     *
     * @var bool
     */
    public $allowEmpty = true;
    /**
     * Полное название метода для проверки, метод будет вызваться через объект $this
     *
     * @var string
     */
    public $method = null;

    /**
     * Запуск валидации
     *
     * @param mixed $sValue Данные для валидации
     * @return bool|string
     */
    public function validate($sValue)
    {
        /**
         * Проверка типа значения
         */
        if (!$this->method) {
            return $this->getMessage($this->Lang_Get('validate.method.invalid', null, false), 'msg');
        }
        /**
         * Разрешение на пустое значение
         */
        if ($this->allowEmpty and $this->isEmpty($sValue)) {
            return true;
        }
        /**
         * Проверяем значение внешнего метода
         */
        if (!call_user_func(array($this, $this->method), $sValue)) {
            return $this->getMessage($this->Lang_Get('validate.method.error', null, false), 'msg');
        }
        return true;
    }
}