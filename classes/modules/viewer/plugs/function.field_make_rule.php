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
 * Конвертирует PHP правила валидации в JS
 *
 * @param array $params
 * @param Smarty $smarty
 * @return string
 */
function smarty_function_field_make_rule($params, &$smarty)
{
    $aParamsReq = array('entity', 'field');
    foreach ($aParamsReq as $sParam) {
        if (!array_key_exists($sParam, $params)) {
            trigger_error("json: missing '{$sParam}' parameter", E_USER_WARNING);
            return;
        }
    }
    $aResult = array();
    $sScenario = isset($params['scenario']) ? $params['scenario'] : '';
    $sField = $params['field'];
    $oEntity = $params['entity'];
    if (!($oEntity instanceof Entity)) {
        $oEntity = Engine::GetEntity($oEntity);
    }

    $aEntityRules = $oEntity->_getValidateRules();
    foreach ($aEntityRules as $aEntityRule) {
        $oValidator = $oEntity->Validate_CreateValidator($aEntityRule[1], $oEntity, $aEntityRule[0],
            array_slice($aEntityRule, 2));
        if (in_array($sField, $oValidator->fields) and (!$sScenario or isset($oValidator->on[$sScenario]))) {
            $sType = $oValidator->getTypeValidator();
            /**
             * Конвертация строкового валидатора
             */
            if ($sType == 'string') {
                if (!is_null($oValidator->min) and !is_null($oValidator->max)) {
                    $aResult['length'] = '[' . $oValidator->min . ',' . $oValidator->max . ']';
                } elseif (!is_null($oValidator->min)) {
                    $aResult['minlength'] = $oValidator->min;
                } else {
                    $aResult['maxlength'] = $oValidator->max;
                }
            }
            /**
             * Конвертация числового валидатора
             */
            if ($sType == 'number') {
                if ($oValidator->integerOnly) {
                    $aResult['type'] = 'digits';
                } else {
                    $aResult['type'] = 'number';
                }
                if (!is_null($oValidator->max)) {
                    $aResult['max'] = $oValidator->max;
                }
                if (!is_null($oValidator->min)) {
                    $aResult['min'] = $oValidator->min;
                }
            }
            /**
             * Конвертация почтового валидатора
             */
            if ($sType == 'email') {
                $aResult['type'] = 'email';
            }

            if ($sType != 'inline' and !$oValidator->allowEmpty) {
                $aResult['required'] = 1;
            }
        }
    }

    if (!empty($params['assign'])) {
        $smarty->assign($params['assign'], $aResult);
    } else {
        return $aResult;
    }
}