<?php
/**
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

/*
 * Хранилище "ключ => значение"
 *
 * Позволяет легко и быстро работать с небольшими объемами данных, CRUD операции с которыми теперь занимают всего одну строку кода.
 *
 * Например:
 * 		$this->Storage_Set('keyname', 'some_mixed_value', $this);	// сохранить 'some_mixed_value' под имененем 'keyname' для вашего плагина
 * 		$this->Storage_Get('keyname', $this);						// получить данные по ключу 'keyname' для вашего плагина
 *
 */

class ModuleStorage extends Module {
	
	protected $oMapperStorage = null;
				
	/*
	 * Группа настроек по-умолчанию (инстанция)
	 */
	const DEFAULT_INSTANCE = 'default';
	
	/*
	 * Префикс ключей для кеша
	 */
	const CACHE_FIELD_DATA_PREFIX = 'storage_field_data_';
	
	/*
	 * Имя ключа для ядра
	 */
	const DEFAULT_KEY_NAME = '__default__';

	/*
	 * Префикс для плагина в таблице
	 */
	const PLUGIN_PREFIX = 'plugin_';

	/*
	 * Кеширование параметров на время работы сессии
	 * структура: array('instance' => array('key' => array('param1' => 'value1', 'param2' => 'value2')))
	 */
	protected $aSessionCache = array();


	public function Init() {
		$this->Setup();
	}


	/**
	 * Настройка
	 */
	protected function Setup() {
		$this->oMapperStorage = Engine::GetMapper(__CLASS__);
	}


	/*
	 *
	 * --- Низкоуровневые обертки для работы с БД ---
	 *
	 * tip: для highload проектов эти обертки можно переопределить через плагин чтобы подключить не РСУБД хранилища, такие как, например, Redis
	 *
	 */

	/**
	 * Записать в БД строку одного ключа
	 *
	 * @param			$sKey			ключ
	 * @param			$sValue			значение
	 * @param string 	$sInstance		инстанция
	 * @return mixed
	 */
	protected function SetFieldOne($sKey, $sValue, $sInstance = self::DEFAULT_INSTANCE) {
		/*
		 * низкоуровневая обработка данных происходит только со строковыми значениями
		 */
		$sKey = (string) $sKey;
		$sValue = (string) $sValue;
		$sInstance = (string) $sInstance;
		/*
		 * сбросить кеш по тегу
		 */
		$this->Cache_Clean(Zend_Cache::CLEANING_MODE_MATCHING_TAG, array('storage_field_data'));
		/*
		 * добавить запись в хранилище для ключа
		 */
		return $this->oMapperStorage->SetData($sKey, $sValue, $sInstance);
	}


	/**
	 * Получить из БД строковое значение одного ключа
	 *
	 * @param			$sKey			ключ
	 * @param string 	$sInstance		инстанция
	 * @return mixed
	 */
	protected function GetFieldOne($sKey, $sInstance = self::DEFAULT_INSTANCE) {
		$sKey = (string) $sKey;
		$sInstance = (string) $sInstance;
		/*
		 * построить ключ для кеша
		 */
		$sCacheKey = self::CACHE_FIELD_DATA_PREFIX . $sKey . '_' . $sInstance;
		/*
		 * есть ли такие данные в кеше
		 */
		if (($mData = $this->Cache_Get($sCacheKey)) === false) {
			/*
			 * построить строку части WHERE запроса
			 */
			$sWhere = $this->oMapperStorage->BuildFilter(array(
				'key' => $sKey,
				'instance' => $sInstance
			));
			$mData = null;
			/*
			 * получить данные
			 */
			$aResult = $this->oMapperStorage->GetData($sWhere, 1, 1);
			/*
			 * есть ли данные
			 */
			if ($aResult['count'] != 0) {
				$mData = $aResult['collection']['value'];
				$this->Cache_Set($mData, $sCacheKey, array('storage_field_data'), 60 * 60 * 24 * 365);	// 1 год
			}
		}
		return $mData;
	}


