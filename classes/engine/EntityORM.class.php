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
 * Абстрактный класс сущности ORM - аналог active record
 * Позволяет без написания SQL запросов работать с базой данных.
 * <pre>
 * $oUser=$this->User_GetUserById(1);
 * $oUser->setName('Claus');
 * $oUser->Update();
 * </pre>
 * Возможно получать списки объектов по фильтру:
 * <pre>
 * $aUsers=$this->User_GetUserItemsByAgeAndSex(18,'male');
 * // эквивалентно
 * $aUsers=$this->User_GetUserItemsByFilter(array('age'=>18,'sex'=>'male'));
 * // эквивалентно, но при использовании #where необходимо указывать префикс таблицы "t"
 * $aUsers=$this->User_GetUserItemsByFilter(array('#where'=>array('t.age = ?d and t.sex = ?' => array(18,'male'))));
 * </pre>
 *
 * @package framework.engine.orm
 * @since 1.0
 */
abstract class EntityORM extends Entity
{
    /**
     * Типы связей сущностей
     *
     */
    const RELATION_TYPE_BELONGS_TO = 'belongs_to';
    const RELATION_TYPE_HAS_MANY = 'has_many';
    const RELATION_TYPE_HAS_ONE = 'has_one';
    const RELATION_TYPE_MANY_TO_MANY = 'many_to_many';
    const RELATION_TYPE_TREE = 'tree';

    /**
     * Массив исходных данных сущности
     *
     * @var array
     */
    protected $_aOriginalData = array();
    /**
     * Список полей таблицы сущности
     *
     * @var array
     */
    protected $aFields = array();
    /**
     * Список связей
     *
     * @var array
     */
    protected $aRelations = array();
    /**
     * Список данных связей
     *
     * @var array
     */
    protected $aRelationsData = array();
    /**
     * Объекты связей many_to_many
     *
     * @var array
     */
    protected $_aManyToManyRelations = array();
    /**
     * Флаг новая или нет сущность
     *
     * @var bool
     */
    protected $bIsNew = true;

    /**
     * Установка связей
     * @see Entity::__construct
     *
     * @param bool $aParam Ассоциативный массив данных сущности
     */
    public function __construct($aParam = false)
    {
        parent::__construct($aParam);
        $this->aRelations = $this->_getRelations();
    }

    /**
     * Получение primary key из схемы таблицы
     *
     * @return string|array    Если индекс составной, то возвращает массив полей
     */
    public function _getPrimaryKey()
    {
        if (!$this->sPrimaryKey) {
            if ($aIndex = $this->ShowPrimaryIndex()) {
                if (count($aIndex) > 1) {
                    // Составной индекс
                    $this->sPrimaryKey = $aIndex;
                } else {
                    $this->sPrimaryKey = $aIndex[1];
                }
            }
        }
        return $this->sPrimaryKey;
    }

    /**
     * Получение значения primary key
     *
     * @return string
     */
    public function _getPrimaryKeyValue()
    {
        return $this->_getDataOne($this->_getPrimaryKey());
    }

    /**
     * Получение имени родительского поля. Используется в связи RELATION_TYPE_TREE
     *
     * @return string
     */
    public function _getTreeParentKey()
    {
        return 'parent_id';
    }

    /**
     * Получение значения родителя. Используется в связи RELATION_TYPE_TREE
     *
     * @return string
     */
    public function _getTreeParentKeyValue()
    {
        return $this->_getDataOne($this->_getTreeParentKey());
    }

    /**
     * Новая или нет сущность
     * Новая - еще не сохранялась в БД
     *
     * @return bool
     */
    public function _isNew()
    {
        return $this->bIsNew;
    }

    /**
     * Установка флага "новая"
     *
     * @param bool $bIsNew Флаг - новая сущность или нет
     */
    public function _SetIsNew($bIsNew)
    {
        $this->bIsNew = $bIsNew;
    }

    /**
     * Добавление сущности в БД
     *
     * @return Entity|false
     */
    public function Add()
    {
        if ($this->beforeSave())
            if ($res = $this->_Method(__FUNCTION__)) {
                $this->afterSave();
                return $res;
            }
        return false;
    }

    /**
     * Обновление сущности в БД
     *
     * @return Entity|false
     */
    public function Update()
    {
        if ($this->beforeSave())
            if ($res = $this->_Method(__FUNCTION__)) {
                $this->afterSave();
                return $res;
            }
        return false;
    }

