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
 * Абстракция модуля ORM
 * Предоставляет базовые методы для работы с EntityORM, например,
 * <pre>
 *	$aUsers=$this->User_GetUserItemsByAgeAndSex(18,'male');
 * </pre>
 *
 * @package framework.engine.orm
 * @since 1.0
 */
abstract class ModuleORM extends Module {
	/**
	 * Объект маппера ORM
	 *
	 * @var MapperORM
	 */
	protected $oMapperORM=null;

	/**
	 * Инициализация
	 * В наследнике этот метод нельзя перекрывать, необходимо вызывать через parent::Init();
	 *
	 */
	public function Init() {
		$this->_LoadMapperORM();
	}
	/**
	 * Загрузка маппера ORM
	 *
	 */
	protected function _LoadMapperORM() {
		$this->oMapperORM=new MapperORM($this->Database_GetConnect());
	}
	/**
	 * Добавление сущности в БД
	 * Вызывается не напрямую, а через сущность, например
	 * <pre>
	 *  $oUser->setName('Claus');
	 * 	$oUser->Add();
	 * </pre>
	 *
	 * @param EntityORM $oEntity	Объект сущности
	 * @return EntityORM|bool
	 */
	protected function _AddEntity($oEntity) {
		$res=$this->oMapperORM->AddEntity($oEntity);
		// сбрасываем кеш
		if ($res===0 or $res) {
			$sEntity=$this->Plugin_GetRootDelegater('entity',get_class($oEntity));
			$this->Cache_Clean(Zend_Cache::CLEANING_MODE_MATCHING_TAG,array($sEntity.'_save'));
		}
		if ($res===0) {
			// у таблицы нет автоинремента
			return $oEntity;
		} elseif ($res) {
			// есть автоинкремент, устанавливаем его
			$oEntity->_setData(array($oEntity->_getPrimaryKey() => $res));
			/**
			 * Смотрим наличие связи many_to_many и добавляем их в бд
			 */
			foreach ($oEntity->_getRelations() as $sRelName => $aRelation) {
				if ($aRelation[0] == EntityORM::RELATION_TYPE_MANY_TO_MANY && $oEntity->$sRelName->isUpdated()) {
					$this->_updateManyToManyRelation($oEntity,$sRelName);
					$oEntity->resetRelationsData($sRelName);
				}
			}
			return $oEntity;
		}
		return false;
	}
	/**
	 * Обновление сущности в БД
	 *
	 * @param EntityORM $oEntity	Объект сущности
	 * @return EntityORM|bool
	 */
	protected function _UpdateEntity($oEntity) {
		$res=$this->oMapperORM->UpdateEntity($oEntity);
		if ($res===0 or $res) { // запись не изменилась, либо изменилась
			// Обновление связей many_to_many
			foreach ($oEntity->_getRelations() as $sRelName => $aRelation) {
				if ($aRelation[0] == EntityORM::RELATION_TYPE_MANY_TO_MANY && $oEntity->$sRelName->isUpdated()) {
					$this->_updateManyToManyRelation($oEntity,$sRelName);
					$oEntity->resetRelationsData($sRelName);
				}
			}
			// сбрасываем кеш
			$sEntity=$this->Plugin_GetRootDelegater('entity',get_class($oEntity));
			$this->Cache_Clean(Zend_Cache::CLEANING_MODE_MATCHING_TAG,array($sEntity.'_save'));
			return $oEntity;
		}
		return false;
	}
	/**
	 * Сохранение сущности в БД
	 *
	 * @param EntityORM $oEntity	Объект сущности
	 * @return EntityORM|bool
	 */
	protected function _SaveEntity($oEntity) {
		if ($oEntity->_isNew()) {
			return $this->_AddEntity($oEntity);
		} else {
			return $this->_UpdateEntity($oEntity);
		}
	}
	/**
	 * Удаление сущности из БД
	 *
	 * @param EntityORM $oEntity	Объект сущности
	 * @return EntityORM|bool
	 */
	protected function _DeleteEntity($oEntity) {
		$res=$this->oMapperORM->DeleteEntity($oEntity);
		if ($res) {
			// сбрасываем кеш
			$sEntity=$this->Plugin_GetRootDelegater('entity',get_class($oEntity));
			$this->Cache_Clean(Zend_Cache::CLEANING_MODE_MATCHING_TAG,array($sEntity.'_delete'));

			// Удаление связей many_to_many
			foreach ($oEntity->_getRelations() as $sRelName => $aRelation) {
				if ($aRelation[0] == EntityORM::RELATION_TYPE_MANY_TO_MANY) {
					$this->_deleteManyToManyRelation($oEntity,$sRelName);
				}
			}

			return $oEntity;
		}
		return false;
	}
	/**
	 * Обновляет данные сущности из БД
	 *
	 * @param EntityORM $oEntity	Объект сущности
	 * @return EntityORM|bool
	 */
	protected function _ReloadEntity($oEntity) {
		if($sPrimaryKey=$oEntity->_getPrimaryKey()) {
			if($sPrimaryKeyValue=$oEntity->_getDataOne($sPrimaryKey)) {
				if($oEntityNew=$this->GetByFilter(array($sPrimaryKey=>$sPrimaryKeyValue),Engine::GetEntityName($oEntity))) {
					$oEntity->_setData($oEntityNew->_getData());
					$oEntity->_setRelationsData(array());
					return $oEntity;
				}
			}
		}
		return false;
	}
	/**
	 * Список полей сущности
	 *
	 * @param EntityORM $oEntity	Объект сущности
	 * @return array
	 */
	protected function _ShowColumnsFrom($oEntity) {
		return $this->oMapperORM->ShowColumnsFrom($oEntity);
	}
	/**
	 * Primary индекс сущности
	 *
	 * @param EntityORM $oEntity	Объект сущности
	 * @return array
	 */
	protected function _ShowPrimaryIndexFrom($oEntity) {
		return $this->oMapperORM->ShowPrimaryIndexFrom($oEntity);
	}
	/**
	 * Для сущности со связью RELATION_TYPE_TREE возвращает список прямых потомков
	 *
	 * @param EntityORM $oEntity	Объект сущности
	 * @return array
	 */
	protected function _GetChildrenOfEntity($oEntity) {
		if(in_array(EntityORM::RELATION_TYPE_TREE,$oEntity->_getRelations())) {
			$aRelationsData=$oEntity->_getRelationsData();
			if(array_key_exists('children',$aRelationsData)) {
				$aChildren=$aRelationsData['children'];
			} else {
				$aChildren=array();
				if($sPrimaryKey=$oEntity->_getPrimaryKey()) {
					if($sPrimaryKeyValue=$oEntity->_getDataOne($sPrimaryKey)) {
						$aChildren=$this->GetItemsByFilter(array($oEntity->_getTreeParentKey()=>$sPrimaryKeyValue),Engine::GetEntityName($oEntity));
					}
				}
			}
			if(is_array($aChildren)) {
				$oEntity->setChildren($aChildren);
				return $aChildren;
			}
		}
		return false;
	}
	/**
	 * Для сущности со связью RELATION_TYPE_TREE возвращает предка
	 *
	 * @param EntityORM $oEntity	Объект сущности
	 * @return EntityORM|bool
	 */
	protected function _GetParentOfEntity($oEntity) {
		if(in_array(EntityORM::RELATION_TYPE_TREE,$oEntity->_getRelations())) {
			$aRelationsData=$oEntity->_getRelationsData();
			if(array_key_exists('parent',$aRelationsData)) {
				$oParent=$aRelationsData['parent'];
			} else {
				$oParent=null;
				if($sPrimaryKey=$oEntity->_getPrimaryKey()) {
					if($sParentId=$oEntity->_getTreeParentKeyValue()) {
						$oParent=$this->GetByFilter(array($sPrimaryKey=>$sParentId),Engine::GetEntityName($oEntity));
					}
				}
			}
			$oEntity->setParent($oParent);
			return $oParent;
		}
		return false;
	}
	/**
	 * Для сущности со связью RELATION_TYPE_TREE возвращает список всех предков
	 *
	 * @param EntityORM $oEntity	Объект сущности
	 * @return array
	 */
	protected function _GetAncestorsOfEntity($oEntity) {
		if(in_array(EntityORM::RELATION_TYPE_TREE,$oEntity->_getRelations())) {
			$aRelationsData=$oEntity->_getRelationsData();
			if(array_key_exists('ancestors',$aRelationsData)) {
				$aAncestors=$aRelationsData['ancestors'];
			} else {
				$aAncestors=array();
				$oEntityParent=$oEntity->getParent();
				while(is_object($oEntityParent)) {
					$aAncestors[]=$oEntityParent;
					$oEntityParent=$oEntityParent->getParent();
				}
			}
			if(is_array($aAncestors)) {
				$oEntity->setAncestors($aAncestors);
				return $aAncestors;
			}
		}
		return false;
	}
	/**
	 * Для сущности со связью RELATION_TYPE_TREE возвращает список всех потомков
	 *
	 * @param EntityORM $oEntity	Объект сущности
	 * @return array
	 */
	protected function _GetDescendantsOfEntity($oEntity) {
		if(in_array(EntityORM::RELATION_TYPE_TREE,$oEntity->_getRelations())) {
			$aRelationsData=$oEntity->_getRelationsData();
			if(array_key_exists('descendants',$aRelationsData)) {
				$aDescendants=$aRelationsData['descendants'];
			} else {
				$aDescendants=array();
				if($aChildren=$oEntity->getChildren()) {
					$aTree=self::buildTree($aChildren);
					foreach($aTree as $aItem) {
						$aDescendants[] = $aItem['entity'];
					}
				}
			}
			if(is_array($aDescendants)) {
				$oEntity->setDescendants($aDescendants);
				return $aDescendants;
			}
		}
		return false;
	}
	/**
	 * Для сущностей со связью RELATION_TYPE_TREE возвращает список сущностей в виде дерева
	 *
	 * @param array $aFilter	Фильтр
	 * @param string $sEntityFull	Название класса сущности
	 * @return array|bool
	 */
	public function LoadTree($aFilter=array(),$sEntityFull=null) {
		if (is_null($sEntityFull)) {
			$sEntityFull=Engine::GetPluginPrefix($this).'Module'.Engine::GetModuleName($this).'_Entity'.Engine::GetModuleName(get_class($this));
		} elseif (!substr_count($sEntityFull,'_')) {
			$sEntityFull=Engine::GetPluginPrefix($this).'Module'.Engine::GetModuleName($this).'_Entity'.$sEntityFull;
		}
		if($oEntityDefault=Engine::GetEntity($sEntityFull)) {
			if(in_array(EntityORM::RELATION_TYPE_TREE,$oEntityDefault->_getRelations())) {
				if($sPrimaryKey=$oEntityDefault->_getPrimaryKey()) {
					if($aItems=$this->GetItemsByFilter($aFilter,$sEntityFull)) {
						$aItemsById = array();
						$aItemsByParentId = array();
						foreach($aItems as $oEntity) {
							$oEntity->setChildren(array());
							$aItemsById[$oEntity->_getDataOne($sPrimaryKey)] = $oEntity;
							$sParentKeyValue=$oEntity->_getTreeParentKeyValue() ? $oEntity->_getTreeParentKeyValue() : 'root';
							if(empty($aItemsByParentId[$sParentKeyValue])) {
								$aItemsByParentId[$sParentKeyValue] = array();
							}
							$aItemsByParentId[$sParentKeyValue][] = $oEntity;
						}
						foreach($aItemsByParentId as $iParentId=>$aItems) {
							if($iParentId!='root') {
								if (isset($aItemsById[$iParentId])) {
									$aItemsById[$iParentId]->setChildren($aItems);
									foreach($aItems as $oEntity) {
										$oEntity->setParent($aItemsById[$iParentId]);
									}
								}
							} else {
								foreach($aItems as $oEntity) {
									$oEntity->setParent(null);
								}
							}
						}
						return isset($aItemsByParentId['root']) ? $aItemsByParentId['root'] : array();
					}
				}
			}
		}
		return false;
	}
	/**
	 * Удаляет сущности по фильтру
	 * Удаление происходит отдельно для каждой сущности через вызов метода Delete()
	 *
	 * @param array $aFilter
	 * @param null  $sEntityFull
	 */
	public function DeleteItemsByFilter($aFilter=array(),$sEntityFull=null) {
		$aItems=$this->GetItemsByFilter($aFilter,$sEntityFull);
		foreach($aItems as $oItem) {
			$oItem->Delete();
		}
	}
	/**
	 * Получить сущность по фильтру
	 *
	 * @param array $aFilter	Фильтр
	 * @param string $sEntityFull	Название класса сущности
	 * @return EntityORM|null
	 */
	public function GetByFilter($aFilter=array(),$sEntityFull=null) {
		$sEntityFull=$this->_NormalizeEntityRootName($sEntityFull);
		/**
		 * Хук для возможности изменения фильтра
		 */
		$this->RunBehaviorHook('module_orm_GetByFilter_before', array('aFilter'=>&$aFilter,'sEntityFull'=>$sEntityFull),true);
		$aEntities=$this->oMapperORM->GetByFilter($aFilter,$sEntityFull);
		/**
		 * Хук для возможности кастомной обработки результата
		 */
		$this->RunBehaviorHook('module_orm_GetByFilter_after', array('aEntities'=>$aEntities,'aFilter'=>$aFilter,'sEntityFull'=>$sEntityFull),true);
		return $aEntities;
	}
	/**
	 * Получить список сущностей по фильтру
	 *
	 * @param array $aFilter	Фильтр
	 * @param string|null $sEntityFull	Название класса сущности
	 * @return array
	 */
	public function GetItemsByFilter($aFilter=array(),$sEntityFull=null) {
		if (is_null($aFilter)) {
			$aFilter = array();
		}

		$sEntityFull=$this->_NormalizeEntityRootName($sEntityFull);
		/**
		 * Хук для возможности изменения фильтра
		 */
		$this->RunBehaviorHook('module_orm_GetItemsByFilter_before', array('aFilter'=>&$aFilter,'sEntityFull'=>$sEntityFull),true);

		// Если параметр #cache указан и пуст, значит игнорируем кэширование для запроса
		if (array_key_exists('#cache', $aFilter) && !$aFilter['#cache']) {
			$aEntities=$this->oMapperORM->GetItemsByFilter($aFilter,$sEntityFull);
		} else {
			$sCacheKey=$sEntityFull.'_items_by_filter_'.serialize($aFilter);
			$aCacheTags=array($sEntityFull.'_save',$sEntityFull.'_delete');
			$iCacheTime=60*60*24; // скорее лучше хранить в свойстве сущности, для возможности выборочного переопределения
			// переопределяем из параметров
			if (isset($aFilter['#cache'][0])) $sCacheKey=$aFilter['#cache'][0];
			if (isset($aFilter['#cache'][1])) $aCacheTags=$aFilter['#cache'][1];
			if (isset($aFilter['#cache'][2])) $iCacheTime=$aFilter['#cache'][2];

			if (false === ($aEntities = $this->Cache_Get($sCacheKey))) {
				$aEntities=$this->oMapperORM->GetItemsByFilter($aFilter,$sEntityFull);
				$this->Cache_Set($aEntities,$sCacheKey, $aCacheTags, $iCacheTime);
			}
		}
		/**
		 * Если необходимо подцепить связанные данные
		 */
		if (count($aEntities) and isset($aFilter['#with'])) {
			if (!is_array($aFilter['#with'])) {
				$aFilter['#with']=array($aFilter['#with']);
			}
			/**
			 * Приводим значение к единой форме ассоциативного массива: array('user'=>array(), 'topic'=>array('blog_id'=>123) )
			 */
			func_array_simpleflip($aFilter['#with'],array());
			/**
			 * Формируем список примари ключей
			 */
			$aEntityPrimaryKeys=array();
			foreach ($aEntities as $oEntity) {
				$aEntityPrimaryKeys[]=$oEntity->_getPrimaryKeyValue();
			}
			$oEntityEmpty=Engine::GetEntity($sEntityFull);
			$aRelations=$oEntityEmpty->_getRelations();
			$aEntityKeys=array();
			foreach ($aFilter['#with'] as $sRelationName => $aRelationFilter) {
				if (!isset($aRelations[$sRelationName])) {
					continue;
				}
				/**
				 * Если необходимо, то выставляем сразу нужное значение и не делаем никаких запросов
				 */
				if (isset($aRelationFilter['#value-set'])) {
					foreach ($aEntities as $oEntity) {
						$oEntity->_setData(array($sRelationName => $aRelationFilter['#value-set']));
					}
					continue;
				}

				$sRelType=$aRelations[$sRelationName][0];
				$sRelEntity=$this->Plugin_GetRootDelegater('entity',$aRelations[$sRelationName][1]); // получаем корневую сущность, без учета наследников
				$sRelKey=$aRelations[$sRelationName][2];

				if (!array_key_exists($sRelationName,$aRelations) or !in_array($sRelType,array(EntityORM::RELATION_TYPE_BELONGS_TO,EntityORM::RELATION_TYPE_HAS_ONE,EntityORM::RELATION_TYPE_HAS_MANY,EntityORM::RELATION_TYPE_MANY_TO_MANY))) {
					throw new Exception("The entity <{$sEntityFull}> not have relation <{$sRelationName}>");
				}

				/**
				 * Формируем список ключей
				 */
				foreach ($aEntities as $oEntity) {
					$aEntityKeys[$sRelKey][]=$oEntity->_getDataOne($sRelKey);
				}
				$aEntityKeys[$sRelKey]=array_unique($aEntityKeys[$sRelKey]);
				/**
				 * Делаем общий запрос по всем ключам
				 */
				$oRelEntityEmpty=Engine::GetEntity($sRelEntity);
				$sRelModuleName=Engine::GetModuleName($sRelEntity);
				$sRelEntityName=Engine::GetEntityName($sRelEntity);
				$sRelPluginPrefix=Engine::GetPluginPrefix($sRelEntity);
				$sRelPrimaryKey = method_exists($oRelEntityEmpty,'_getPrimaryKey') ? func_camelize($oRelEntityEmpty->_getPrimaryKey()) : 'Id';
				if ($sRelType==EntityORM::RELATION_TYPE_BELONGS_TO) {
					$aRelData=Engine::GetInstance()->_CallModule("{$sRelPluginPrefix}{$sRelModuleName}_get{$sRelEntityName}ItemsByArray{$sRelPrimaryKey}", array($aEntityKeys[$sRelKey]));
				} elseif ($sRelType==EntityORM::RELATION_TYPE_HAS_ONE) {
					$aFilterRel=array($sRelKey.' in'=>$aEntityPrimaryKeys,'#index-from'=>$sRelKey);
					$aFilterRel=array_merge($aFilterRel,$aRelationFilter);
					$aRelData=Engine::GetInstance()->_CallModule("{$sRelPluginPrefix}{$sRelModuleName}_get{$sRelEntityName}ItemsByFilter", array($aFilterRel));
				} elseif ($sRelType==EntityORM::RELATION_TYPE_HAS_MANY) {
					$aFilterRel=array($sRelKey.' in'=>$aEntityPrimaryKeys,'#index-group'=>$sRelKey);
					$aFilterRel=array_merge($aFilterRel,$aRelationFilter);
					$aRelData=Engine::GetInstance()->_CallModule("{$sRelPluginPrefix}{$sRelModuleName}_get{$sRelEntityName}ItemsByFilter", array($aFilterRel));
				} elseif ($sRelType==EntityORM::RELATION_TYPE_MANY_TO_MANY) {
					$sEntityJoin=$aRelations[$sRelationName][3];
					$sKeyJoin=$aRelations[$sRelationName][4];
					if (isset($aRelations[$sRelationName][5])) {
						$aFilterAdd=$aRelations[$sRelationName][5];
					} else {
						$aFilterAdd=array();
					}
					if (!array_key_exists('#value-default',$aRelationFilter)) {
						$aRelationFilter['#value-default']=array();
					}
					$aFilterRel=array_merge($aFilterAdd,$aRelationFilter);
					$aRelData=Engine::GetInstance()->_CallModule("{$sRelPluginPrefix}{$sRelModuleName}_get{$sRelEntityName}ItemsByJoinEntity", array($sEntityJoin,$sKeyJoin,$sRelKey,$aEntityPrimaryKeys,$aFilterRel));
					$aRelData=$this->_setIndexesGroupJoinField($aRelData,$sKeyJoin);
				}
				/**
				 * Собираем набор
				 */
				foreach ($aEntities as $oEntity) {
					if ($sRelType==EntityORM::RELATION_TYPE_BELONGS_TO) {
						$sKeyData=$oEntity->_getDataOne($sRelKey);
					} elseif (in_array($sRelType,array(EntityORM::RELATION_TYPE_HAS_ONE,EntityORM::RELATION_TYPE_HAS_MANY,EntityORM::RELATION_TYPE_MANY_TO_MANY))) {
						$sKeyData=$oEntity->_getPrimaryKeyValue();
					} else {
						break;
					}
					if (isset($aRelData[$sKeyData])) {
						$oEntity->_setData(array($sRelationName => $aRelData[$sKeyData]));
					} elseif (isset($aRelationFilter['#value-default'])) {
						$oEntity->_setData(array($sRelationName => $aRelationFilter['#value-default']));
					}
					if ($sRelType==EntityORM::RELATION_TYPE_MANY_TO_MANY) {
						// Создаём объекты-обёртки для связей MANY_TO_MANY
						$oEntity->_setManyToManyRelations(new ORMRelationManyToMany($oEntity->_getRelationsData($sRelationName)),$sRelationName);
					}
				}
			}

		}
		/**
		 * Returns assotiative array, indexed by PRIMARY KEY or another field.
		 */
		if (in_array('#index-from-primary', $aFilter) || !empty($aFilter['#index-from'])) {
			$aEntities = $this->_setIndexesFromField($aEntities, $aFilter);
		}
		/**
		 * Группирует результирующий массив по ключам необходимого поля
		 */
		if (!empty($aFilter['#index-group'])) {
			$aEntities = $this->_setIndexesGroupField($aEntities, $aFilter);
		}
		/**
		 * Хук для возможности кастомной обработки результата
		 */
		$this->RunBehaviorHook('module_orm_GetItemsByFilter_after', array('aEntities'=>$aEntities,'aFilter'=>$aFilter,'sEntityFull'=>$sEntityFull),true);
		/**
		 * Если запрашиваем постраничный список, то возвращаем сам список и общее количество записей
		 */
		if (isset($aFilter['#page'])) {
			if (isset($aFilter['#cache'][0])) {
				/**
				 * Задан собственный ключ для хранения кеша, поэтому нужно его сменить для передачи в GetCount*
				 * Добавляем префикс 'count_'
				 */
				$aFilter['#cache'][0]='count_'.$aFilter['#cache'][0];
			}
			return array('collection'=>$aEntities,'count'=>$this->GetCountItemsByFilter($aFilter,$sEntityFull));
		}
		return $aEntities;
	}
	/**
	 * Returns assotiative array, indexed by PRIMARY KEY or another field.
	 *
	 * @param array $aEntities	Список сущностей
	 * @param array $aFilter	Фильтр
	 * @return array
	 */
	protected function _setIndexesFromField($aEntities, $aFilter) {
		$aIndexedEntities=array();
		foreach ($aEntities as $oEntity) {
			$sKey = in_array('#index-from-primary', $aFilter) || ( !empty($aFilter['#index-from']) && $aFilter['#index-from'] == '#primary' ) ?
				$oEntity->_getPrimaryKey() :
				$oEntity->_getField($aFilter['#index-from']);
			$aIndexedEntities[$oEntity->_getDataOne($sKey)]=$oEntity;
		}
		return $aIndexedEntities;
	}
	/**
	 * Возвращает сгруппированный массив по нужному полю
	 *
	 * @param array $aEntities
	 * @param array $aFilter
	 *
	 * @return array
	 */
	protected function _setIndexesGroupField($aEntities, $aFilter) {
		$aIndexedEntities=array();
		foreach ($aEntities as $oEntity) {
			$sKey = $oEntity->_getField($aFilter['#index-group']);
			$aIndexedEntities[$oEntity->_getDataOne($sKey)][]=$oEntity;
		}
		return $aIndexedEntities;
	}
	/**
	 * Возвращает сгруппированный массив по нужному полю из данных таблицы связей
	 *
	 * @param array $aEntities
	 * @param string $sField
	 *
	 * @return array
	 */
	protected function _setIndexesGroupJoinField($aEntities, $sField) {
		$aIndexedEntities=array();
		foreach ($aEntities as $oEntity) {
			$oRelEntity=$oEntity->_getDataOne('_relation_entity');
			if ($oRelEntity) {
				$sVal=$oRelEntity->_getDataOne($oRelEntity->_getField($sField));
				if (!is_null($sVal)) {
					$aIndexedEntities[$sVal][]=$oEntity;
				}
			}
		}
		return $aIndexedEntities;
	}
	/**
	 * Получить значение агрегирующей функции
	 *
	 * @param       $sAggregateFunction
	 * @param       $sField
	 * @param array $aFilter
	 * @param null  $sEntityFull
	 *
	 * @return EntityORM|null
	 */
	public function GetAggregateFunctionByFilter($sAggregateFunction,$sField,$aFilter=array(),$sEntityFull=null) {
		$sEntityFull=$this->_NormalizeEntityRootName($sEntityFull);
		// Если параметр #cache указан и пуст, значит игнорируем кэширование для запроса
		if (array_key_exists('#cache', $aFilter) && !$aFilter['#cache']) {
			$iValue=$this->oMapperORM->GetAggregateFunctionByFilter($sAggregateFunction,$sField,$aFilter,$sEntityFull);
		} else {
			$sCacheKey=$sEntityFull."_aggregate_function_by_filter_{$sAggregateFunction}_{$sField}".serialize($aFilter);
			$aCacheTags=array($sEntityFull.'_save',$sEntityFull.'_delete');
			$iCacheTime=60*60*24; // скорее лучше хранить в свойстве сущности, для возможности выборочного переопределения
			// переопределяем из параметров
			if (isset($aFilter['#cache'][0])) $sCacheKey=$aFilter['#cache'][0];
			if (isset($aFilter['#cache'][1])) $aCacheTags=$aFilter['#cache'][1];
			if (isset($aFilter['#cache'][2])) $iCacheTime=$aFilter['#cache'][2];

			if (false === ($iValue = $this->Cache_Get($sCacheKey))) {
				$iValue=$this->oMapperORM->GetAggregateFunctionByFilter($sAggregateFunction,$sField,$aFilter,$sEntityFull);
				$this->Cache_Set($iValue,$sCacheKey, $aCacheTags, $iCacheTime);
			}
		}
		return $iValue;
	}
	/**
	 * Получить количество сущностей по фильтру
	 *
	 * @param array $aFilter	Фильтр
	 * @param string $sEntityFull	Название класса сущности
	 * @return int
	 */
	public function GetCountItemsByFilter($aFilter=array(),$sEntityFull=null) {
		$sEntityFull=$this->_NormalizeEntityRootName($sEntityFull);
		// Если параметр #cache указан и пуст, значит игнорируем кэширование для запроса
		if (array_key_exists('#cache', $aFilter) && !$aFilter['#cache']) {
			$iCount=$this->oMapperORM->GetCountItemsByFilter($aFilter,$sEntityFull);
		} else {
			$sCacheKey=$sEntityFull.'_count_items_by_filter_'.serialize($aFilter);
			$aCacheTags=array($sEntityFull.'_save',$sEntityFull.'_delete');
			$iCacheTime=60*60*24; // скорее лучше хранить в свойстве сущности, для возможности выборочного переопределения
			// переопределяем из параметров
			if (isset($aFilter['#cache'][0])) $sCacheKey=$aFilter['#cache'][0];
			if (isset($aFilter['#cache'][1])) $aCacheTags=$aFilter['#cache'][1];
			if (isset($aFilter['#cache'][2])) $iCacheTime=$aFilter['#cache'][2];

			if (false === ($iCount = $this->Cache_Get($sCacheKey))) {
				$iCount=$this->oMapperORM->GetCountItemsByFilter($aFilter,$sEntityFull);
				$this->Cache_Set($iCount,$sCacheKey, $aCacheTags, $iCacheTime);
			}
		}
		return $iCount;
	}
	/**
	 * Возвращает список сущностей по фильтру
	 * В качестве ключей возвращаемого массива используется primary key сущности
	 *
	 * @param array $aFilter	Фильтр
	 * @param string|null $sEntityFull	Название класса сущности
	 * @return array
	 */
	public function GetItemsByArray($aFilter,$sEntityFull=null) {
		foreach ($aFilter as $k=>$v) {
			$aFilter["{$k} IN"]=$v;
			unset($aFilter[$k]);
		}
		$aFilter[] = '#index-from-primary';
		return $this->GetItemsByFilter($aFilter,$sEntityFull);
	}

