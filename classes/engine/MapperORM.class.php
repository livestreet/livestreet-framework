<?php
/*-------------------------------------------------------
*
*   LiveStreet Engine Social Networking
*   Copyright © 2008 Mzhelskiy Maxim
*
*--------------------------------------------------------
*
*   Official site: www.livestreet.ru
*   Contact e-mail: rus.engine@gmail.com
*
*   GNU General Public License, version 2:
*   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*
---------------------------------------------------------
*/

/**
 * Системный класс мапера ORM для работы с БД
 *
 * @package engine.orm
 * @since 1.0
 */
class MapperORM extends Mapper {
	/**
	 * Добавление сущности в БД
	 *
	 * @param EntityORM $oEntity	Объект сущности
	 * @return int|bool	Если есть primary индекс с автоинкрементом, то возвращает его для новой записи
	 */
	public function AddEntity($oEntity) {
		$sTableName = self::GetTableName($oEntity);

		$sql = "INSERT INTO ".$sTableName." SET ?a ";
		return $this->oDb->query($sql,$oEntity->_getDataFields());
	}
	/**
	 * Обновление сущности
	 *
	 * @param EntityORM $oEntity	Объект сущности
	 * @return int|bool	Возвращает число измененых записей в БД
	 */
	public function UpdateEntity($oEntity) {
		$sTableName = self::GetTableName($oEntity);

		if($aPrimaryKey=$oEntity->_getPrimaryKey()) {
			// Возможен составной ключ
			if (!is_array($aPrimaryKey)) {
				$aPrimaryKey=array($aPrimaryKey);
			}
			$sWhere=' 1 = 1 ';
			foreach ($aPrimaryKey as $sField) {
				$sWhere.=' and '.$this->oDb->escape($sField,true)." = ".$this->oDb->escape($oEntity->_getDataOne($sField));
			}
			$sql = "UPDATE ".$sTableName." SET ?a WHERE {$sWhere}";
			return $this->oDb->query($sql,$oEntity->_getDataFields());
		} else {
			$aOriginalData = $oEntity->_getOriginalData();
			$sWhere = implode(' AND ',array_map(create_function(
													'$k,$v,$oDb',
													'return "{$oDb->escape($k,true)} = {$oDb->escape($v)}";'
												),array_keys($aOriginalData),array_values($aOriginalData),array_fill(0,count($aOriginalData),$this->oDb)));
			$sql = "UPDATE ".$sTableName." SET ?a WHERE 1=1 AND ". $sWhere;
			return $this->oDb->query($sql,$oEntity->_getDataFields());
		}
	}
	/**
	 * Удаление сущности
	 *
	 * @param EntityORM $oEntity	Объект сущности
	 * @return int|bool	Возвращает число удаленных записей в БД
	 */
	public function DeleteEntity($oEntity) {
		$sTableName = self::GetTableName($oEntity);

		if($aPrimaryKey=$oEntity->_getPrimaryKey()) {
			// Возможен составной ключ
			if (!is_array($aPrimaryKey)) {
				$aPrimaryKey=array($aPrimaryKey);
			}
			$sWhere=' 1 = 1 ';
			foreach ($aPrimaryKey as $sField) {
				$sWhere.=' and '.$this->oDb->escape($sField,true)." = ".$this->oDb->escape($oEntity->_getDataOne($sField));
			}
			$sql = "DELETE FROM ".$sTableName." WHERE {$sWhere}";
			return $this->oDb->query($sql);
		} else {
			$aOriginalData = $oEntity->_getOriginalData();
			$sWhere = implode(' AND ',array_map(create_function(
													'$k,$v,$oDb',
													'return "{$oDb->escape($k,true)} = {$oDb->escape($v)}";'
												),array_keys($aOriginalData),array_values($aOriginalData),array_fill(0,count($aOriginalData),$this->oDb)));
			$sql = "DELETE FROM ".$sTableName." WHERE 1=1 AND ". $sWhere;
			return $this->oDb->query($sql);
		}
	}
	/**
	 * Получение сущности по фильтру
	 *
	 * @param array $aFilter	Фильтр
	 * @param string $sEntityFull	Название класса сущности
	 * @return EntityORM|null
	 */
	public function GetByFilter($aFilter,$sEntityFull) {
		$oEntitySample=Engine::GetEntity($sEntityFull);
		$sTableName = self::GetTableName($sEntityFull);

		list($aFilterFields,$sFilterFields,$sJoinTables)=$this->BuildFilter($aFilter,$oEntitySample);
		list($sOrder,$sLimit,$sGroup,$sSelect)=$this->BuildFilterMore($aFilter,$oEntitySample);

		if (!$sSelect) {
			$sSelect='t.*';
		}

		$sql = "SELECT {$sSelect} FROM ".$sTableName." t {$sJoinTables} WHERE 1=1 {$sFilterFields} {$sGroup} {$sOrder} LIMIT 0,1";
		$aQueryParams=array_merge(array($sql),array_values($aFilterFields));

		if($aRow=call_user_func_array(array($this->oDb,'selectRow'),$aQueryParams)) {
			$oEntity=Engine::GetEntity($sEntityFull,$aRow);
			$oEntity->_SetIsNew(false);
			return $oEntity;
		}
		return null;
	}
	/**
	 * Получение списка сущностей по фильтру
	 *
	 * @param array $aFilter	Фильтр
	 * @param string $sEntityFull	Название класса сущности
	 * @return array
	 */
	public function GetItemsByFilter($aFilter,$sEntityFull) {
		$oEntitySample=Engine::GetEntity($sEntityFull);
		$sTableName = self::GetTableName($sEntityFull);

		list($aFilterFields,$sFilterFields,$sJoinTables)=$this->BuildFilter($aFilter,$oEntitySample);
		list($sOrder,$sLimit,$sGroup,$sSelect)=$this->BuildFilterMore($aFilter,$oEntitySample);

		if (!$sSelect) {
			$sSelect='t.*';
		}

		$sql = "SELECT {$sSelect} FROM ".$sTableName." t {$sJoinTables} WHERE 1=1 {$sFilterFields} {$sGroup} {$sOrder} {$sLimit} ";
		$aQueryParams=array_merge(array($sql),array_values($aFilterFields));
		$aItems=array();
		if($aRows=call_user_func_array(array($this->oDb,'select'),$aQueryParams)) {
			foreach($aRows as $aRow) {
				$oEntity=Engine::GetEntity($sEntityFull,$aRow);
				$oEntity->_SetIsNew(false);
				$aItems[] = $oEntity;
			}
		}
		return $aItems;
	}
	/**
	 * Получение числа сущностей по фильтру
	 *
	 * @param array $aFilter	Фильтр
	 * @param string $sEntityFull	Название класса сущности
	 * @return int
	 */
	public function GetCountItemsByFilter($aFilter,$sEntityFull) {
		$oEntitySample=Engine::GetEntity($sEntityFull);
		$sTableName = self::GetTableName($sEntityFull);

		list($aFilterFields,$sFilterFields,$sJoinTables)=$this->BuildFilter($aFilter,$oEntitySample);
		list($sOrder,$sLimit,$sGroup)=$this->BuildFilterMore($aFilter,$oEntitySample);

		if ($sGroup) {
			/**
			 * Т.к. count меняет свою логику при наличии группировки
			 */
			$sql = "SELECT SQL_CALC_FOUND_ROWS * FROM `".$sTableName."` t {$sJoinTables} WHERE 1=1 {$sFilterFields} {$sGroup} ";
		} else {
			$sql = "SELECT count(*) as c FROM ".$sTableName." t {$sJoinTables} WHERE 1=1 {$sFilterFields} {$sGroup} ";
		}
		$aQueryParams=array_merge(array($sql),array_values($aFilterFields));
		if($aRow=call_user_func_array(array($this->oDb,'selectRow'),$aQueryParams)) {
			if ($sGroup) {
				$aRow=$this->oDb->selectRow('SELECT FOUND_ROWS() as c;');
			}
			return $aRow['c'];
		}
		return 0;
	}

