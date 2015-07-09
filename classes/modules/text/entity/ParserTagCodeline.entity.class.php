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
 * Сущность для парсинга тега <codeline>
 *
 * @package framework.modules.text
 * @since 2.0
 */
class ModuleText_EntityParserTagCodeline extends Entity
{
    /**
     * Запускает обработку контента тега
     *
     * @param $sContent
     * @param array $aParams
     * @return bool|string
     */
    public function parse($sContent, $aParams = array())
    {
        return "<code>{$sContent}</code>";
    }
}