	/**
	 * Удалить из БД ключ
	 *
	 * @param			$sKey			ключ
	 * @param string 	$sInstance		инстанция
	 * @return mixed
	 */
	protected function DeleteFieldOne($sKey, $sInstance = self::DEFAULT_INSTANCE) {
		$sKey = (string) $sKey;
		$sInstance = (string) $sInstance;
		/*
		 * сбросить кеш по тегу
		 */
		$this->Cache_Clean(Zend_Cache::CLEANING_MODE_MATCHING_TAG, array('storage_field_data'));
		/*
		 * построить строку части WHERE запроса
		 */
		$sWhere = $this->oMapperStorage->BuildFilter(array(
			'key' => $sKey,
			'instance' => $sInstance
		));
		/*
		 * удалить данные
		 */
		return $this->oMapperStorage->DeleteData($sWhere, 1);
	}


	/**
	 * Получить из БД все ключи в "сыром" виде
	 *
	 * @param string 	$sInstance		инстанция
	 * @return mixed
	 */
	protected function GetFieldsAll($sInstance = self::DEFAULT_INSTANCE) {
		$sInstance = (string) $sInstance;
		/*
		 * построить ключ для кеша
		 */
		$sCacheKey = self::CACHE_FIELD_DATA_PREFIX . '_fields_all_' . $sInstance;
		/*
		 * есть ли такие данные в кеше
		 */
		if (($mData = $this->Cache_Get($sCacheKey)) === false) {
			/*
			 * построить строку части WHERE запроса
			 */
			$sWhere = $this->oMapperStorage->BuildFilter(array(
				'instance' => $sInstance
			));
			/*
			 * получить данные
			 */
			$mData = $this->oMapperStorage->GetData($sWhere);
			$this->Cache_Set($mData, $sCacheKey, array('storage_field_data'), 60 * 60 * 24 * 365);	// 1 год
		}
		return $mData;
	}

	
	/*
	 *
	 * --- Обработка значений параметров ---
	 *
	 */

	/**
	 * Подготовка значения параметра перед сохранением
	 *
	 * @param $mValue		значение
	 * @return string
	 * @throws Exception	если тип данных - ресурсы
	 */
	protected function PrepareParamValueBeforeSaving($mValue) {
		if (is_resource($mValue)) {
			throw new Exception('Storage: your data must be scalar value, not resource!');
		}
		return $mValue;
	}


	/**
	 * Восстановление значения параметра
	 *
	 * @param $mValue		значение
	 * @return mixed|null
	 */
	protected function RetrieveParamValueFromSavedValue($mValue) {
		return $mValue;
	}


	/**
	 * Перевести данные в строковый вид (сериализировать)
	 *
	 * @param $mValue		значение
	 * @return string
	 */
	protected function PackValue($mValue) {
		return serialize($mValue);
	}


	/**
	 * Восстановить данные из строкового вида (десериализировать)
	 *
	 * @param $mValue		значение
	 * @return mixed|null
	 */
	protected function UnpackValue($mValue) {
		if (($mData = @unserialize($mValue)) !== false) {
			return $mData;
		}
		return null;
	}


	/**
	 * Получить массив используемых значений параметров ключа по "сырым" данным из БД
	 *
	 * @param 			$sKey			ключ
	 * @param 			$sFieldData		"сырые" (серилизированные) данные ключа
	 * @param 			$sInstance		инстанция
	 * @return array					данные
	 */
	protected function GetParamsValuesFromRawData($sKey, $sFieldData, $sInstance = self::DEFAULT_INSTANCE) {
		if ($aData = $this->UnpackValue($sFieldData) and is_array ($aData)) {
			/*
			 * Восстановить значения параметров ключа
			 */
			$aData = array_map(array($this, 'RetrieveParamValueFromSavedValue'), $aData);
			/*
			 * Сохранить в кеше сессии распакованные значения
			 */
			$this->aSessionCache[$sInstance][$sKey] = $aData;
			return $aData;
		}
		return array();
	}

				
	/*
	 *
	 * --- Высокоуровневые обертки для работы непосредственно с параметрами каждого ключа ---
	 *
	 */

