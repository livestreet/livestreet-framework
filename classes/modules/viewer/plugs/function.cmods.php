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
 * Формирует список с классами-модификаторами
 *
 * @author  Denis Shakhov
 * @param   array $aParams
 * @return  string
 */
function smarty_function_cmods($aParams)
{
    if (empty($aParams['name'])) {
        trigger_error("Parameter 'name' cannot be empty", E_USER_WARNING);
        return;
    }

    // Разделитель между названием компонента и мод-ом
    $sDelimiter = $aParams['delimiter'] ?: Config::Get('view.mod_delimiter');

    // Удаляем лишние пробелы
    $sMods = trim(preg_replace('/\s+/', ' ', $aParams['mods']));

    // Получаем список модификаторов
    $aMods = array_filter(explode(' ', $sMods));

    // Устанавливаем дефолтный модификатор
    if (empty($aMods) && !empty($aParams['default'])) {
        $aMods[] = $aParams['default'];
    }

    // Формируем список с классами-модификаторами
    foreach ($aMods as $sMod) {
        $sResultMods .= $aParams['name'] . $sDelimiter . $sMod . " ";
    }

    return $sResultMods;
}