	public function GetItemsByJoinEntity($sEntityJoin,$sKeyJoin,$sRelationKey,$aRelationValues,$aFilter,$sEntityFull=null) {
		$sEntityFull=$this->_NormalizeEntityRootName($sEntityFull);
		/**
		 * Кеширование
		 * Если параметр #cache указан и пуст, значит игнорируем кэширование для запроса
		 */
		if (array_key_exists('#cache', $aFilter) && !$aFilter['#cache']) {
			$aEntities = $this->oMapperORM->GetItemsByJoinEntity($sEntityJoin,$sKeyJoin,$sRelationKey,$aRelationValues,$aFilter,$sEntityFull);
		} else {
			$sEntityJoin=$this->Plugin_GetRootDelegater('entity',$sEntityJoin);

			$sCacheKey='items_by_join_entity_'.serialize(array($sEntityJoin,$sKeyJoin,$sRelationKey,$aRelationValues,$aFilter,$sEntityFull));
			/**
			 * Формируем теги для сброса кеша
			 * Сброс идет по обновлению запрашиваемой сущности
			 * Дополнительно по обновлению таблицы связей
			 */
			$aCacheTags=array($sEntityFull.'_save',$sEntityFull.'_delete',$sEntityJoin.'_save',$sEntityJoin.'_delete');
			$iCacheTime=60*60*24; // todo: скорее лучше хранить в свойстве сущности, для возможности выборочного переопределения
			/**
			 * Переопределяем из параметров
			 */
			if (isset($aFilter['#cache'][0])) $sCacheKey=$aFilter['#cache'][0];
			if (isset($aFilter['#cache'][1])) $aCacheTags=$aFilter['#cache'][1];
			if (isset($aFilter['#cache'][2])) $iCacheTime=$aFilter['#cache'][2];
			/**
			 * Смотрим в кеше
			 */
			if (false === ($aEntities = $this->Cache_Get($sCacheKey))) {
				$aEntities = $this->oMapperORM->GetItemsByJoinEntity($sEntityJoin,$sKeyJoin,$sRelationKey,$aRelationValues,$aFilter,$sEntityFull);
				$this->Cache_Set($aEntities,$sCacheKey, $aCacheTags, $iCacheTime);
			}
		}
		/**
		 * Если запрашиваем постраничный список, то возвращаем сам список и общее количество записей
		 */
		if (isset($aFilter['#page'])) {
			if (isset($aFilter['#cache'][0])) {
				/**
				 * Задан собственный ключ для хранения кеша, поэтому нужно его сменить для передачи в GetCount*
				 * Добавляем префикс 'count_'
				 */
				$aFilter['#cache'][0]='count_'.$aFilter['#cache'][0];
			}
			return array('collection'=>$aEntities,'count'=>$this->GetCountItemsByJoinEntity($sEntityJoin,$sKeyJoin,$sRelationKey,$aRelationValues,$aFilter,$sEntityFull=null));
		}
		return $aEntities;
	}