	/**
	 * Получить список всех параметров ключа
	 *
	 * @param			$sKey			ключ
	 * @param string 	$sInstance		инстанция
	 * @return array
	 */
	protected function GetParamsAll($sKey, $sInstance = self::DEFAULT_INSTANCE) {
		/*
		 * Если значение есть в кеше сессии - получить его
		 */
		if (isset($this->aSessionCache[$sInstance][$sKey])) {
			return $this->aSessionCache[$sInstance][$sKey];
		}
		/*
		 * Если есть запись для ключа и она не повреждена и корректна
		 */
		if ($sFieldData = $this->GetFieldOne($sKey, $sInstance)) {
			return $this->GetParamsValuesFromRawData($sKey, $sFieldData, $sInstance);
		}
		return array();
	}


	/**
	 * Сохранить значение параметра для ключа
	 *
	 * @param			$sKey				ключ
	 * @param			$sParamName			параметр
	 * @param			$mValue				значение
	 * @param string 	$sInstance			инстанция
	 * @return mixed
	 */
	protected function SetOneParam($sKey, $sParamName, $mValue, $sInstance = self::DEFAULT_INSTANCE) {
		/*
		 * Подготовить значение перед сохранением
		 */
		$mValueChecked = $this->PrepareParamValueBeforeSaving($mValue);
		/*
		 * Объеденить с остальными параметрами ключа
		 */
		$aParamsContainer = $this->GetParamsAll($sKey, $sInstance);
		$aParamsContainer[$sParamName] = $mValueChecked;
		/*
		 * Сохранить в кеше сессии оригинальное значение
		 */
		$this->aSessionCache[$sInstance][$sKey][$sParamName] = $mValue;
		/*
		 * записать упакованные данные в строку
		 */
		return $this->SetFieldOne($sKey, $this->PackValue($aParamsContainer), $sInstance);
	}


	/**
	 * Получить значение параметра для ключа
	 *
	 * @param			$sKey				ключ
	 * @param			$sParamName			параметр
	 * @param string 	$sInstance			инстанция
	 * @return null
	 */
	protected function GetOneParam($sKey, $sParamName, $sInstance = self::DEFAULT_INSTANCE) {
		/*
		 * Если значение есть в кеше сессии - получить его
		 */
		if (isset($this->aSessionCache[$sInstance][$sKey][$sParamName])) {
			return $this->aSessionCache[$sInstance][$sKey][$sParamName];
		}
		/*
		 * Получить одно значение
		 */
		if ($aFieldData = $this->GetParamsAll($sKey, $sInstance) and isset($aFieldData[$sParamName])) {
			return $aFieldData[$sParamName];
		}
		return null;
	}


	/**
	 * Удалить значение параметра для ключа
	 *
	 * @param			$sKey			ключ
	 * @param			$sParamName		параметр
	 * @param string 	$sInstance		инстанция
	 * @return mixed
	 */
	protected function RemoveOneParam($sKey, $sParamName, $sInstance = self::DEFAULT_INSTANCE) {
		/*
		 * Удалить значение из кеша сессии
		 */
		unset($this->aSessionCache[$sInstance][$sKey][$sParamName]);
		/*
		 * Удалить параметр
		 */
		$aParamsContainer = $this->GetParamsAll($sKey, $sInstance);
		unset($aParamsContainer[$sParamName]);
		/*
		 * записать упакованные данные в строку
		 */
		return $this->SetFieldOne($sKey, $this->PackValue($aParamsContainer), $sInstance);
	}


	/**
	 * Удалить все параметры ключа
	 *
	 * @param			$sKey			ключ
	 * @param string 	$sInstance		инстанция
	 * @return mixed
	 */
	protected function RemoveAllParams($sKey, $sInstance = self::DEFAULT_INSTANCE) {
		/*
		 * Удалить все значения из кеша сессии
		 */
		unset($this->aSessionCache[$sInstance][$sKey]);
		return $this->DeleteFieldOne($sKey, $sInstance);
	}


