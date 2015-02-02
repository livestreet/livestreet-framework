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
 * Валидатор google re-каптчи
 *
 * @package framework.modules.validate
 * @since 1.0
 */
class ModuleValidate_EntityValidatorRecaptcha extends ModuleValidate_EntityValidator
{
    /**
     * Допускать или нет пустое значение
     *
     * @var bool
     */
    public $allowEmpty = false;

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
        $sSecret = Config::Get('module.validate.recaptcha.secret_key');
        $sUrl = "https://www.google.com/recaptcha/api/siteverify?secret={$sSecret}&response={$sValue}";
        if (Config::Get('module.validate.recaptcha.use_ip')) {
            $sUrl .= '&remoteip=' . func_getIp();
        }
        if ($sData = file_get_contents($sUrl)) {
            if ($aData = @json_decode($sData, true)) {
                if (isset($aData['success']) and $aData['success']) {
                    return true;
                }
            } else {
                $this->Logger_Warning('ReCaptcha: error json decode', array('url' => $sUrl));
            }
        } else {
            $aError = error_get_last();
            $this->Logger_Warning('ReCaptcha: ' . ($aError ? $aError['message'] : 'error server request'),
                array('url' => $sUrl));
        }
        return $this->getMessage($this->Lang_Get('validate.captcha.not_valid', null, false), 'msg');
    }
}