    /**
     * Сохранение сущности в БД (если новая то создается)
     *
     * @return Entity|false
     */
    public function Save()
    {
        if ($this->beforeSave())
            if ($res = $this->_Method(__FUNCTION__)) {
                $this->afterSave();
                return $res;
            }
        return false;
    }

    /**
     * Удаление сущности из БД
     *
     * @return Entity|false
     */
    public function Delete()
    {
        if ($this->beforeDelete())
            if ($res = $this->_Method(__FUNCTION__)) {
                $this->afterDelete();
                return $res;
            }
        return false;
    }

    /**
     * Обновляет данные сущности из БД
     *
     * @return Entity|false
     */
    public function Reload()
    {
        return $this->_Method(__FUNCTION__);
    }

    /**
     * Возвращает список полей сущности
     *
     * @return array
     */
    public function ShowColumns()
    {
        return $this->_Method(__FUNCTION__ . 'From');
    }

    /**
     * Возвращает primary индекс сущности
     *
     * @return array
     */
    public function ShowPrimaryIndex()
    {
        return $this->_Method(__FUNCTION__ . 'From');
    }

    /**
     * Хук, срабатывает перед сохранением сущности
     *
     * @return bool
     */
    protected function beforeSave()
    {
        $bResult = true;
        $this->RunBehaviorHook('before_save', array('bResult' => &$bResult));
        return $bResult;
    }

    /**
     * Хук, срабатывает после сохранением сущности
     *
     */
    protected function afterSave()
    {
        $this->RunBehaviorHook('after_save');
    }

    /**
     * Хук, срабатывает перед удалением сущности
     *
     * @return bool
     */
    protected function beforeDelete()
    {
        $bResult = true;
        $this->RunBehaviorHook('before_delete', array('bResult' => &$bResult));
        return $bResult;
    }

    /**
     * Хук, срабатывает после удаления сущности
     *
     */
    protected function afterDelete()
    {
        $this->RunBehaviorHook('after_delete');
    }

    /**
     * Для сущности со связью RELATION_TYPE_TREE возвращает список прямых потомков
     *
     * @return array
     */
    public function getChildren()
    {
        if (in_array(self::RELATION_TYPE_TREE, $this->aRelations)) {
            return $this->_Method(__FUNCTION__ . 'Of');
        }
        return $this->__call(__FUNCTION__, array());
    }

    /**
     * Для сущности со связью RELATION_TYPE_TREE возвращает список всех потомков
     *
     * @return array
     */
    public function getDescendants()
    {
        if (in_array(self::RELATION_TYPE_TREE, $this->aRelations)) {
            return $this->_Method(__FUNCTION__ . 'Of');
        }
        return $this->__call(__FUNCTION__, array());
    }

    /**
     * Для сущности со связью RELATION_TYPE_TREE возвращает предка
     *
     * @return Entity
     */
    public function getParent()
    {
        if (in_array(self::RELATION_TYPE_TREE, $this->aRelations)) {
            return $this->_Method(__FUNCTION__ . 'Of');
        }
        return $this->__call(__FUNCTION__, array());
    }

    /**
     * Для сущности со связью RELATION_TYPE_TREE возвращает список всех предков
     *
     * @return array
     */
    public function getAncestors()
    {
        if (in_array(self::RELATION_TYPE_TREE, $this->aRelations)) {
            return $this->_Method(__FUNCTION__ . 'Of');
        }
        return $this->__call(__FUNCTION__, array());
    }

    /**
     * Для сущности со связью RELATION_TYPE_TREE устанавливает потомков
     *
     * @param array $aChildren Список потомков
     */
    public function setChildren($aChildren = array())
    {
        if (in_array(self::RELATION_TYPE_TREE, $this->aRelations)) {
            $this->aRelationsData['children'] = $aChildren;
        } else {
            $aArgs = func_get_args();
            return $this->__call(__FUNCTION__, $aArgs);
        }
    }

    /**
     * Для сущности со связью RELATION_TYPE_TREE устанавливает потомков
     *
     * @param array $aDescendants Список потомков
     */
    public function setDescendants($aDescendants = array())
    {
        if (in_array(self::RELATION_TYPE_TREE, $this->aRelations)) {
            $this->aRelationsData['descendants'] = $aDescendants;
        } else {
            $aArgs = func_get_args();
            return $this->__call(__FUNCTION__, $aArgs);
        }
    }

