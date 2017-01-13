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
 * Загружает список языковых текстовок в шаблон
 *
 * @param unknown_type $params
 * @param unknown_type $smarty
 * @return unknown
 */
function smarty_function_lang_load($params, &$smarty)
{

    if (!array_key_exists('name', $params)) {
        trigger_error("lang_load: missing 'name' parameter", E_USER_WARNING);
        return;
    }

    $aLangName = explode(',', $params['name']);
    $bPrepare = (!empty($params['prepare']) and $params['prepare']) ? true : false;

    $aLangMsg = array();
    foreach ($aLangName as $sName) {
        if ($bPrepare) {
            $aLangMsg[] = trim($sName);
        } else {
            $aLangMsg[trim($sName)] = Engine::getInstance()->Lang_Get(trim($sName), array(), false);
        }
    }
    if ($bPrepare) {
        Engine::getInstance()->Lang_AddLangJs($aLangMsg);
        return;
    }

    if (!isset($params['json']) or $params['json'] !== false) {
        $aLangMsg = json_encode($aLangMsg);
    }

    if (!empty($params['assign'])) {
        $smarty->assign($params['assign'], $aLangMsg);
    } else {
        return $aLangMsg;
    }
}