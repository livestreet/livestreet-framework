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
 * Валидатор каптчи (число с картинки)
 *
 * @package framework.modules.validate
 * @since 1.0
 */
class ModuleValidate_EntityValidatorCaptcha extends ModuleValidate_EntityValidator
{
    /**
     * Допускать или нет пустое значение
     *
     * @var bool
     */
    public $allowEmpty = false;
    /**
     * Название каптчи для возможности создавать несколько независимых каптч на странице
     *
     * @var string
     */
    public $name = '';

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
            return $this->getMessage($this->Lang_Get('validate.captcha.not_valid', null, false), 'msg');
        }
        if ($this->allowEmpty && $this->isEmpty($sValue)) {
            return true;
        }

        $sSessionName = 'captcha_keystring' . ($this->name ? '_' . $this->name : '');
        if (!isset($_SESSION[$sSessionName]) or $_SESSION[$sSessionName] != strtolower($sValue)) {
            return $this->getMessage($this->Lang_Get('validate.captcha.not_valid', null, false), 'msg');
        }
        return true;
    }
}