    /**
     * Для сущности со связью RELATION_TYPE_TREE устанавливает предка
     *
     * @param Entity $oParent Родитель
     */
    public function setParent($oParent = null)
    {
        if (in_array(self::RELATION_TYPE_TREE, $this->aRelations)) {
            $this->aRelationsData['parent'] = $oParent;
        } else {
            $aArgs = func_get_args();
            return $this->__call(__FUNCTION__, $aArgs);
        }
    }

    /**
     * Для сущности со связью RELATION_TYPE_TREE устанавливает предков
     *
     * @param array $oParent Родитель
     */
    public function setAncestors($oParent = null)
    {
        if (in_array(self::RELATION_TYPE_TREE, $this->aRelations)) {
            $this->aRelationsData['ancestors'] = $oParent;
        } else {
            $aArgs = func_get_args();
            return $this->__call(__FUNCTION__, $aArgs);
        }
    }

    /**
     * Проксирует вызов методов в модуль сущности
     *
     * @param string $sName Название метода
     * @return mixed
     */
    protected function _Method($sName)
    {
        $sRootDelegater = $this->Plugin_GetRootDelegater('entity', get_class($this));

        $sModuleName = Engine::GetModuleName($sRootDelegater);
        $sPluginPrefix = Engine::GetPluginPrefix($sRootDelegater);
        $sEntityName = Engine::GetEntityName($sRootDelegater);
        return Engine::GetInstance()->_CallModule("{$sPluginPrefix}{$sModuleName}_{$sName}{$sEntityName}", array($this));
    }

    /**
     * Устанавливает данные сущности
     *
     * @param array $aData Ассоциативный массив данных сущности
     */
    public function _setData($aData)
    {
        if (is_array($aData)) {
            foreach ($aData as $sKey => $val) {
                if (array_key_exists($sKey, $this->aRelations)) {
                    $this->aRelationsData[$sKey] = $val;
                } else {
                    $this->_aData[$sKey] = $val;
                }
            }
            $this->_aOriginalData = $this->_aData;
        }
    }

    /**
     * Возвращает все данные сущности
     *
     * @return array
     */
    public function _getOriginalData()
    {
        return $this->_aOriginalData;
    }

    /**
     * Возвращает "оригинальные" данные по конкретному полю
     *
     * @param string $sKey Название поля, например <pre>'my_property'</pre>
     * @return null|mixed
     */
    public function _getOriginalDataOne($sKey)
    {
        if (array_key_exists($sKey, $this->_aOriginalData)) {
            return $this->_aOriginalData[$sKey];
        }
        return null;
    }

    /**
     * Возвращает данные для списка полей сущности
     *
     * @return array
     */
    public function _getDataFields()
    {
        return $this->_getData($this->_getFields());
    }

    /**
     * Возвращает список полей сущности
     *
     * @return array
     */
    public function _getFields()
    {
        if (empty($this->aFields)) {
            $this->aFields = $this->ShowColumns();
        }
        return $this->aFields;
    }

    /**
     * Возвращает поле в нужном формате
     *
     * @param string $sField Название поля
     * @param int $iPersistence Тип "глубины" определения поля
     * @return null|string
     */
    public function _getField($sField, $iPersistence = 3)
    {
        $sRootDelegater = $this->Plugin_GetRootDelegater('entity', get_class($this));

        if ($aFields = $this->_getFields()) {
            if (in_array($sField, $aFields)) {
                return $sField;
            }
            if ($iPersistence == 0) {
                return null;
            }
            $sFieldU = func_camelize($sField);
            $sEntityField = func_underscore(Engine::GetEntityName($sRootDelegater) . $sFieldU);
            if (in_array($sEntityField, $aFields)) {
                return $sEntityField;
            }
            if ($iPersistence == 1) {
                return null;
            }
            $sModuleEntityField = func_underscore(Engine::GetModuleName($sRootDelegater) . Engine::GetEntityName($sRootDelegater) . $sFieldU);
            if (in_array($sModuleEntityField, $aFields)) {
                return $sModuleEntityField;
            }
            if ($iPersistence == 2) {
                return null;
            }
            $sModuleField = func_underscore(Engine::GetModuleName($sRootDelegater) . $sFieldU);
            if (in_array($sModuleField, $aFields)) {
                return $sModuleField;
            }
        }
        return $sField;
    }

