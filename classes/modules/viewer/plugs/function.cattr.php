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
 * Формирует из массива строку со списком атрибутов
 *
 * @author  Denis Shakhov
 * @param   array $params
 * @return  string
 */
function smarty_function_cattr( $params )
{
    if ( ! $params['list'] || ! is_array($params['list'])) return '';

    foreach ($params['list'] as $key => $value)
    {
        if (is_bool($value) && $value)
        {
            $result .= "$key ";
        }
        else
        {
            // Удаляем кавычки в начале и конце значения
            if ($value[0] === '"') $value = substr($value, 1, -1);

            $result .= "$key=\"$value\" ";
        }
    }

    return $result;
}