	public function GetItemsByJoinEntity($sEntityJoin,$sKeyJoin,$sRelationKey,$aRelationValues,$aFilter,$sEntityFull) {
		$oEntitySample=Engine::GetEntity($sEntityFull);
		$oEntityJoinSample=Engine::GetEntity($sEntityJoin);
		$sTableName = self::GetTableName($sEntityFull);
		$sTableJoinName = self::GetTableName($sEntityJoin);

		/**
		 * Формируем параметры по таблице связей
		 */
		list($aFilterFields,$sFilterFields)=$this->BuildFilter($aFilter,$oEntityJoinSample);
		list($sOrder,$sLimit)=$this->BuildFilterMore($aFilter,$oEntityJoinSample);
		/**
		 * Формируем список полей для возврата у таблице связей
		 */
		$aFieldsJoinReturn=$oEntityJoinSample->_getFields();
		foreach($aFieldsJoinReturn as $k=>$sField) {
			if (!is_numeric($k)) {
				// Удаляем служебные (примари) поля
				unset($aFieldsJoinReturn[$k]);
				continue;
			}
			$aFieldsJoinReturn[$k]="t.`{$sField}` as t_join_{$sField}";
		}
		$sFieldsJoinReturn=join(', ',$aFieldsJoinReturn);

		if (!is_array($aRelationValues)) {
			$aRelationValues=array($aRelationValues);
		}
		/**
		 * SQL и параметры
		 */
		$sql = "SELECT {$sFieldsJoinReturn}, b.* FROM ?# t LEFT JOIN ?# b ON b.?# = t.?# WHERE t.?# in ( ?a ) {$sFilterFields} {$sOrder} {$sLimit}";
		$aQueryParams=array_merge(array($sql,$sTableJoinName,$sTableName,$oEntitySample->_getPrimaryKey(),$sRelationKey,$sKeyJoin,$aRelationValues),array_values($aFilterFields));
		$aItems = array();
		/**
		 * Выполняем запрос
		 */
		if($aRows=call_user_func_array(array($this->oDb,'select'),$aQueryParams)) {
			foreach($aRows as $aRow) {
				$aData=array();
				$aDataRelation=array();
				foreach($aRow as $k=>$v) {
					if (strpos($k,'t_join_')===0) {
						$aDataRelation[str_replace('t_join_','',$k)]=$v;
					} else {
						$aData[$k]=$v;
					}
				}
				$aData['_relation_entity']=Engine::GetEntity($sEntityJoin,$aDataRelation);
				$oEntity=Engine::GetEntity($sEntityFull,$aData);
				$oEntity->_SetIsNew(false);
				$aItems[] = $oEntity;
			}
		}
		return $aItems;
	}

