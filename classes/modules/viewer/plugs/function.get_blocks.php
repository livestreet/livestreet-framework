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
 * Загружает в переменную список блоков
 *
 * @param unknown_type $params
 * @param unknown_type $smarty
 * @return unknown
 */
function smarty_function_get_blocks($params, &$smarty)
{
    if (!array_key_exists('assign', $params)) {
        trigger_error("get_blocks: missing 'assign' parameter", E_USER_WARNING);
        return;
    }

    $smarty->assign($params['assign'], Engine::getInstance()->Viewer_GetBlocks(true));
    return '';
}