	/**
	 * Сохранить значение параметра для ключа на время сессии (без записи в хранилище)
	 *
	 * @param			$sKey			ключ
	 * @param			$sParamName		параметр
	 * @param			$mValue			значение
	 * @param string 	$sInstance		инстанция
	 */
	protected function SetSmartParam($sKey, $sParamName, $mValue, $sInstance = self::DEFAULT_INSTANCE) {
		/*
		 * tip: В первый запрос все данные будут загружены в сессионное хранилище и при повторном вызове они не будут затираться
		 */
		$this->GetParamsAll($sKey, $sInstance);
		/*
		 * Сохранить в кеше сессии
		 */
		$this->aSessionCache[$sInstance][$sKey][$sParamName] = $mValue;
	}


	/**
	 * Удалить значение параметра для ключа на время сессии (без записи в хранилище)
	 *
	 * @param			$sKey			ключ
	 * @param			$sParamName		параметр
	 * @param string 	$sInstance		инстанция
	 */
	protected function RemoveSmartParam($sKey, $sParamName, $sInstance = self::DEFAULT_INSTANCE) {
		/*
		 * tip: В первый запрос все данные будут загружены в сессионное хранилище и при повторном вызове они не будут затираться
		 */
		$this->GetParamsAll($sKey, $sInstance);
		/*
		 * Удалить в кеше сессии
		 */
		unset($this->aSessionCache[$sInstance][$sKey][$sParamName]);
	}


	/**
	 * Записать в хранилище значения параметров для ключа из кеша сессии
	 *
	 * @param			$sKey			ключ
	 * @param string 	$sInstance		инстанция
	 * @return mixed
	 */
	protected function StoreParams($sKey, $sInstance = self::DEFAULT_INSTANCE) {
		return $this->SetFieldOne($sKey, $this->PackValue($this->aSessionCache[$sInstance][$sKey]), $sInstance);
	}


	/**
	 * Сбросить кеш сессии (без записи в хранилище)
	 *
	 * @param null	 	$sKey			ключ
	 * @param string 	$sInstance		инстанция
	 */
	protected function ResetSessionCache($sKey = null, $sInstance = self::DEFAULT_INSTANCE) {
		if (!is_null($sKey)) {
			unset($this->aSessionCache[$sInstance][$sKey]);
		} else {
			unset($this->aSessionCache[$sInstance]);
		}
	}
	
	
	/*
	 *
	 * --- Хелперы ---
	 *
	 */

	/**
	 * Получить имя ключа из текущего, вызывающего метод, контекста
	 *
	 * @param $oCaller		контекст, вызывающий метод (для движка можно указывать null)
	 * @return string
	 */
	protected function GetKeyForCaller($oCaller = null) {
		$this->CheckCaller($oCaller);
		/*
		 * Получаем имя плагина, если возможно
		 */
		if (!$sCaller = strtolower(Engine::GetPluginName($oCaller))) {
			/*
			 * Если имени нет - значит это вызов ядра
			 */
			return self::DEFAULT_KEY_NAME;
		}
		return self::PLUGIN_PREFIX . $sCaller;
	}


	/**
	 * Проверить корректность указания контекста
	 *
	 * @param $oCaller		контекст, вызывающий метод
	 * @throws Exception	если не объект
	 */
	protected function CheckCaller($oCaller) {
		/*
		 * контекст должен быть указан или нулл для движка
		 */
		if (!is_object($oCaller) and !is_null($oCaller)) {
			throw new Exception('Storage: caller is not correct. Always use "$this" for caller value. Also it can be set to NULL for engine calls');
		}
	}

	
	/*
	 *
	 * --- Конечные методы для использования в движке и плагинах ---
	 *
	 */

	/**
	 * Установить значение
	 *
	 * @param			$sParamName			параметр
	 * @param			$mValue				значение
	 * @param			$oCaller			контекст, вызывающий метод
	 * @param string 	$sInstance			инстанция
	 * @return mixed
	 */
	public function Set($sParamName, $mValue, $oCaller = null, $sInstance = self::DEFAULT_INSTANCE) {
		$sCallerName = $this->GetKeyForCaller($oCaller);
		return $this->SetOneParam($sCallerName, $sParamName, $mValue, $sInstance);
	}