    /**
     * Возвращает список связей
     *
     * @return array
     */
    public function _getRelations()
    {
        return $this->aRelations;
    }

    /**
     * Возвращает список данный связей
     *
     * @param string|null $sKey
     *
     * @return array|null
     */
    public function _getRelationsData($sKey = null)
    {
        if ($sKey) {
            if (array_key_exists($sKey, $this->aRelationsData)) {
                return $this->aRelationsData[$sKey];
            }
            return null;
        }
        return $this->aRelationsData;
    }

    /**
     * Устанавливает данные связей
     *
     * @param array $aData Список связанных данных
     */
    public function _setRelationsData($aData)
    {
        $this->aRelationsData = $aData;
    }

    /**
     * Устанавливает вспомогательные объекты для связи many_to_many
     *
     * @param array $aData
     * @param string|null $sRelationKey
     */
    public function _setManyToManyRelations($aData, $sRelationKey = null)
    {
        if ($sRelationKey) {
            $this->_aManyToManyRelations[$sRelationKey] = $aData;
        } else {
            $this->_aManyToManyRelations = $aData;
        }
    }

    /**
     * Возвращает сущность связи при many to many
     * Актуально только в том случае, если текущая сущность была получена через обращение к связи many to many
     *
     * @return mixed|null
     */
    public function _getManyToManyRelationEntity()
    {
        return $this->_getDataOne('_relation_entity');
    }

