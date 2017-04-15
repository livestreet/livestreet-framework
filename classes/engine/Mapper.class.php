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
 * Абстрактный класс мапера
 * Вся задача маппера сводится в выполнению запроса к базе данных (или либому другому источнику данных) и возвращению результата в модуль.
 *
 * @package framework.engine
 * @since 1.0
 */
abstract class Mapper extends LsObject
{
    /**
     * Объект подключения к базе данных
     *
     * @var DbSimple_Database
     */
    protected $oDb;

    /**
     * Передаем коннект к БД
     *
     * @param DbSimple_Database $oDb
     */
    public function __construct($oDb)
    {
        parent::__construct();
        $this->oDb = $oDb;
    }

    protected function IsSuccessful($mRes)
    {
        return $mRes === false or is_null($mRes) ? false : true;
    }
}