	public function GetCountItemsByJoinEntity($sEntityJoin,$sKeyJoin,$sRelationKey,$aRelationValues,$aFilter,$sEntityFull=null) {
		$sEntityFull=$this->_NormalizeEntityRootName($sEntityFull);
		/**
		 * Кеширование
		 * Если параметр #cache указан и пуст, значит игнорируем кэширование для запроса
		 */
		if (array_key_exists('#cache', $aFilter) && !$aFilter['#cache']) {
			$iCount=$this->oMapperORM->GetCountItemsByJoinEntity($sEntityJoin,$sKeyJoin,$sRelationKey,$aRelationValues,$aFilter,$sEntityFull);
		} else {
			$sEntityJoin=$this->Plugin_GetRootDelegater('entity',$sEntityJoin);

			$sCacheKey='count_items_by_join_entity_'.serialize(array($sEntityJoin,$sKeyJoin,$sRelationKey,$aRelationValues,$aFilter,$sEntityFull));
			/**
			 * Формируем теги для сброса кеша
			 * Сброс идет по обновлению таблицы связей
			 */
			$aCacheTags=array($sEntityJoin.'_save',$sEntityJoin.'_delete');
			$iCacheTime=60*60*24; // todo: скорее лучше хранить в свойстве сущности, для возможности выборочного переопределения
			/**
			 * Переопределяем из параметров
			 */
			if (isset($aFilter['#cache'][0])) $sCacheKey=$aFilter['#cache'][0];
			if (isset($aFilter['#cache'][1])) $aCacheTags=$aFilter['#cache'][1];
			if (isset($aFilter['#cache'][2])) $iCacheTime=$aFilter['#cache'][2];
			/**
			 * Смотрим в кеше
			 */
			if (false === ($iCount = $this->Cache_Get($sCacheKey))) {
				$iCount=$this->oMapperORM->GetCountItemsByJoinEntity($sEntityJoin,$sKeyJoin,$sRelationKey,$aRelationValues,$aFilter,$sEntityFull);
				$this->Cache_Set($iCount,$sCacheKey, $aCacheTags, $iCacheTime);
			}
		}

		return $iCount;
	}
	/**
	 * Ставим хук на вызов неизвестного метода и считаем что хотели вызвать метод какого либо модуля.
	 * Также обрабатывает различные ORM методы сущности, например
	 * <pre>
	 * $oUser->Save();
	 * $oUser->Delete();
	 * </pre>
	 * И методы модуля ORM, например
	 * <pre>
	 *	$this->User_getUserItemsByName('Claus');
	 *	$this->User_getUserItemsAll();
	 * </pre>
	 * @see Engine::_CallModule
	 *
	 * @param string $sName Имя метода
	 * @param array $aArgs Аргументы
	 * @return mixed
	 */
	public function __call($sName,$aArgs) {
		$sNameUnderscore=func_underscore($sName);

		if (preg_match("@^add([a-z]+)$@i",$sName,$aMatch)) {
			return $this->_AddEntity($aArgs[0]);
		}

		if (preg_match("@^update([a-z]+)$@i",$sName,$aMatch)) {
			return $this->_UpdateEntity($aArgs[0]);
		}

		if (preg_match("@^save([a-z]+)$@i",$sName,$aMatch)) {
			return $this->_SaveEntity($aArgs[0]);
		}

		if (preg_match("@^delete([a-z]+)$@i",$sName,$aMatch) and !strpos($sNameUnderscore,'items_by_filter')) {
			return $this->_DeleteEntity($aArgs[0]);
		}

		if (preg_match("@^reload([a-z]+)$@i",$sName,$aMatch)) {
			return $this->_ReloadEntity($aArgs[0]);
		}

		if (preg_match("@^showcolumnsfrom([a-z]+)$@i",$sName,$aMatch)) {
			return $this->_ShowColumnsFrom($aArgs[0]);
		}

		if (preg_match("@^showprimaryindexfrom([a-z]+)$@i",$sName,$aMatch)) {
			return $this->_ShowPrimaryIndexFrom($aArgs[0]);
		}

		if (preg_match("@^getchildrenof([a-z]+)$@i",$sName,$aMatch)) {
			return $this->_GetChildrenOfEntity($aArgs[0]);
		}

		if (preg_match("@^getparentof([a-z]+)$@i",$sName,$aMatch)) {
			return $this->_GetParentOfEntity($aArgs[0]);
		}

		if (preg_match("@^getdescendantsof([a-z]+)$@i",$sName,$aMatch)) {
			return $this->_GetDescendantsOfEntity($aArgs[0]);
		}

		if (preg_match("@^getancestorsof([a-z]+)$@i",$sName,$aMatch)) {
			return $this->_GetAncestorsOfEntity($aArgs[0]);
		}

		if (preg_match("@^loadtreeof([a-z]+)$@i",$sName,$aMatch)) {
			$sEntityFull = array_key_exists(1,$aMatch) ? $aMatch[1] : null;
			return $this->LoadTree(isset($aArgs[0]) ? $aArgs[0] : array(), $sEntityFull);
		}

		$iEntityPosEnd=0;
		if(strpos($sNameUnderscore,'_items')>=3) {
			$iEntityPosEnd=strpos($sNameUnderscore,'_items');
		} else if(strpos($sNameUnderscore,'_by')>=3) {
			$iEntityPosEnd=strpos($sNameUnderscore,'_by');
		} else if(strpos($sNameUnderscore,'_all')>=3) {
			$iEntityPosEnd=strpos($sNameUnderscore,'_all');
		}
		if($iEntityPosEnd && $iEntityPosEnd > 4) {
			$sEntityName=substr($sNameUnderscore,4,$iEntityPosEnd-4);
		} else {
			$sEntityName=func_underscore(Engine::GetModuleName($this)).'_';
			$sNameUnderscore=substr_replace($sNameUnderscore,$sEntityName,4,0);
			$iEntityPosEnd=strlen($sEntityName)-1+4;
		}

		$sNameUnderscore=substr_replace($sNameUnderscore,str_replace('_','',$sEntityName),4,$iEntityPosEnd-4);

		$sEntityName=func_camelize($sEntityName);
		/**
		 * getMaxRatingFromUserByFilter() get_max_rating_from_user_by_filter
		 */
		if (preg_match("@^get_(max|min|avg|sum)_([a-z][a-z0-9]*)_from_([a-z][a-z0-9]*)_by_filter$@i",func_underscore($sName),$aMatch)) {
			return $this->GetAggregateFunctionByFilter($aMatch[1],$aMatch[2],isset($aArgs[0]) ? $aArgs[0] : array(),func_camelize($aMatch[3]));
		}

		/**
		 * getMaxRatingFromUserByStatusAndActive() get_max_rating_from_user_by_status_and_active
		 */
		if (preg_match("@^get_(max|min|avg|sum)_([a-z][a-z0-9]*)_from_([a-z][a-z0-9]*)_by_([_a-z]+)$@i",func_underscore($sName),$aMatch)) {
			$aSearchParams=explode('_and_',$aMatch[4]);
			$aSplit=array_chunk($aArgs,count($aSearchParams));
			$aFilter=array_combine($aSearchParams,$aSplit[0]);
			if (isset($aSplit[1][0])) {
				$aFilter=array_merge($aFilter,$aSplit[1][0]);
			}
			return $this->GetAggregateFunctionByFilter($aMatch[1],$aMatch[2],$aFilter,func_camelize($aMatch[3]));
		}

		/**
		 * getCountFromUserByFilter() get_count_from_user_by_filter
		 */
		if (preg_match("@^get_count_from_([a-z][a-z0-9]*)_by_filter$@i",func_underscore($sName),$aMatch)) {
			return $this->GetCountItemsByFilter(isset($aArgs[0]) ? $aArgs[0] : array(),func_camelize($aMatch[1]));
		}

		/**
		 * getUserItemsByFilter() get_user_items_by_filter
		 */
		if (preg_match("@^get_([a-z]+)((_items)|())_by_filter$@i",$sNameUnderscore,$aMatch)) {
			if ($aMatch[2]=='_items') {
				return $this->GetItemsByFilter($aArgs[0],$sEntityName);
			} else {
				return $this->GetByFilter($aArgs[0],$sEntityName);
			}
		}

		/**
		 * deleteUserItemsByFilter() delete_user_items_by_filter
		 */
		if (preg_match("@^delete_([a-z\_]+)_items_by_filter$@i",func_underscore($sName),$aMatch)) {
			return $this->DeleteItemsByFilter(isset($aArgs[0]) ? $aArgs[0] : array(),func_camelize($aMatch[1]));
		}

		/**
		 * getUserItemsByArrayId() get_user_items_by_array_id
		 */
		if (preg_match("@^get_([a-z]+)_items_by_array_([_a-z]+)$@i",$sNameUnderscore,$aMatch)) {
			return $this->GetItemsByArray(array($aMatch[2]=>$aArgs[0]),$sEntityName);
		}

		/**
		 * getUserItemsByJoinEntity() get_user_items_by_join_entity
		 */
		if (preg_match("@^get_([a-z]+)_items_by_join_entity$@i",$sNameUnderscore,$aMatch)) {
			return $this->GetItemsByJoinEntity($aArgs[0],$aArgs[1],$aArgs[2],$aArgs[3],$aArgs[4],func_camelize($sEntityName));
		}

		/**
		 * getUserByLogin()					get_user_by_login
		 * getUserByLoginAndMail()			get_user_by_login_and_mail
		 * getUserItemsByName()				get_user_items_by_name
		 * getUserItemsByNameAndActive()	get_user_items_by_name_and_active
		 * getUserItemsByDateRegisterGte()	get_user_items_by_date_register_gte		(>=)
		 * getUserItemsByProfileNameLike()	get_user_items_by_profile_name_like
		 * getUserItemsByCityIdIn()			get_user_items_by_city_id_in
		 */
		if (preg_match("@^get_([a-z]+)((_items)|())_by_([_a-z]+)$@i",$sNameUnderscore,$aMatch)) {
			$aAliases = array( '_gte' => ' >=', '_lte' => ' <=', '_gt' => ' >', '_lt' => ' <', '_like' => ' LIKE', '_in' => ' IN' );
			$sSearchParams = str_replace(array_keys($aAliases),array_values($aAliases),$aMatch[5]);
			$aSearchParams=explode('_and_',$sSearchParams);
			$aSplit=array_chunk($aArgs,count($aSearchParams));
			$aFilter=array_combine($aSearchParams,$aSplit[0]);
			if (isset($aSplit[1][0])) {
				$aFilter=array_merge($aFilter,$aSplit[1][0]);
			}
			if ($aMatch[2]=='_items') {
				return $this->GetItemsByFilter($aFilter,$sEntityName);
			} else {
				return $this->GetByFilter($aFilter,$sEntityName);
			}
		}

		/**
		 * getUserAll()			get_user_all 		OR
		 * getUserItemsAll()	get_user_items_all
		 */
		if (preg_match("@^get_([a-z]+)_all$@i",$sNameUnderscore,$aMatch) ||
			preg_match("@^get_([a-z]+)_items_all$@i",$sNameUnderscore,$aMatch)
		) {
			$aFilter=array();
			if (isset($aArgs[0]) and is_array($aArgs[0])) {
				$aFilter=$aArgs[0];
			}
			return $this->GetItemsByFilter($aFilter,$sEntityName);
		}

		return parent::__call($sName,$aArgs);
	}
	/**
	 * Построение дерева
	 *
	 * @param array $aItems	Список сущностей
	 * @param array $aList
	 * @param int $iLevel	Текущий уровень вложенности
	 * @return array
	 */
	static function buildTree($aItems,$aList=array(),$iLevel=0) {
		if (!$aItems) {
			return array();
		}
		foreach($aItems as $oEntity) {
			$aChildren=$oEntity->getChildren();
			$bHasChildren = !empty($aChildren);
			$sEntityId = $oEntity->_getDataOne($oEntity->_getPrimaryKey());
			$aList[$sEntityId] = array(
				'entity'		 => $oEntity,
				'parent_id'		 => $oEntity->_getTreeParentKeyValue(),
				'children_count' => $bHasChildren ? count($aChildren) : 0,
				'level'			 => $iLevel,
			);
			if($bHasChildren) {
				$aList=self::buildTree($aChildren,$aList,$iLevel+1);
			}
		}
		return $aList;
	}
	/**
	 * Выполняет обновление связи many_to_many у сущности
	 *
	 * @param $oEntity
	 * @param $sRelationKey
	 */
	protected function _updateManyToManyRelation($oEntity,$sRelationKey) {
		$aRelations=$oEntity->_getRelations();
		if (!isset($aRelations[$sRelationKey][0]) or $aRelations[$sRelationKey][0]!=EntityORM::RELATION_TYPE_MANY_TO_MANY) {
			return;
		}
		$aFilterAdd=isset($aRelations[$sRelationKey][5]) ? $aRelations[$sRelationKey][5] : array();
		$oEntityRelation=Engine::GetEntity($aRelations[$sRelationKey][3]);
		/**
		 * По сущности связи формируем запрос за получение списка сохраненых связей в БД
		 */
		$sCmd=Engine::GetPluginPrefix($aRelations[$sRelationKey][3]).'Module'.Engine::GetModuleName($aRelations[$sRelationKey][3]).'_Get'.Engine::GetEntityName($aRelations[$sRelationKey][3]).'ItemsByFilter';
		list($aFilter)=$this->oMapperORM->BuildFilter($aFilterAdd,$oEntityRelation);
		$aDataInsert=$aFilter;
		$aFilter['#index-from']=$aRelations[$sRelationKey][2];
		$aFilter[$aRelations[$sRelationKey][4]]=$oEntity->_getPrimaryKeyValue();
		$aRelationItemsSaved=Engine::GetInstance()->_CallModule($sCmd,array($aFilter));
		/**
		 * Получаем текущие связи из сущности
		 */
		$aTargetItemsCurrent=$oEntity->$sRelationKey->getCollection();
		/**
		 * Удаляем связи, которых нет в текущих связях
		 */
		foreach($aRelationItemsSaved as $k=>$oRelationItem) {
			if (!isset($aTargetItemsCurrent[$k])) {
				$oRelationItem->Delete();
			}
		}
		/**
		 * Создаем новые связи, которых нет в сохраненных
		 */
		foreach($aTargetItemsCurrent as $k=>$oTargetItem) {
			if (!isset($aRelationItemsSaved[$k])) {
				$oRelationNew=Engine::GetEntity($aRelations[$sRelationKey][3]);
				$aDataInsert[$aRelations[$sRelationKey][4]]=$oEntity->_getPrimaryKeyValue();
				$aDataInsert[$aRelations[$sRelationKey][2]]=$oTargetItem->_getPrimaryKeyValue();
				$oRelationNew->_setData($aDataInsert);
				$oRelationNew->Add();
			}
		}
	}
	/**
	 * Выполняет удаление всех связей many_to_many сущности
	 *
	 * @param $oEntity
	 * @param $sRelationKey
	 */
	protected function _deleteManyToManyRelation($oEntity,$sRelationKey) {
		$aRelations=$oEntity->_getRelations();
		if (!isset($aRelations[$sRelationKey][0]) or $aRelations[$sRelationKey][0]!=EntityORM::RELATION_TYPE_MANY_TO_MANY) {
			return;
		}
		$aFilterAdd=isset($aRelations[$sRelationKey][5]) ? $aRelations[$sRelationKey][5] : array();
		$oEntityRelation=Engine::GetEntity($aRelations[$sRelationKey][3]);
		/**
		 * По сущности связи формируем запрос за получение списка сохраненых связей в БД
		 */
		$sCmd=Engine::GetPluginPrefix($aRelations[$sRelationKey][3]).'Module'.Engine::GetModuleName($aRelations[$sRelationKey][3]).'_Get'.Engine::GetEntityName($aRelations[$sRelationKey][3]).'ItemsByFilter';
		list($aFilter)=$this->oMapperORM->BuildFilter($aFilterAdd,$oEntityRelation);
		$aFilter[$aRelations[$sRelationKey][4]]=$oEntity->_getPrimaryKeyValue();
		$aRelationItemsSaved=Engine::GetInstance()->_CallModule($sCmd,array($aFilter));
		foreach($aRelationItemsSaved as $oRelation) {
			$oRelation->Delete();
		}
	}

