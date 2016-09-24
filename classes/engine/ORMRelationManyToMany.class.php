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
 * Класс представляющий собой обертку для связей MANY_TO_MANY.
 * Позволяет оперировать коллекцией загруженных по связи элементов через имя связи
 * Например
 * <pre>
 * $oTopic->Tags->add($oTag)
 * // или
 * $oTopic->Tags->delete($oTag->getId())
 * </pre> при
 * наличии настроенной MANY_TO_MANY связи 'tags'
 *
 * @package framework.engine.orm
 * @since 2.0
 */
class ORMRelationManyToMany extends LsObject
{
    /**
     * Список объектов связи
     *
     * @var array
     */
    protected $aCollection = array();
    /**
     * Общее количество объектов связи
     *
     * @var integer
     */
    protected $iCount = 0;
    /**
     * Флаг обновления списка объектов связи
     *
     * @var bool
     */
    protected $bUpdated = false;

    /**
     * Устанавливает список объектов
     *
     * @param $aCollection    Список объектов связи
     */
    public function __construct($aCollection)
    {
        parent::__construct();
        if (!$aCollection) {
            $aCollection = array();
        }
        if (!is_array($aCollection)) {
            $aCollection = array($aCollection);
        }
        if (!isset($aCollection['collection'])) {
            $this->iCount = count($aCollection);
        } else {
            $this->iCount = $aCollection['count'];
            $aCollection = $aCollection['collection'];
        }
        foreach ($aCollection as $oEntity) {
            $this->aCollection[$oEntity->_getPrimaryKeyValue()] = $oEntity;
        }
    }

    /**
     * Добавление объекта в список
     *
     * @param Entity $oEntity
     */
    public function add($oEntity)
    {
        $this->bUpdated = true;
        $this->aCollection[$oEntity->_getPrimaryKeyValue()] = $oEntity;
    }

    /**
     * Удаление объекта из списка по его id или массиву id
     *
     * @param int|array $iId
     */
    public function delete($iId)
    {
        $this->bUpdated = true;
        if (is_array($iId)) {
            foreach ($iId as $id) {
                if (is_object($id)) {
                    $id = $id->_getPrimaryKeyValue();
                }
                if (isset($this->aCollection[$id])) {
                    unset($this->aCollection[$id]);
                }
            }
        } else {
            if (is_object($iId)) {
                $iId = $iId->_getPrimaryKeyValue();
            }
            if (isset($this->aCollection[$iId])) {
                unset($this->aCollection[$iId]);
            }
        }
    }

    /**
     * Удаляет все объекты
     */
    public function clear()
    {
        $this->bUpdated = true;
        $this->aCollection = array();
    }

    /**
     * Возвращает список объектов связи
     *
     * @return array
     */
    public function getCollection()
    {
        return $this->aCollection;
    }

    /**
     * Возвращает список объектов связи
     *
     * @return array
     */
    public function getCount()
    {
        return $this->iCount;
    }

    /**
     * Проверка списка на обновление
     *
     * @return bool
     */
    public function isUpdated()
    {
        return $this->bUpdated;
    }
}