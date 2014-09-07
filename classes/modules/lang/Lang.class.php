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
 * Модуль поддержки языковых файлов
 *
 * @package engine.modules
 * @since 1.0
 */
class ModuleLang extends Module {
	/**
	 * Текущий язык ресурса
	 *
	 * @var string
	 */
	protected $sCurrentLang;
	/**
	 * Язык ресурса, используемый по умолчанию
	 *
	 * @var string
	 */
	protected $sDefaultLang;
	/**
	 * Путь к языковым файлам
	 *
	 * @var string
	 */
	protected $sLangPath;
	/**
	 * Список языковых текстовок
	 *
	 * @var array
	 */
	protected $aLangMsg=array();
	/**
	 * Список текстовок для JS
	 *
	 * @var array
	 */
	protected $aLangMsgJs=array();

	/**
	 * Инициализация модуля
	 *
	 */
	public function Init() {
		$this->Hook_Run('lang_init_start');

		$this->InitConfig();
		$this->InitLang();
	}
	/**
	 * Инициализирует языковые параметры из конфига
	 */
	protected function InitConfig() {
		$this->sCurrentLang = Config::Get('lang.current');
		$this->sDefaultLang = Config::Get('lang.default');
		$this->sLangPath = Config::Get('lang.path');
	}
	/**
	 * Инициализирует языковой файл
	 *
	 */
	protected function InitLang() {
		/**
		 * Если используется кеширование через memcaсhed, то сохраняем данные языкового файла в кеш
		 */
		if (Config::Get('sys.cache.type')=='memory') {
			if (false === ($this->aLangMsg = $this->Cache_Get("lang_{$this->sCurrentLang}_".Config::Get('view.skin')))) {
				$this->aLangMsg=array();
				$this->LoadLangFiles($this->sDefaultLang);
				if($this->sCurrentLang!=$this->sDefaultLang) $this->LoadLangFiles($this->sCurrentLang);
				$this->Cache_Set($this->aLangMsg, "lang_{$this->sCurrentLang}_".Config::Get('view.skin'), array(), 60*60);
			}
		} else {
			$this->LoadLangFiles($this->sDefaultLang);
			if($this->sCurrentLang!=$this->sDefaultLang) $this->LoadLangFiles($this->sCurrentLang);
		}

		$this->LoadLangJs();
		/**
		 * Загружаем в шаблон
		 */
		$this->Viewer_Assign('aLang',array(&$this->aLangMsg),false,true);
	}
	/**
	 * Загружает из конфига текстовки для JS
	 *
	 */
	protected function LoadLangJs() {
		$aMsg=Config::Get('lang.load_to_js');
		if (is_array($aMsg) and count($aMsg)) {
			$this->aLangMsgJs=$aMsg;
		}
	}
	/**
	 * Прогружает в шаблон текстовки в виде JS
	 *
	 */
	protected function AssignToJs() {
		$aLangMsg=array();
		foreach ($this->aLangMsgJs as $sName) {
			$aLangMsg[$sName]=$this->Get($sName,array(),false);
		}
		$this->Viewer_Assign('aLangJs',$aLangMsg);
	}
	/**
	 * Добавляет текстовку к JS
	 *
	 * @param array $aKeys	Список текстовок
	 */
	public function AddLangJs($aKeys) {
		if (!is_array($aKeys)) {
			$aKeys=array($aKeys);
		}
		$this->aLangMsgJs=array_merge($this->aLangMsgJs,$aKeys);
	}
	/**
	 * Загружает текстовки из языковых файлов
	 *
	 * @param $sLangName	Язык для загрузки
	 */
	protected function LoadLangFiles($sLangName) {
		/**
		 * Загружаем текстовки фреймворка
		 */
		$sLangFilePath = Config::Get('path.framework.server').'/frontend/i18n/'.$sLangName.'.php';
		if(file_exists($sLangFilePath)) {
			$this->AddMessages(include($sLangFilePath));
		}
		/**
		 * Загружаем текстовки приложения
		 */
		$sLangFilePath = $this->sLangPath.'/'.$sLangName.'.php';
		if(file_exists($sLangFilePath)) {
			$this->AddMessages(include($sLangFilePath));
		}
		/**
		 * Ищет языковые файлы модулей и объединяет их с текущим
		 */
		$sDirConfig=$this->sLangPath.'/modules/';
		if (is_dir($sDirConfig) and $hDirConfig = opendir($sDirConfig)) {
			while (false !== ($sDirModule = readdir($hDirConfig))) {
				if ($sDirModule !='.' and $sDirModule !='..' and is_dir($sDirConfig.$sDirModule)) {
					$sFileConfig=$sDirConfig.$sDirModule.'/'.$sLangName.'.php';
					if (file_exists($sFileConfig)) {
						$this->AddMessages(include($sFileConfig), array('category' =>'module', 'name' =>$sDirModule));
					}
				}
			}
			closedir($hDirConfig);
		}
		/**
		 * Ищет языковые файлы активированных плагинов
		 */
		if($aPluginList = Engine::getInstance()->GetPlugins()) {
			$aPluginList=array_keys($aPluginList);
			$sDir=Config::Get('path.application.plugins.server').'/';

			foreach ($aPluginList as $sPluginName) {
				$aFiles=glob($sDir.$sPluginName.'/templates/'.Config::Get('lang.dir').'/'.$sLangName.'.php');
				if($aFiles and count($aFiles)) {
					foreach ($aFiles as $sFile) {
						if (file_exists($sFile)) {
							$this->AddMessages(include($sFile), array('category' =>'plugin', 'name' =>$sPluginName));
						}
					}
				}
			}

		}
		/**
		 * Ищет языковой файл текущего шаблона
		 */
		$this->LoadLangFileTemplate($sLangName);
	}
	/**
	 * Загружает языковой файл текущего шаблона
	 *
	 * @param string $sLangName	Язык для загрузки
	 */
	public function LoadLangFileTemplate($sLangName) {
		$sFile=Config::Get('path.smarty.template').'/settings/'.Config::Get('lang.dir').'/'.$sLangName.'.php';
		if (file_exists($sFile)) {
			$this->AddMessages(include($sFile));
		}
	}
	/**
	 * Установить текущий язык
	 *
	 * @param string $sLang	Название языка
	 */
	public function SetLang($sLang) {
		$this->sCurrentLang=$sLang;
		$this->InitLang();
	}
	/**
	 * Получить текущий язык
	 *
	 * @return string
	 */
	public function GetLang() {
		return $this->sCurrentLang;
	}
	/**
	 * Получить дефолтный язык
	 *
	 * @return string
	 */
	public function GetLangDefault() {
		return $this->sDefaultLang;
	}
	/**
	 * Получить список текстовок
	 *
	 * @return array
	 */
	public function GetLangMsg() {
		return $this->aLangMsg;
	}
	/**
	 * Получает текстовку по её имени
	 *
	 * @param  string $sName	Имя текстовки
	 * @param  array  $aReplace	Список параметром для замены в текстовке
	 * @param  bool  $bDelete	Удалять или нет параметры, которые не были заменены
	 * @return string
	 */
	public function Get($sName,$aReplace=array(),$bDelete=true) {
		if (strpos($sName, '.')) {
			$sLang = $this->aLangMsg;
			$aKeys = explode('.', $sName);
			foreach ($aKeys as $k) {
				if (isset($sLang[$k])) {
					$sLang = $sLang[$k];
				} else {
					return $sName;
				}
			}
		} else {
			if (isset($this->aLangMsg[$sName])) {
				$sLang=$this->aLangMsg[$sName];
			} else {
				return $sName;
			}
		}
		/**
		 * Заменяем вхождение других ключей вида ___lang_key___
		 */
		$sLang=$this->ReplaceKey($sLang);

		if(is_array($aReplace)&&count($aReplace)&&is_string($sLang)) {
			foreach ($aReplace as $sFrom => $sTo) {
				$aReplacePairs["%%{$sFrom}%%"]=$sTo;
			}
			$sLang=strtr($sLang,$aReplacePairs);
		}

		if(Config::Get('module.lang.delete_undefined') and $bDelete and is_string($sLang)) {
			$sLang=preg_replace("/\%\%[\S]+\%\%/U",'',$sLang);
		}
		return $sLang;
	}
	/**
	 * Заменяет плейсхолдеры ключей в значениях текстовки
	 *
	 * @param string|array $msg	Значение текстовки
	 * @return array|mixed
	 */
	protected function ReplaceKey($msg) {
		if(is_array($msg)) {
			foreach($msg as $k=>$v) {
				$k_replaced = $this->ReplaceKey($k);
				if($k==$k_replaced) {
					$msg[$k] = $this->ReplaceKey($v);
				} else {
					$msg[$k_replaced] = $this->ReplaceKey($v);
					unset($msg[$k]);
				}
			}
		} else {
			if(preg_match_all('~___([\S|\.]+)___~Ui',$msg,$aMatch,PREG_SET_ORDER)) {
				foreach($aMatch as $aItem) {
					$msg=str_replace('___'.$aItem[1].'___',$this->Get($aItem[1]),$msg);
				}
			}
		}
		return $msg;
	}
	/**
	 * Добавить к текстовкам массив сообщений
	 *
	 * @param array $aMessages     Список текстовок для добавления
	 * @param array|null $aParams	Параметры, позволяют хранить текстовки в структурированном виде, например, тестовки плагина "test" получать как Get('plugin.name.test')
	 */
	public function AddMessages($aMessages, $aParams = null) {
		if (is_array($aMessages)) {
			if (isset($aParams['name'])) {
				$sMsgs=$aMessages;
				if (isset($aParams['category'])) {
					if (isset($this->aLangMsg[$aParams['category']][$aParams['name']])) {
						$sMsgs=func_array_merge_assoc($this->aLangMsg[$aParams['category']][$aParams['name']],$sMsgs);
					}
					$this->aLangMsg[$aParams['category']][$aParams['name']]=$sMsgs;
				} else {
					if (isset($this->aLangMsg[$aParams['name']])) {
						$sMsgs=func_array_merge_assoc($this->aLangMsg[$aParams['name']],$sMsgs);
					}
					$this->aLangMsg[$aParams['name']]=$sMsgs;
				}
			} else {
				$this->aLangMsg = func_array_merge_assoc($this->aLangMsg, $aMessages);
			}
		}
	}
	/**
	 * Добавить к текстовкам отдельное сообщение
	 *
	 * @param string $sKey	Имя текстовки
	 * @param string $sMessage	Значение текстовки
	 */
	public function AddMessage($sKey, $sMessage) {
		$this->aLangMsg[$sKey] = $sMessage;
	}
	/**
	 * Возвращает нужную форму слова во множественном числе
	 *
	 * @param int $iNumber
	 * @param string|array $mText
	 * @param string|null $sLang
	 *
	 * @return string|mixed
	 */
	public function Pluralize($iNumber,$mText,$sLang=null) {
		if (is_null($sLang)) {
			$sLang=$this->GetLang();
		}
		if ('pt_BR'===$sLang) {
			// temporary set a locale for brazilian
			$sLang='xbr';
		}

		if (strlen($sLang)>3) {
			$sLang=substr($sLang,0,-strlen(strrchr($sLang,'_')));
		}
		$iNumber=abs($iNumber);
		$iForm=0;
		/*
		 * The plural rules are derived from code of the Zend Framework (2010-09-25),
		 * which is subject to the new BSD license (http://framework.zend.com/license/new-bsd).
		 * Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
		 */
		switch ($sLang) {
			case 'bo':
			case 'dz':
			case 'id':
			case 'ja':
			case 'jv':
			case 'ka':
			case 'km':
			case 'kn':
			case 'ko':
			case 'ms':
			case 'th':
			case 'tr':
			case 'vi':
			case 'zh':
				$iForm=0;
				break;

			case 'af':
			case 'az':
			case 'bn':
			case 'bg':
			case 'ca':
			case 'da':
			case 'de':
			case 'el':
			case 'en':
			case 'eo':
			case 'es':
			case 'et':
			case 'eu':
			case 'fa':
			case 'fi':
			case 'fo':
			case 'fur':
			case 'fy':
			case 'gl':
			case 'gu':
			case 'ha':
			case 'he':
			case 'hu':
			case 'is':
			case 'it':
			case 'ku':
			case 'lb':
			case 'ml':
			case 'mn':
			case 'mr':
			case 'nah':
			case 'nb':
			case 'ne':
			case 'nl':
			case 'nn':
			case 'no':
			case 'om':
			case 'or':
			case 'pa':
			case 'pap':
			case 'ps':
			case 'pt':
			case 'so':
			case 'sq':
			case 'sv':
			case 'sw':
			case 'ta':
			case 'te':
			case 'tk':
			case 'ur':
			case 'zu':
				$iForm=($iNumber==1) ? 0 : 1;
				break;

			case 'am':
			case 'bh':
			case 'fil':
			case 'fr':
			case 'gun':
			case 'hi':
			case 'ln':
			case 'mg':
			case 'nso':
			case 'xbr':
			case 'ti':
			case 'wa':
				$iForm=(($iNumber==0) || ($iNumber==1)) ? 0 : 1;
				break;

			case 'be':
			case 'bs':
			case 'hr':
			case 'ru':
			case 'sr':
			case 'uk':
				$iForm=(($iNumber%10==1) && ($iNumber%100!=11)) ? 0 : ((($iNumber%10>=2) && ($iNumber%10<=4) && (($iNumber%100<10) || ($iNumber%100>=20))) ? 1 : 2);
				break;

			case 'cs':
			case 'sk':
				$iForm=($iNumber==1) ? 0 : ((($iNumber>=2) && ($iNumber<=4)) ? 1 : 2);
				break;

			case 'ga':
				$iForm=($iNumber==1) ? 0 : (($iNumber==2) ? 1 : 2);
				break;

			case 'lt':
				$iForm=(($iNumber%10==1) && ($iNumber%100!=11)) ? 0 : ((($iNumber%10>=2) && (($iNumber%100<10) || ($iNumber%100>=20))) ? 1 : 2);
				break;

			case 'sl':
				$iForm=($iNumber%100==1) ? 0 : (($iNumber%100==2) ? 1 : ((($iNumber%100==3) || ($iNumber%100==4)) ? 2 : 3));
				break;

			case 'mk':
				$iForm=($iNumber%10==1) ? 0 : 1;
				break;

			case 'mt':
				$iForm=($iNumber==1) ? 0 : ((($iNumber==0) || (($iNumber%100>1) && ($iNumber%100<11))) ? 1 : ((($iNumber%100>10) && ($iNumber%100<20)) ? 2 : 3));
				break;

			case 'lv':
				$iForm=($iNumber==0) ? 0 : ((($iNumber%10==1) && ($iNumber%100!=11)) ? 1 : 2);
				break;

			case 'pl':
				$iForm=($iNumber==1) ? 0 : ((($iNumber%10>=2) && ($iNumber%10<=4) && (($iNumber%100<12) || ($iNumber%100>14))) ? 1 : 2);
				break;

			case 'cy':
				$iForm=($iNumber==1) ? 0 : (($iNumber==2) ? 1 : ((($iNumber==8) || ($iNumber==11)) ? 2 : 3));
				break;

			case 'ro':
				$iForm=($iNumber==1) ? 0 : ((($iNumber==0) || (($iNumber%100>0) && ($iNumber%100<20))) ? 1 : 2);
				break;

			case 'ar':
				$iForm=($iNumber==0) ? 0 : (($iNumber==1) ? 1 : (($iNumber==2) ? 2 : ((($iNumber%100>=3) && ($iNumber%100<=10)) ? 3 : ((($iNumber%100>=11) && ($iNumber%100<=99)) ? 4 : 5))));
				break;

			default:
				$iForm=0;
		}
		if (is_array($mText)) {
			$aText=$mText;
		} else {
			$aText=explode(';',$mText);
		}
		/**
		 * Возвращаем нужную форму слова, либо исходный текст
		 */
		return array_key_exists($iForm,$aText) ? $aText[$iForm] : $mText;
	}
	/**
	 * Завершаем работу модуля
	 *
	 */
	public function Shutdown() {
		/**
		 * Делаем выгрузку необходимых текстовок в шаблон в виде js
		 */
		$this->AssignToJs();
	}
}