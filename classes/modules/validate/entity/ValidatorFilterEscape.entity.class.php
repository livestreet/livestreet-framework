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
 * Фильтр для экранирования строк
 *
 * @package framework.modules.validate
 * @since 1.0
 */
class ModuleValidate_EntityValidatorFilterEscape extends ModuleValidate_EntityValidator
{
    /**
     * Функция для экранирования, может быть коллбэком
     *
     * @var string
     */
    public $function = 'htmlspecialchars';
    /**
     * Допускать или нет пустое значение
     *
     * @var bool
     */
    public $allowEmpty = true;
    /**
     * Пропускать или нет ошибку
     *
     * @var bool
     */
    public $bSkipOnError = true;

    /**
     * Запуск валидации
     *
     * @param mixed $sValue Данные для валидации
     *
     * @return bool|string
     */
    public function validate($sValue)
    {
        if ($this->allowEmpty && $this->isEmpty($sValue)) {
            return true;
        }
        if (!is_scalar($sValue)) {
            return $this->getMessage($this->Lang_Get('validate.string.not_valid', null, false), 'msg');
        }

        if ($this->oEntityCurrent) {
            $sValueEscape = call_user_func($this->function, $sValue);
            $this->setValueOfCurrentEntity($this->sFieldCurrent, $sValueEscape);
        }
        return true;
    }
}