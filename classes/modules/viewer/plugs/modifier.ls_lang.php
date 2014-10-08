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

function smarty_modifier_ls_lang($sString)
{
    $aArgs = func_get_args();
    if (count($aArgs) == 1 || !$sString) {
        return $sString;
    }
    array_shift($aArgs);
    $aFrom = array();
    $aTo = array();
    foreach ($aArgs as $sPair) {
        if (!strpos($sPair, '%%')) {
            continue;
        }
        list ($sFrom, $sTo) = explode('%%', $sPair);
        $aFrom[] = '%%' . $sFrom . '%%';
        $aTo[] = $sTo;
    }
    return str_replace($aFrom, $aTo, $sString);
} 