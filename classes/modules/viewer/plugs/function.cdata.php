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
 * Формирует список data-атрибутов с заданным префиксом
 *
 * @author  Denis Shakhov
 * @param   array $aParams
 * @return  string
 */
function smarty_function_cdata($aParams)
{
    if (empty($aParams['name'])) {
        trigger_error("Parameter 'name' cannot be empty", E_USER_WARNING);
        return;
    }

    $aParams['prefix'] = "data-" . str_replace("-", "", $aParams['name']) . "-";

    return smarty_function_cattr($aParams);
}