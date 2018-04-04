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
 * Плагин для смарти.
 * Позволяет получать дату с возможностью склонения
 * формы слова и поддержкой мультиязычноти.
 *
 * Список ключей параметров:
 *  date*          [string]
 *  format*        [string]
 *  declination*   [int]
 *  now*           [int]    Количество секунд, в течении которых событие имеет статус "Только что"
 *  day*           [string] Указывает на необходимость замены "Сегодня", "Вчера", "Завтра". 
 *                          В указанном формате 'day' будет заменено на соответствующее значение.
 *  minutes_back*  [int]    Количество минут, в течении которых событие имеет статус "... минут назад"
 *  hours_back*    [int]    Количество часов, в течении которых событие имеет статус "... часов назад"
 *  days_back*     [int]    Количество дней, в течении которых событие имеет статус "... дней назад"
 *  weeks_back*    [int]    Количество недель, в течении которых событие имеет статус "... недель назад"
 *  months_back*   [int]    Количество месяцев, в течении которых событие имеет статус "... месяцев назад"
 *  years_back*    [int]    Количество лет, в течении которых событие имеет статус "... лет назад"
 *  tz*            [float]  Временная зона
 *  notz*          [bool]   Не учитывать зону
 *
 * (* - параметр является необязательным)
 *
 * @param   array $aParams
 * @param   Smarty $oSmarty
 * @return  string
 */
function smarty_function_date_format($aParams, &$oSmarty)
{
    $oEngine = Engine::getInstance();

    $sResult = $oEngine->Viewer_GetDateFormat(
        isset($aParams['date']) ? $aParams['date'] : null,
        isset($aParams['format']) ? $aParams['format'] : 'd F Y, H:i',
        array_intersect_key($aParams, array_flip(array('declination', 'now', 'day', 'minutes_back', 'hours_back', 'days_back', 'weeks_back', 'months_back', 'years_back', 'tz', 'notz')))
    );
    if (!empty($aParams['assign'])) {
        $oSmarty->assign($aParams['assign'], $sResult);
    } else {
        return $sResult;
    }
    return '';
}
