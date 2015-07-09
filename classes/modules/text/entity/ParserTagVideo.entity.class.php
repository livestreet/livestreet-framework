<?php
/*
 * LiveStreet CMS
 * Copyright © 2015 OOO "ЛС-СОФТ"
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
 * @copyright 2015 OOO "ЛС-СОФТ"
 * @author Maxim Mzhelskiy <rus.engine@gmail.com>
 *
 */

/**
 * Сущность для парсинга тега <video>
 *
 * @package framework.modules.text
 * @since 2.0
 */
class ModuleText_EntityParserTagVideo extends Entity
{
    /**
     * Запускает парсинг контента тега
     *
     * @param $sContent
     * @param array $aParams
     * @return bool|string
     */
    public function parse($sContent, $aParams = array())
    {
        if ($sReturn = $this->parseYoutube($sContent)) {
            return $sReturn;
        }
        if ($sReturn = $this->parseVimeo($sContent)) {
            return $sReturn;
        }
        if ($sReturn = $this->parseYandex($sContent)) {
            return $sReturn;
        }

        return false;
    }

    protected function parseYoutube($sContent)
    {
        if (preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[\w\-?&!#=,;]+/[\w\-?&!#=/,;]+/|(?:v|e(?:mbed)?)/|[\w\-?&!#=,;]*[?&]v=)|youtu\.be/)([\w-]{11})(?:[^\w-]|\Z)%i',
            $sContent, $aMatch)) {
            return '<iframe width="560" height="315" src="https://www.youtube.com/embed/' . $aMatch[1] . '?rel=0" frameborder="0" allowfullscreen></iframe>';
        }
        return false;
    }

    protected function parseVimeo($sContent)
    {
        if (preg_match('#(?:www\.|)vimeo\.com\/(\d+).*#i', $sContent, $aMatch)) {
            return '<iframe src="https://player.vimeo.com/video/' . $aMatch[1] . '" width="500" height="281" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>';
        }
        return false;
    }

    protected function parseYandex($sContent)
    {
        if (preg_match('#video\.yandex\.ru\/users\/([a-zA-Z0-9_\-]+)\/view\/(\d+).*#i', $sContent, $aMatch)) {
            return '<iframe width="450" height="253" frameborder="0" src="https://video.yandex.ru/users/' . $aMatch[1] . '/view/' . $aMatch[2] . '/get-object-by-url/redirect" allowfullscreen="1"> </iframe>';
        }
        return false;
    }
}