	/**
	 * Приводит название сущности к единому формату полного имени класса
	 * Если используется наследование, то возвращается корневой класс
	 * $sEntity может содержать как короткое имя сущности (без плагина и модуля), так и полное
	 *
	 * @param string|object|null $sEntity
	 *
	 * @return string
	 */
	protected function _NormalizeEntityRootName($sEntity) {
		/**
		 * Если передан объект сущности, то просто возвращаем ее корневой класс
		 */
		if (is_object($sEntity)) {
			return $this->Plugin_GetRootDelegater('entity',get_class($sEntity));
		}
		/**
		 * Разбиваем сущность на составляющие
		 */
		if (is_null($sEntity)) {
			$sPluginPrefix=Engine::GetPluginPrefix($this);
			$sModuleName=Engine::GetModuleName($this);
			$sEntityName=Engine::GetEntityName($this);
		} elseif (substr_count($sEntity,'_')) {
			$sPluginPrefix=Engine::GetPluginPrefix($sEntity);
			$sModuleName=Engine::GetModuleName($sEntity);
			$sEntityName=Engine::GetEntityName($sEntity);
		} else {
			$sPluginPrefix=Engine::GetPluginPrefix($this);
			$sModuleName=Engine::GetModuleName($this);
			$sEntityName=$sEntity;
		}
		/**
		 * Получаем корневой модуль
		 */
		$sModuleRoot=$this->Plugin_GetRootDelegater('module',$sPluginPrefix.'Module'.$sModuleName);
		/**
		 * Возвращаем корневую сущность
		 */
		return $this->Plugin_GetRootDelegater('entity',$sModuleRoot.'_Entity'.$sEntityName);
	}
}