	/**
	 * Получить значение
	 *
	 * @param			$sParamName			параметр
	 * @param			$oCaller			контекст, вызывающий метод
	 * @param string 	$sInstance			инстанция
	 * @return null
	 */
	public function Get($sParamName, $oCaller = null, $sInstance = self::DEFAULT_INSTANCE) {
		$sCallerName = $this->GetKeyForCaller($oCaller);
		return $this->GetOneParam($sCallerName, $sParamName, $sInstance);
	}


	/**
	 * Получить все значения
	 *
	 * @param			$oCaller			контекст, вызывающий метод
	 * @param string 	$sInstance			инстанция
	 * @return array
	 */
	public function GetAll($oCaller = null, $sInstance = self::DEFAULT_INSTANCE) {
		$sCallerName = $this->GetKeyForCaller($oCaller);
		return $this->GetParamsAll($sCallerName, $sInstance);
	}


	/**
	 * Удалить значение
	 *
	 * @param			$sParamName			параметр
	 * @param			$oCaller			контекст, вызывающий метод
	 * @param string 	$sInstance			инстанция
	 * @return mixed
	 */
	public function Remove($sParamName, $oCaller = null, $sInstance = self::DEFAULT_INSTANCE) {
		$sCallerName = $this->GetKeyForCaller($oCaller);
		return $this->RemoveOneParam($sCallerName, $sParamName, $sInstance);
	}


	/**
	 * Удалить все значения
	 *
	 * @param			$oCaller			контекст, вызывающий метод
	 * @param string 	$sInstance			инстанция
	 * @return mixed
	 */
	public function RemoveAll($oCaller = null, $sInstance = self::DEFAULT_INSTANCE) {
		$sCallerName = $this->GetKeyForCaller($oCaller);
		return $this->RemoveAllParams($sCallerName, $sInstance);
	}


	/*
	 *
	 * --- Работа с параметрами только на момент сессии ---
	 *
	 */

	/**
	 * Сохранить значение параметра на время сессии (без записи в хранилище)
	 *
	 * @param			$sParamName			параметр
	 * @param			$mValue				значение
	 * @param			$oCaller			контекст, вызывающий метод
	 * @param string 	$sInstance			инстанция
	 */
	public function SetSmart($sParamName, $mValue, $oCaller = null, $sInstance = self::DEFAULT_INSTANCE) {
		$sCallerName = $this->GetKeyForCaller($oCaller);
		$this->SetSmartParam($sCallerName, $sParamName, $mValue, $sInstance);
	}


	/**
	 * Удалить параметр кеша сессии (без записи в хранилище)
	 *
	 * @param			$sParamName			параметр
	 * @param			$oCaller			контекст, вызывающий метод
	 * @param string 	$sInstance			инстанция
	 */
	public function RemoveSmart($sParamName, $oCaller = null, $sInstance = self::DEFAULT_INSTANCE) {
		$sCallerName = $this->GetKeyForCaller($oCaller);
		$this->RemoveSmartParam($sCallerName, $sParamName, $sInstance);
	}


	/**
	 * Записать в хранилище значения параметров из кеша сессии
	 *
	 * @param			$oCaller			контекст, вызывающий метод
	 * @param string 	$sInstance			инстанция
	 * @return mixed
	 */
	public function Store($oCaller = null, $sInstance = self::DEFAULT_INSTANCE) {
		$sCallerName = $this->GetKeyForCaller($oCaller);
		return $this->StoreParams($sCallerName, $sInstance);
	}


	/**
	 * Сбросить кеш сессии (без записи в хранилище)
	 *
	 * @param			$oCaller			контекст, вызывающий метод
	 * @param string 	$sInstance			инстанция
	 */
	public function Reset($oCaller = null, $sInstance = self::DEFAULT_INSTANCE) {
		$sCallerName = $this->GetKeyForCaller($oCaller);
		$this->ResetSessionCache($sCallerName, $sInstance);
	}


}