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
 * Инициализирует параметры компонета
 *
 * @param   array $aParams
 * @param   Smarty $oSmarty
 * @return  string
 */
function smarty_function_component_define_params($aParams, &$oSmarty)
{
    if (isset($aParams['params'])) {
        if (is_array($aParams['params'])) {
            $aComponentParams = $aParams['params'];
        } else {
            $aComponentParams = explode(',', $aParams['params']);
        }
    } else {
        trigger_error("component_define_params: missing 'params' parameter", E_USER_WARNING);
        return;
    }

    $aLocalVars = $oSmarty->tpl_vars_local;
    foreach ($aComponentParams as $sParamName) {
        if (array_key_exists($sParamName, $aLocalVars)) {
            $oSmarty->assign($sParamName, $aLocalVars[$sParamName]->value);
        } else {
            $oSmarty->assign($sParamName, null);
        }
    }

    return false;
}