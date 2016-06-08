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
 * Склонение текста от множественного числа
 *
 * @param   array $aParams
 * @param   Smarty $oSmarty
 * @return  string
 */
function smarty_function_pluralize($aParams, &$oSmarty)
{
    if (isset($aParams['_default_short'])) {
        $aParams['text'] = $aParams['_default_short'];
    }
    if (empty($aParams['text'])) {
        trigger_error("Lang: missing 'text' parameter", E_USER_WARNING);
        return;
    }

    if (!isset($aParams['count'])) {
        $aParams['count'] = 0;
    }

    $sReturn = Engine::getInstance()->Lang_Pluralize((int)$aParams['count'], $aParams['text']);
    /**
     * Возвращаем результат
     */
    if (!empty($aParams['assign'])) {
        $oSmarty->assign($aParams['assign'], $sReturn);
    } else {
        return $sReturn;
    }
}