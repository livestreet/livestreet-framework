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
 * Модуль для работы с файловой системой
 * TODO: проверить работу под Windows
 *
 * @package framework.modules
 * @since 2.0
 */
class ModuleFs extends Module {
	/**
	 * Список типов файловых путей.
	 * Путь задается в виде "[web]http://site.com/image.jpg", [web] - префикс с типом пути
	 */
	const PATH_TYPE_WEB='web';
	/**
	 * Серверный путь [server]/home/webmaster/site.com/image.jpg
	 */
	const PATH_TYPE_SERVER='server';
	/**
	 * Относительный путь от корня сайта [relative]/image.jpg
	 */
	const PATH_TYPE_RELATIVE='relative';

	/**
	 * Дефолтный тип пути, используется если префикс не указан
	 *
	 * @var string|null
	 */
	protected $sPathTypeDefault=null;


	public function Init() {
		/**
		 * Определяем дефолтный тип
		 */
		$this->sPathTypeDefault=self::PATH_TYPE_SERVER;
	}
	/**
	 * Формирует полный путь с учетом типа
	 *
	 * @param string $sPath
	 * @param string $sType
	 *
	 * @return string
	 */
	public function MakePath($sPath,$sType) {
		return '['.$sType.']'.$sPath;
	}
	/**
	 * Возвращает серверный путь
	 *
	 * @param string $sPath	Исходный путь с префиксом, например, [relative]/image.jpg
	 * @param bool $bWithType
	 *
	 * @return string
	 */
	public function GetPathServer($sPath,$bWithType=false) {
		list($sType,$sPath)=$this->GetParsedPath($sPath);
		if ($sType!=self::PATH_TYPE_SERVER) {
			/**
			 * Пробуем вызвать метод GetPathServerFrom[Type]()
			 */
			$sMethod='GetPathServerFrom'.func_camelize($sType);
			if (method_exists($this,$sMethod)) {
				$sPath=$this->$sMethod($sPath);
			}
		}
		if ($bWithType) {
			$sPath=$this->MakePath($sPath,self::PATH_TYPE_SERVER);
		}
		return $sPath;
 	}
	/**
	 * Возвращает веб путь (URL)
	 *
	 * @param string $sPath	Исходный путь с префиксом, например, [relative]/image.jpg
	 * @param bool $bWithType
	 *
	 * @return string
	 */
	public function GetPathWeb($sPath,$bWithType=false) {
		list($sType,$sPath)=$this->GetParsedPath($sPath);
		if ($sType!=self::PATH_TYPE_WEB) {
			/**
			 * Пробуем вызвать метод GetPathWebFrom[Type]()
			 */
			$sMethod='GetPathWebFrom'.func_camelize($sType);
			if (method_exists($this,$sMethod)) {
				$sPath=$this->$sMethod($sPath);
			}
		}
		if ($bWithType) {
			$sPath=$this->MakePath($sPath,self::PATH_TYPE_WEB);
		}
		return $sPath;
	}
	/**
	 * Возвращает относительный путь
	 *
	 * @param string $sPath	Исходный путь с префиксом, например, [server]/home/webmaster/site.com/image.jpg
	 * @param bool $bWithType
	 *
	 * @return string
	 */
	public function GetPathRelative($sPath,$bWithType=false) {
		list($sType,$sPath)=$this->GetParsedPath($sPath);
		if ($sType!=self::PATH_TYPE_RELATIVE) {
			/**
			 * Пробуем вызвать метод GetPathRelativeFrom[Type]()
			 */
			$sMethod='GetPathRelativeFrom'.func_camelize($sType);
			if (method_exists($this,$sMethod)) {
				$sPath=$this->$sMethod($sPath);
			}
		}
		if ($bWithType) {
			$sPath=$this->MakePath($sPath,self::PATH_TYPE_RELATIVE);
		}
		return $sPath;
	}
	/**
	 * Возвращает серверный путь из веб
	 *
	 * @param string $sPath
	 *
	 * @return string
	 */
	public function GetPathServerFromWeb($sPath) {
		/**
		 * Определяем, принадлежит ли этот адрес основному домену
		 */
		if(parse_url($sPath,PHP_URL_HOST)!=parse_url(Router::GetPathRootWeb(),PHP_URL_HOST)) {
			return $sPath;
		}
		/**
		 * Выделяем адрес пути
		 */
		$sPath = ltrim(parse_url($sPath,PHP_URL_PATH),'/');
		if($iOffset = Config::Get('path.offset_request_url')){
			$sPath = preg_replace('#^([^/]+/*){'.$iOffset.'}#msi', '', $sPath);
		}
		return rtrim(Config::Get('path.root.server'),'/').'/'.$sPath;
	}
	/**
	 * Возвращает относительный путь из серверного
	 *
	 * @param string $sPath
	 *
	 * @return string
	 */
	public function GetPathRelativeFromServer($sPath) {
		$sServerPath = rtrim(str_replace(DIRECTORY_SEPARATOR,'/',Config::Get('path.root.server')),'/');
		return str_replace($sServerPath, '', str_replace(DIRECTORY_SEPARATOR,'/',$sPath));
	}
	/**
	 * Возвращает относительный путь из веб
	 *
	 * @param string $sPath
	 *
	 * @return string
	 */
	public function GetPathRelativeFromWeb($sPath) {
		$sPath=ltrim(parse_url($sPath,PHP_URL_PATH),'/');
		if($iOffset=Config::Get('path.offset_request_url')){
			$sPath=preg_replace('#^([^/]+/*){'.$iOffset.'}#msi', '', $sPath);
		}
		return '/'.$sPath;
	}
	/**
	 * Возвращает веб путь из серверного
	 *
	 * @param string $sPath
	 *
	 * @return string
	 */
	public function GetPathWebFromServer($sPath) {
		$sServerPath=rtrim(str_replace(DIRECTORY_SEPARATOR,'/',Config::Get('path.root.server')),'/');
		$sWebPath=Router::GetPathRootWeb();
		return str_replace($sServerPath, $sWebPath, str_replace(DIRECTORY_SEPARATOR,'/',$sPath));
	}
	/**
	 * Возвращает серверный путь из относительного
	 *
	 * @param string $sPath
	 *
	 * @return string
	 */
	public function GetPathServerFromRelative($sPath) {
		return rtrim(Config::Get('path.root.server'),'/').'/'.ltrim($sPath,'/');
	}
	/**
	 * Возвращает веб путь из относительного
	 *
	 * @param string $sPath
	 *
	 * @return string
	 */
	public function GetPathWebFromRelative($sPath) {
		return Router::GetPathRootWeb().'/'.ltrim($sPath,'/');
	}
	/**
	 * Проверяет принадлежность пути нужному типу
	 *
	 * @param string $sPath	Исходный путь с префиксом, например, [relative]/image.jpg
	 * @param string $sType
	 *
	 * @return bool
	 */
	public function IsPathType($sPath,$sType) {
		return $this->GetPathType($sPath)==$sType;
	}
	/**
	 * Возвращает тип из пути
	 *
	 * @param string $sPath	Исходный путь с префиксом, например, [relative]/image.jpg
	 *
	 * @return mixed
	 */
	public function GetPathType($sPath) {
		list($sType,)=$this->GetParsedPath($sPath);
		return $sType;
	}
	/**
	 * Парсит путь и возвращет его составляющие - массив вида array(0=>'relative', 1=>'/image.jpg')
	 *
	 * @param string $sPath	Исходный путь с префиксом, например, [relative]/image.jpg
	 *
	 * @return array
	 */
	public function GetParsedPath($sPath) {
		if (preg_match("#^\[([a-z_0-9]*)\](.*)$#i",$sPath,$aMatch)) {
			return array($aMatch[1] ? $aMatch[1] : self::PATH_TYPE_SERVER,$aMatch[2]);
		}
		return array(self::PATH_TYPE_SERVER,$sPath);
	}
	/**
	 * Сохраняет(копирует) файл на локальном(текущем) сервере
	 *
	 * @param string $sFileSource	Полный путь до исходного файла
	 * @param string $sFileDest	Полный путь до файла для сохранения
	 * @param int|null $iMode	Права chmod для файла, например, 0777
	 * @param bool $bRemoveSource	Удалять исходный файл или нет
	 * @return bool
	 */
	public function SaveFileLocal($sFileSource,$sFileDest,$iMode=null,$bRemoveSource=false) {
		$bResult=copy($sFileSource,$sFileDest);
		if ($bResult and !is_null($iMode)) {
			chmod($sFileDest,$iMode);
		}
		if ($bRemoveSource) {
			unlink($sFileSource);
		}
		return $bResult;
	}
	/**
	 * Сохраняет(копирует) файл на локальном(текущем) сервере
	 * Основное отличие от SaveLocal() в том, что здесь для указания целевого файла используется относительный каталог (если он не существует, то создается автоматически)
	 * И в случае успешного копирования метод возвращает путь до целевого файла
	 *
	 * @param string $sFileSource	Полный путь до исходного файла
	 * @param string $sDirDest	Каталог для сохранения файла относительно корня сайта
	 * @param string $sFileDest	Имя файла для сохранения
	 * @param int|null $iMode	Права chmod для файла, например, 0777
	 * @param bool $bRemoveSource	Удалять исходный файл или нет
	 * @return bool | string	При успешном сохранении возвращает полный серверный путь до файла
	 */
	public function SaveFileLocalSmart($sFileSource,$sDirDest,$sFileDest,$iMode=null,$bRemoveSource=false) {
		$sFileDestFullPath=rtrim(Config::Get('path.root.server'),"/").'/'.trim($sDirDest,"/").'/'.$sFileDest;
		$this->CreateDirectoryLocalSmart($sDirDest);

		if ($this->SaveFileLocal($sFileSource,$sFileDestFullPath,$iMode,$bRemoveSource)) {
			return $sFileDestFullPath;
		}
		return false;
	}
	/**
	 * Создает каталог на локальном(текущем) сервере
	 *
	 * @param string $sDirDest	Полный путь до каталога
	 */
	public function CreateDirectoryLocal($sDirDest) {
		if (!is_dir ($sDirDest)) {
			@mkdir($sDirDest,0755,true);
		}
	}
	/**
	 * Создает каталог на локальном(текущем) сервере
	 * Отличие от CreateDirectoryLocal() в том, что здесь используется каталог относительно корня сайта
	 *
	 * @param string $sDirDest	Каталог относительно корня сайта
	 */
	public function CreateDirectoryLocalSmart($sDirDest) {
		$this->CreateDirectoryLocal(rtrim(Config::Get('path.root.server'),'/').'/'.ltrim($sDirDest,'/'));
	}
	/**
	 * Удаляет локальный файл
	 *
	 * @param string $sFile
	 *
	 * @return bool
	 */
	public function RemoveFileLocal($sFile) {
		if (file_exists($sFile)) {
			return @unlink($sFile);
		}
		return false;
	}
	/**
	 * Проверяет на существование локальный файл
	 *
	 * @param string $sFile
	 *
	 * @return bool
	 */
	public function IsExistsFileLocal($sFile) {
		return file_exists($sFile);
	}
	/**
	 * Проверяет наличие блокировки
	 *
	 * @param resource $hDescriptor	Дексриптор открытого файла для блокировки
	 *
	 * @return bool
	 */
	public function IsLock($hDescriptor) {
		if (!$hDescriptor) {
			return false;
		}
		return !$this->CreateLock($hDescriptor);
	}
	/**
	 * Создает блокировку
	 *
	 * @param resource $hDescriptor Дексриптор открытого файла для блокировки
	 *
	 * @return bool
	 */
	public function CreateLock($hDescriptor) {
		return flock($hDescriptor,LOCK_EX|LOCK_NB);
	}
	/**
	 * Удаляет блокировку
	 *
	 * @param resource $hDescriptor Дексриптор открытого файла для блокировки
	 *
	 * @return bool
	 */
	public function RemoveLock($hDescriptor) {
		return ($hDescriptor && @flock($hDescriptor,LOCK_UN));
	}
}