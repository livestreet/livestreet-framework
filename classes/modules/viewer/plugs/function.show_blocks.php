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
 * Показывает блоки определенно группы
 *
 * @param unknown_type $params
 * @param unknown_type $smarty
 * @return unknown
 */
function smarty_function_show_blocks($aParams, &$oSmarty)
{
    if (!array_key_exists('group', $aParams)) {
        trigger_error("show_blocks: missing 'group' parameter", E_USER_WARNING);
        return;
    }

    $sGroup = $aParams['group'];
    $aBlocks = Engine::getInstance()->Viewer_GetBlocks(true);

    $sResult = '';
    if (isset($aBlocks[$sGroup]) and is_array($aBlocks[$sGroup])) {
        $oSmarty->loadPlugin('smarty_insert_block');
        foreach ($aBlocks[$sGroup] as $aBlock) {
            if ($aBlock['type'] == 'block') {
                $sResult .= smarty_insert_block(array('block' => $aBlock['name'], 'params' => isset($aBlock['params']) ? $aBlock['params'] : array()),
                    $oSmarty);
            } elseif ($aBlock['type'] == 'template') {
                $sResult .= $oSmarty->getSubTemplate($aBlock['name'], $oSmarty->cache_id, $oSmarty->compile_id, null, null,
                    array('params' => isset($aBlock['params']) ? $aBlock['params'] : array()), Smarty::SCOPE_LOCAL);
            }
        }
    }
    if (!empty($aParams['assign'])) {
        $oSmarty->assign($aParams['assign'], $sResult);
    } else {
        return $sResult;
    }
}