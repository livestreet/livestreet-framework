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
 * @author Serge Pustovit (PSNet) <light.feel@gmail.com>
 *
 */

/**
 * Маппер хранилища настроек
 *
 * @package framework.modules
 * @since 2.0
 */
class ModuleStorage_MapperStorage extends Mapper
{

    /**
     * Получить данные из хранилища по фильтру
     *
     * @param null $sWhere фильтр
     * @param int $iPage страница
     * @param int $iPerPage результатов на страницу
     * @return array
     */
    public function GetData($sWhere = null, $iPage = 1, $iPerPage = PHP_INT_MAX)
    {
        $sSql = 'SELECT *
			FROM
				?#
			WHERE
				1 = 1
				' . $sWhere . '
			ORDER BY
				`id` ASC
			LIMIT ?d, ?d
		';
        $iTotalCount = 0;
        $aCollection = array();

        if ($aData = $this->oDb->selectPage(
            $iTotalCount,
            $sSql,

            Config::Get('db.table.storage'),

            ($iPage - 1) * $iPerPage,
            $iPerPage
        )
        ) {
            /*
             * Если нужен только один элемент
             */
            $aCollection = $iPerPage == 1 ? array_shift($aData) : $aData;
        }
        return array(
            'collection' => $aCollection,
            'count'      => $iTotalCount
        );
    }


    /**
     * Записать данные
     *
     * @param    $sKey                ключ
     * @param    $sValue                значение
     * @param    $sInstance            инстанция хранилища
     * @return array|null
     */
    public function SetData($sKey, $sValue, $sInstance)
    {
        $sSql = 'INSERT INTO
				?#
			(
				`key`,
				`value`,
				`instance`
			)
			VALUES
			(
				?,
				?,
				?
			)
			ON DUPLICATE KEY UPDATE
				`value` = ?
		';

        return $this->oDb->query(
            $sSql,

            Config::Get('db.table.storage'),

            $sKey,
            $sValue,
            $sInstance,

            $sValue
        );
    }


    /**
     * Удалить данные из хранилища
     *
     * @param null $sWhere фильтр
     * @param int $iLimit лимит запроса
     * @return array|null
     */
    public function DeleteData($sWhere = null, $iLimit = 1)
    {
        $sSql = 'DELETE
			FROM
				?#
			WHERE
				1 = 1
				' . $sWhere . '
			LIMIT ?d
		';

        return $this->oDb->query(
            $sSql,

            Config::Get('db.table.storage'),

            $iLimit
        );
    }


    /**
     * Построить строку части WHERE условия из набора параметров фильтра
     *
     * @param array $aFilter фильтр
     * @return string                часть WHERE условия sql запроса
     */
    public function BuildFilter($aFilter = array())
    {
        $sWhere = '';
        /*
         * для всех значение добавить условие "ключ = значение"
         */
        foreach ($aFilter as $sKey => $mValue) {
            $sWhere .= '
				AND ' . $this->oDb->escape($sKey, true) . ' = ' . $this->oDb->escape($mValue);
        }
        return $sWhere;
    }

}