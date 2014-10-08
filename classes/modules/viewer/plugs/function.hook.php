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
 * Плагин для смарти
 * Запускает хуки из шаблона на выполнение
 *
 * @param   array $aParams
 * @param   Smarty $oSmarty
 * @return  string
 */
function smarty_function_hook($aParams, &$oSmarty)
{
    if (empty($aParams['run'])) {
        trigger_error("Hook: missing 'run' parametr", E_USER_WARNING);
        return;
    }

    $sHookName = 'template_' . strtolower($aParams['run']);
    unset($aParams['run']);
    $aResultHook = Engine::getInstance()->Hook_Run($sHookName, $aParams);

    if (isset($aParams['array']) and $aParams['array']) {
        /**
         * Если хуку необходимо вернуть результат в виде массива
         */
        $mReturn = array();
        if (array_key_exists('template_result', $aResultHook)) {
            foreach ($aResultHook['template_result'] as $aResultItem) {
                if (is_array($aResultItem) and $aResultItem) {
                    $mReturn = array_merge($mReturn, $aResultItem);
                }
            }
        }
    } else {
        /**
         * Стандартное поведение хука - результат в виде строки
         */
        $mReturn = '';
        if (array_key_exists('template_result', $aResultHook)) {
            $mReturn = join('', $aResultHook['template_result']);
        }
    }

    if (!empty($aParams['assign'])) {
        $oSmarty->assign($aParams['assign'], $mReturn);
    } else {
        return $mReturn;
    }
}