    /**
     * Ставим хук на вызов неизвестного метода и считаем что хотели вызвать метод какого либо модуля
     * Также производит обработку методов set* и get*
     * Учитывает связи и может возвращать связанные данные
     * @see Engine::_CallModule
     *
     * @param string $sName Имя метода
     * @param array $aArgs Аргументы
     * @return mixed
     */
    public function __call($sName, $aArgs)
    {
        $sType = substr($sName, 0, strpos(func_underscore($sName), '_'));
        if (!strpos($sName, '_') and in_array($sType, array('get', 'set', 'reload'))) {
            $sKey = func_underscore(preg_replace('/' . $sType . '/', '', $sName, 1));
            if ($sType == 'get') {
                if (isset($this->_aData[$sKey])) {
                    return $this->_aData[$sKey];
                } else {
                    $sField = $this->_getField($sKey);
                    if ($sField != $sKey && isset($this->_aData[$sField])) {
                        return $this->_aData[$sField];
                    }
                }
                /**
                 * Проверяем на связи
                 */
                if (array_key_exists($sKey, $this->aRelations)) {
                    $sEntityRel = $this->aRelations[$sKey][1];
                    $sRelationType = $this->aRelations[$sKey][0];
                    $sRelationKey = $this->aRelations[$sKey][2];

                    $sRelModuleName = Engine::GetModuleName($sEntityRel);
                    $sRelEntityName = Engine::GetEntityName($sEntityRel);
                    $sRelPluginPrefix = Engine::GetPluginPrefix($sEntityRel);
                    $sRelPrimaryKey = 'id';
                    if ($oRelEntity = Engine::GetEntity($sEntityRel)) {
                        $sRelPrimaryKey = $oRelEntity->_getPrimaryKey();
                    }

                    $iPrimaryKeyValue = $this->_getDataOne($this->_getPrimaryKey());
                    $bUseFilter = array_key_exists(0, $aArgs) && is_array($aArgs[0]);
                    $sCmd = '';
                    $mCmdArgs = array();
                    switch ($sRelationType) {
                        case self::RELATION_TYPE_BELONGS_TO :
                            $sCmd = "{$sRelPluginPrefix}{$sRelModuleName}_get{$sRelEntityName}By" . func_camelize($sRelPrimaryKey);
                            $mCmdArgs = array($this->_getDataOne($sRelationKey));
                            break;
                        case self::RELATION_TYPE_HAS_ONE :
                            $sCmd = "{$sRelPluginPrefix}{$sRelModuleName}_get{$sRelEntityName}By" . func_camelize($sRelationKey);
                            $mCmdArgs = array($iPrimaryKeyValue);
                            break;
                        case self::RELATION_TYPE_HAS_MANY :
                            if (isset($this->aRelations[$sKey][3])) {
                                $aFilterAdd = $this->aRelations[$sKey][3];
                            } else {
                                $aFilterAdd = array();
                            }
                            $sCmd = "{$sRelPluginPrefix}{$sRelModuleName}_get{$sRelEntityName}ItemsByFilter";
                            $aFilterAdd = array_merge(array($sRelationKey => $iPrimaryKeyValue), $aFilterAdd);
                            if ($bUseFilter) {
                                $aFilterAdd = array_merge($aFilterAdd, $aArgs[0]);
                            }
                            $mCmdArgs = array($aFilterAdd);
                            break;
                        case self::RELATION_TYPE_MANY_TO_MANY :
                            $sEntityJoin = $this->aRelations[$sKey][3];
                            $sKeyJoin = $this->aRelations[$sKey][4];
                            if (isset($this->aRelations[$sKey][5])) {
                                $aFilterAdd = $this->aRelations[$sKey][5];
                            } else {
                                $aFilterAdd = array();
                            }
                            $sCmd = "{$sRelPluginPrefix}Module{$sRelModuleName}_get{$sRelEntityName}ItemsByJoinEntity";
                            if ($bUseFilter) {
                                $aFilterAdd = array_merge($aFilterAdd, $aArgs[0]);
                            }
                            $mCmdArgs = array($sEntityJoin, $sKeyJoin, $sRelationKey, $iPrimaryKeyValue, $aFilterAdd);
                            break;
                        default:
                            break;
                    }
                    /**
                     * Если связь уже загруженна, то возвращаем результат
                     */
                    if (!$bUseFilter and array_key_exists($sKey, $this->aRelationsData)) {
                        return $this->aRelationsData[$sKey];
                    }
                    // Нужно ли учитывать дополнительный фильтр
                    $res = Engine::GetInstance()->_CallModule($sCmd, $mCmdArgs);

                    // Сохраняем данные только в случае "чистой" выборки
                    if (!$bUseFilter) {
                        $this->aRelationsData[$sKey] = $res;
                    }
                    // Создаём объекты-обёртки для связей MANY_TO_MANY
                    if ($sRelationType == self::RELATION_TYPE_MANY_TO_MANY) {
                        $this->_aManyToManyRelations[$sKey] = new ORMRelationManyToMany($res);
                    }
                    return $res;
                }

                return null;
            } elseif ($sType == 'set' and array_key_exists(0, $aArgs)) {
                if (array_key_exists($sKey, $this->aRelations)) {
                    $this->aRelationsData[$sKey] = $aArgs[0];
                } else {
                    $this->_aData[$this->_getField($sKey)] = $aArgs[0];
                }
                return $this;
            } elseif ($sType == 'reload') {
                if (array_key_exists($sKey, $this->aRelationsData)) {
                    unset($this->aRelationsData[$sKey]);
                    return $this->__call('get' . func_camelize($sKey), $aArgs);
                }
            }
        } else {
            return parent::__call($sName, $aArgs);
        }
    }

    /**
     * Используется для доступа к связанным данным типа MANY_TO_MANY
     *
     * @param string $sName Название свойства к которому обращаемсяя
     * @return mixed
     */
    public function __get($sName)
    {
        // Обработка обращений к обёрткам связей MANY_TO_MANY
        // Если связь загружена, возвращаем объект связи
        if (isset($this->_aManyToManyRelations[func_underscore($sName)])) {
            return $this->_aManyToManyRelations[func_underscore($sName)];
            // Есл не загружена, но связь с таким именем существет, пробуем загрузить и вернуть объект связи
        } elseif (isset($this->aRelations[func_underscore($sName)]) && $this->aRelations[func_underscore($sName)][0] == self::RELATION_TYPE_MANY_TO_MANY) {
            $sMethod = 'get' . func_camelize($sName);
            $this->__call($sMethod, array());
            if (isset($this->_aManyToManyRelations[func_underscore($sName)])) {
                return $this->_aManyToManyRelations[func_underscore($sName)];
            }
            // В противном случае возвращаем то, что просили у объекта
        } else {
            return parent::__get($sName);
        }
    }

    /**
     * Сбрасывает данные необходимой связи
     *
     * @param string $sKey Ключ(поле) связи
     */
    public function resetRelationsData($sKey)
    {
        if (isset($this->aRelationsData[$sKey])) {
            unset($this->aRelationsData[$sKey]);
        }
    }
}