	public function GetCountItemsByJoinEntity($sEntityJoin,$sKeyJoin,$sRelationKey,$aRelationValues,$aFilter,$sEntityFull) {
		$oEntitySample=Engine::GetEntity($sEntityFull);
		$oEntityJoinSample=Engine::GetEntity($sEntityJoin);
		$sTableName = self::GetTableName($sEntityFull);
		$sTableJoinName = self::GetTableName($sEntityJoin);

		/**
		 * Формируем параметры по таблице связей
		 */
		list($aFilterFields,$sFilterFields)=$this->BuildFilter($aFilter,$oEntityJoinSample);

		if (!is_array($aRelationValues)) {
			$aRelationValues=array($aRelationValues);
		}
		/**
		 * SQL и параметры
		 */
		$sql = "SELECT count(*) as c FROM ?# t LEFT JOIN ?# b ON b.?# = t.?# WHERE t.?# in ( ?a ) {$sFilterFields} ";
		$aQueryParams=array_merge(array($sql,$sTableJoinName,$sTableName,$oEntitySample->_getPrimaryKey(),$sRelationKey,$sKeyJoin,$aRelationValues),array_values($aFilterFields));
		if($aRow=call_user_func_array(array($this->oDb,'selectRow'),$aQueryParams)) {
			return $aRow['c'];
		}
		return 0;
	}
	/**
	 * Построение фильтра
	 *
	 * @param array $aFilter	Фильтр
	 * @param EntityORM $oEntitySample	Объект сущности
	 * @return array
	 */
	public function BuildFilter($aFilter,$oEntitySample) {
		$aFilterFields=array();
		foreach ($aFilter as $k=>$v) {
			if (substr($k,0,1)=='#' || (is_string($v) && substr($v,0,1)=='#')) {

			} else {
				$aFilterFields[$oEntitySample->_getField($k)]=$v;
			}
		}

		$sFilterFields='';
		foreach ($aFilterFields as $k => $v) {
			$aK=explode(' ',trim($k));
			$sFieldCurrent=$aK[0];
			$sConditionCurrent=' = ';
			if (count($aK)>1) {
				$sConditionCurrent=strtolower($aK[1]);
			}
			/**
			 * У поля уже может быть указан префикс таблицы, поэтому делаем проверку
			 */
			if (!strpos($sFieldCurrent,'.')) {
				$sFieldCurrent='t.'.$this->oDb->escape($sFieldCurrent,true);
			}
			if (strtolower($sConditionCurrent)=='in') {
				$sFilterFields.=" and {$sFieldCurrent} {$sConditionCurrent} ( ?a ) ";
			} else {
				$sFilterFields.=" and {$sFieldCurrent} {$sConditionCurrent} ? ";
			}
		}
		if (isset($aFilter['#where']) and is_array($aFilter['#where'])) {
			// '#where' => array('t.id = ?d OR t.name = ?' => array(1,'admin'));
			foreach ($aFilter['#where'] as $sFilterKey => $aValues) {
				$aFilterFields = array_merge($aFilterFields, $aValues);
				$sFilterFields .= ' and '. trim($sFilterKey) .' ';
			}
		}
		/**
		 * Формируем JOIN запрос
		 */
		$sJoinTables='';
		if (isset($aFilter['#join']) and is_array($aFilter['#join'])) {
			$aValuesForMerge=array();
			foreach($aFilter['#join'] as $sJoin => $aValues) {
				if (is_int($sJoin)) {
					$sJoinTables.=' '.$aValues.' ';
				} else {
					$sJoinTables.=' '.$sJoin.' ';
					$aValuesForMerge=array_merge($aValuesForMerge,$aValues);
				}
			}
			$aFilterFields = array_merge($aValuesForMerge,$aFilterFields);
		}
		return array($aFilterFields,$sFilterFields,$sJoinTables);
	}
	/**
	 * Построение дополнительного фильтра
	 * Здесь учитываются ключи фильтра вида #*
	 *
	 * @param array $aFilter	Фильтр
	 * @param EntityORM $oEntitySample	Объект сущности
	 * @return array
	 */
	public function BuildFilterMore($aFilter,$oEntitySample) {
		// Сортировка
		$sOrder='';
		if (isset($aFilter['#order'])) {
			if(!is_array($aFilter['#order'])) {
				$aFilter['#order'] = array($aFilter['#order']);
			}
			foreach ($aFilter['#order'] as $key=>$value) {
				if (is_numeric($key)) {
					$key=$value;
					$value='asc';
				} elseif (!in_array($value,array('asc','desc'))) {
					$value='asc';
				}
				/**
				 * Проверяем на простые выражения: field1 + field2 * field3
				 */
				$aKeyPath=preg_split("#\s?([\-\+\*\\\])\s?#",$key,-1,PREG_SPLIT_DELIM_CAPTURE);
				if (count($aKeyPath)>2) {
					$key='';
					foreach($aKeyPath as $i=>$sKey) {
						if ($i%2==0) {
							$key.='t.'.$this->oDb->escape($oEntitySample->_getField(trim($sKey)),true);
						} else {
							$key.=" {$sKey} ";
						}
					}
				} else {
					/**
					 * Проверяем на FIELD:id -> FIELD(id,?a)
					 */
					$aKeys=explode(':',$key);
					if (count($aKeys)==2) {
						if (strtolower($aKeys[0])=='field' and is_array($aFilter['#order'][$key]) and count($aFilter['#order'][$key])) {
							$key = 'FIELD(t.'.$this->oDb->escape($oEntitySample->_getField(trim($aKeys[1])),true).','.join(',',$aFilter['#order'][$key]).')';
							$value='';
						} else {
							/**
							 * Неизвестное выражение
							 */
							continue;
						}
					} else {
						/**
						 * Пропускаем экранирование функций
						 */
						if (!in_array($key,array('rand()'))) {
							/**
							 * Проверяем наличие префикса таблицы
							 */
							if (!strpos($oEntitySample->_getField($key),'.')) {
								$key = 't.'.$this->oDb->escape($oEntitySample->_getField($key),true);
							} else {
								$key=$oEntitySample->_getField($key);
							}
						}
					}
				}

				$sOrder.=" {$key} {$value},";
			}
			$sOrder=trim($sOrder,',');
			if ($sOrder!='') {
				$sOrder="ORDER BY {$sOrder}";
			}
		}

		// Постраничность		
		if (isset($aFilter['#page']) and is_array($aFilter['#page']) and count($aFilter['#page'])==2) { // array(2,15) - 2 - page, 15 - count			
			$aFilter['#limit']=array(($aFilter['#page'][0]-1)*$aFilter['#page'][1],$aFilter['#page'][1]);
		}

		// Лимит
		$sLimit='';
		if (isset($aFilter['#limit'])) { // допустимы варианты: limit=10 , limit=array(10) , limit=array(10,15)
			$aLimit=$aFilter['#limit'];
			if (is_numeric($aLimit)) {
				$iBegin=0;
				$iEnd=$aLimit;
			} elseif (is_array($aLimit)) {
				if (count($aLimit)>1) {
					$iBegin=$aLimit[0];
					$iEnd=$aLimit[1];
				} else {
					$iBegin=0;
					$iEnd=$aLimit[0];
				}
			}
			$sLimit="LIMIT {$iBegin}, {$iEnd}";
		}

		// Группировка
		$sGroup='';
		if (isset($aFilter['#group'])) {
			if(!is_array($aFilter['#group'])) {
				$aFilter['#group'] = array($aFilter['#group']);
			}
			foreach ($aFilter['#group'] as $sField) {
				$sField = $this->oDb->escape($oEntitySample->_getField($sField),true);
				$sGroup.=" t.{$sField},";
			}
			$sGroup=trim($sGroup,',');
			if ($sGroup!='') {
				$sGroup="GROUP BY {$sGroup}";
			}
		}

		// Определение полей в select
		$sSelect='';
		if (isset($aFilter['#select'])) {
			// todo: добавить экранирование полей с учетом префикса таблицы
			if(!is_array($aFilter['#select'])) {
				$aFilter['#select'] = array($aFilter['#select']);
			}
			$sSelect=join(', ',$aFilter['#select']);
		}
		return array($sOrder,$sLimit,$sGroup,$sSelect);
	}
	/**
	 * Список колонок/полей сущности
	 *
	 * @param EntityORM $oEntity	Объект сущности
	 * @return array
	 */
	public function ShowColumnsFrom($oEntity) {
		$sTableName = self::GetTableName($oEntity);
		return $this->ShowColumnsFromTable($sTableName);
	}
	/**
	 * Список колонок/полей таблицы
	 *
	 * @param string $sTableName	Название таблицы
	 * @return array
	 */
	public function ShowColumnsFromTable($sTableName) {
		if (false === ($aItems = Engine::getInstance()->Cache_Get("columns_table_{$sTableName}",'file_orm',true))) {
			$sql = "SHOW COLUMNS FROM ".$sTableName;
			$aItems = array();
			if($aRows=$this->oDb->select($sql)) {
				foreach($aRows as $aRow) {
					$aItems[] = $aRow['Field'];
					if($aRow['Key']=='PRI') {
						$aItems['#primary_key'] = $aRow['Field'];
					}
				}
			}
			Engine::getInstance()->Cache_Set($aItems,"columns_table_{$sTableName}",array(),60*60*4,'file_orm',true);
		}
		return $aItems;
	}
	/**
	 * Primary индекс сущности
	 *
	 * @param EntityORM $oEntity	Объект сущности
	 * @return array
	 */
	public function ShowPrimaryIndexFrom($oEntity) {
		$sTableName = self::GetTableName($oEntity);
		return $this->ShowPrimaryIndexFromTable($sTableName);
	}
	/**
	 * Primary индекс таблицы
	 *
	 * @param string $sTableName	Название таблицы
	 * @return array
	 */
	public function ShowPrimaryIndexFromTable($sTableName) {
		if (false === ($aItems = Engine::getInstance()->Cache_Get("index_table_{$sTableName}",'file_orm',true))) {
			$sql = "SHOW INDEX FROM ".$sTableName;
			$aItems = array();
			if($aRows=$this->oDb->select($sql)) {
				foreach($aRows as $aRow) {
					if ($aRow['Key_name']=='PRIMARY') {
						$aItems[$aRow['Seq_in_index']]=$aRow['Column_name'];
					}
				}
			}
			Engine::getInstance()->Cache_Set($aItems, "index_table_{$sTableName}",array(),60*60*4,'file_orm',true);
		}
		return $aItems;
	}
	/**
	 * Возвращает имя таблицы для сущности
	 *
	 * @param EntityORM $oEntity	Объект сущности
	 * @return string
	 */
	public static function GetTableName($oEntity) {
		/**
		 * Варианты таблиц:
		 * 	prefix_user -> если модуль совпадает с сущностью
		 * 	prefix_user_invite -> если модуль не сопадает с сущностью
		 * Если сущность плагина:
		 * 	prefix_pluginname_user
		 * 	prefix_pluginname_user_invite
		 */
		$sClass = Engine::getInstance()->Plugin_GetDelegater('entity', is_object($oEntity)?get_class($oEntity):$oEntity);
		$sPluginName = func_underscore(Engine::GetPluginName($sClass));
		$sModuleName = func_underscore(Engine::GetModuleName($sClass));
		$sEntityName = func_underscore(Engine::GetEntityName($sClass));
		if (strpos($sEntityName,$sModuleName)===0) {
			$sTable=func_underscore($sEntityName);
		} else {
			$sTable=func_underscore($sModuleName).'_'.func_underscore($sEntityName);
		}
		if ($sPluginName) {
			$sTablePlugin=$sPluginName.'_'.$sTable;
			/**
			 * Для обратной совместимости с 1.0.1
			 * Если такая таблица определена в конфиге, то ок, если нет, то используем старый вариант без имени плагина
			 */
			if (Config::Get('db.table.'.$sTablePlugin)) {
				$sTable=$sTablePlugin;
			}
		}
		/**
		 * Если название таблиц переопределено в конфиге, то возвращаем его
		 */
		if(Config::Get('db.table.'.$sTable)) {
			return Config::Get('db.table.'.$sTable);
		} else {
			return Config::Get('db.table.prefix').$sTable;
		}
	}
}