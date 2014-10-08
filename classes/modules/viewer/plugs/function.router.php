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
 * Позволяет получать данные о роутах
 *
 * @param   array $aParams
 * @param   Smarty $oSmarty
 * @return  string
 */
function smarty_function_router($aParams, &$oSmarty)
{
    if (isset($aParams['_default_short'])) {
        $aParams['page'] = $aParams['_default_short'];
    }
    if (empty($aParams['page'])) {
        trigger_error("Router: missing 'page' parametr", E_USER_WARNING);
        return;
    }
    require_once(Config::Get('path.framework.server') . '/classes/engine/Router.class.php');

    if (!$sPath = Router::GetPath($aParams['page'])) {
        trigger_error("Router: unknown 'page' given", E_USER_WARNING);
        return;
    }
    /**
     * Возвращаем полный адрес к указаному Action
     */
    $sReturn = (isset($aParams['extend']))
        ? $sPath . $aParams['extend'] . "/"
        : $sPath;

    if (!empty($aParams['assign'])) {
        $oSmarty->assign($aParams['assign'], $sReturn);
    } else {
        return $sReturn;
    }
}