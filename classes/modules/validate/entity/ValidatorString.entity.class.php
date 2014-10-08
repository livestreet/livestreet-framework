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
 * CStringValidator class file.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @link http://www.yiiframework.com/
 * @copyright Copyright &copy; 2008-2011 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

/**
 * Валидатор текстовых данных на длину
 *
 * @package framework.modules.validate
 * @since 1.0
 */
class ModuleValidate_EntityValidatorString extends ModuleValidate_EntityValidator
{
    /**
     * Максимальня длина строки
     *
     * @var int
     */
    public $max;
    /**
     * Минимальная длина строки
     *
     * @var int
     */
    public $min;
    /**
     * Конкретное значение длины строки
     *
     * @var int
     */
    public $is;
    /**
     * Кастомное сообщение об ошибке при короткой строке
     *
     * @var string
     */
    public $msgTooShort;
    /**
     * Кастомное сообщение об ошибке при слишком длинной строке
     *
     * @var string
     */
    public $msgTooLong;
    /**
     * Допускать или нет пустое значение
     *
     * @var bool
     */
    public $allowEmpty = true;

    /**
     * Запуск валидации
     *
     * @param mixed $sValue Данные для валидации
     *
     * @return bool|string
     */
    public function validate($sValue)
    {
        if (is_array($sValue)) {
            return $this->getMessage($this->Lang_Get('validate.string.too_short', null, false), 'msgTooShort', array('min' => $this->min));
        }
        if ($this->allowEmpty && $this->isEmpty($sValue)) {
            return true;
        }

        $iLength = mb_strlen($sValue, 'UTF-8');

        if ($this->min !== null && $iLength < $this->min) {
            return $this->getMessage($this->Lang_Get('validate.string.too_short', null, false), 'msgTooShort', array('min' => $this->min));
        }
        if ($this->max !== null && $iLength > $this->max) {
            return $this->getMessage($this->Lang_Get('validate.string.too_long', null, false), 'msgTooLong', array('max' => $this->max));
        }
        if ($this->is !== null && $iLength !== $this->is) {
            return $this->getMessage($this->Lang_Get('validate.string.no_lenght', null, false), 'msg', array('length' => $this->is));
        }
        if (!$this->allowEmpty && $this->isEmpty($sValue)) {
            return $this->getMessage($this->Lang_Get('validate.empty_error', null, false), 'msg');
        }
        return true;
    }
}