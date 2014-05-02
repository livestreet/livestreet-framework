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
 * @author Maxim Mzhelskiy <rus.engine@gmail.com>
 *
 */

/**
 * Модуль логирования
 * В качестве бекенда используется библиотека Monolog
 * <pre>
 * $this->Logger_Debug('Debug message');
 * </pre>
 *
 * @package engine.modules
 * @since 1.0
 */
class ModuleLogger extends Module {
	/**
	 * Список инстанций логов, в качестве ключей используются названия инстансов
	 *
	 * @var array
	 */
	protected $aInstances=array();
	/**
	 * Список уровней логирования
	 *
	 * @var array
	 */
	protected $aLevels = array(
		'debug',
		'info',
		'notice',
		'warning',
		'error',
		'critical',
		'alert',
		'emergency',
	);

	public function Init() {

	}
	/**
	 * Возвращает инстанс по имени
	 *
	 * @param string $sInstance
	 *
	 * @return mixed
	 * @throws Exception
	 */
	public function GetInstance($sInstance) {
		if (!isset($this->aInstances[$sInstance])) {
			/**
			 * Создаем новый инстанс
			 */
			$aConfig=Config::Get('sys.logs.instances.'.$sInstance);
			if (!$aConfig) {
				throw new Exception("Log instance '{$sInstance}' not available");
			}
			$oInstance=new \Monolog\Logger($sInstance);
			/**
			 * Список обработчиков
			 */
			foreach($aConfig['handlers'] as $sHandler=>$aParams) {
				/**
				 * Формируем список параметром инициализации хендлера, учитываем только числовые ключи
				 */
				$aParamsInit=array();
				foreach($aParams as $k=>$v) {
					if (is_int($k)) {
						if (in_array($v,$this->aLevels)) {
							$v=$this->ConvertLevel($v);
						}
						$aParamsInit[]=$v;
					}
				}
				$sHandler=ucfirst($sHandler);
				$oRefClass=new ReflectionClass("Monolog\\Handler\\{$sHandler}Handler");
				$oHandler=$oRefClass->newInstanceArgs($aParamsInit);
				$oInstance->pushHandler($oHandler);
				/**
				 * Устанавливаем формат логов
				 */
				if (isset($aParams['formatter'])) {
					$aFormatter=$aParams['formatter'];
					$sFormatterName=ucfirst(array_shift($aFormatter));
					$oRefClass=new ReflectionClass("Monolog\\Formatter\\{$sFormatterName}Formatter");
					$oFormatter=$oRefClass->newInstanceArgs($aFormatter);
					$oHandler->setFormatter($oFormatter);
				}
			}
			/**
			 * Список пре-обработчиков
			 */
			foreach($aConfig['processors'] as $sProcessors=>$aParams) {
				if (is_int($sProcessors)) {
					$sProcessors=$aParams;
					$aParams=array();
				}
				$sProcessors=ucfirst($sProcessors);
				$oRefClass=new ReflectionClass("Monolog\\Processor\\{$sProcessors}Processor");
				$oProcessors=$oRefClass->newInstanceArgs($aParams);
				$oInstance->pushProcessor($oProcessors);
			}
			$this->aInstances[$sInstance]=$oInstance;
		}
		return $this->aInstances[$sInstance];
	}
	/**
	 * Добавляет запись в лог
	 *
	 * @param string|int $sLevel	Уровень логирования
	 * @param string  $sMsg	Сообщение
	 * @param array  $aContext	Дополнительные параметры для логирования
	 * @param string $sInstance	Имя инстанса
	 */
	public function Write($sLevel,$sMsg,$aContext=array(),$sInstance='default') {
		$oInstance=$this->GetInstance($sInstance);
		$oInstance->log($sLevel,$sMsg,$aContext);
	}
	/**
	 * Логирует с уровнем Debug
	 *
	 * @param string  $sMsg	Сообщение
	 * @param array  $aContext	Дополнительные параметры для логирования
	 * @param string $sInstance	Имя инстанса
	 */
	public function Debug($sMsg,$aContext=array(),$sInstance='default') {
		return $this->Write(strtolower(__FUNCTION__),$sMsg,$aContext,$sInstance);
	}
	/**
	 * Логирует с уровнем Info
	 *
	 * @param string  $sMsg	Сообщение
	 * @param array  $aContext	Дополнительные параметры для логирования
	 * @param string $sInstance	Имя инстанса
	 */
	public function Info($sMsg,$aContext=array(),$sInstance='default') {
		return $this->Write(strtolower(__FUNCTION__),$sMsg,$aContext,$sInstance);
	}
	/**
	 * Логирует с уровнем Notice
	 *
	 * @param string  $sMsg	Сообщение
	 * @param array  $aContext	Дополнительные параметры для логирования
	 * @param string $sInstance	Имя инстанса
	 */
	public function Notice($sMsg,$aContext=array(),$sInstance='default') {
		return $this->Write(strtolower(__FUNCTION__),$sMsg,$aContext,$sInstance);
	}
	/**
	 * Логирует с уровнем Warning
	 *
	 * @param string  $sMsg	Сообщение
	 * @param array  $aContext	Дополнительные параметры для логирования
	 * @param string $sInstance	Имя инстанса
	 */
	public function Warning($sMsg,$aContext=array(),$sInstance='default') {
		return $this->Write(strtolower(__FUNCTION__),$sMsg,$aContext,$sInstance);
	}
	/**
	 * Логирует с уровнем Error
	 *
	 * @param string  $sMsg	Сообщение
	 * @param array  $aContext	Дополнительные параметры для логирования
	 * @param string $sInstance	Имя инстанса
	 */
	public function Error($sMsg,$aContext=array(),$sInstance='default') {
		return $this->Write(strtolower(__FUNCTION__),$sMsg,$aContext,$sInstance);
	}
	/**
	 * Логирует с уровнем Critical
	 *
	 * @param string  $sMsg	Сообщение
	 * @param array  $aContext	Дополнительные параметры для логирования
	 * @param string $sInstance	Имя инстанса
	 */
	public function Critical($sMsg,$aContext=array(),$sInstance='default') {
		return $this->Write(strtolower(__FUNCTION__),$sMsg,$aContext,$sInstance);
	}
	/**
	 * Логирует с уровнем Alert
	 *
	 * @param string  $sMsg	Сообщение
	 * @param array  $aContext	Дополнительные параметры для логирования
	 * @param string $sInstance	Имя инстанса
	 */
	public function Alert($sMsg,$aContext=array(),$sInstance='default') {
		return $this->Write(strtolower(__FUNCTION__),$sMsg,$aContext,$sInstance);
	}
	/**
	 * Логирует с уровнем Emergency
	 *
	 * @param string  $sMsg	Сообщение
	 * @param array  $aContext	Дополнительные параметры для логирования
	 * @param string $sInstance	Имя инстанса
	 */
	public function Emergency($sMsg,$aContext=array(),$sInstance='default') {
		return $this->Write(strtolower(__FUNCTION__),$sMsg,$aContext,$sInstance);
	}
	/**
	 * Конвертирует уровень в сзначине библиотеки Monolog
	 *
	 * @param string $sLevel
	 *
	 * @return int
	 * @throws Exception
	 */
	protected function ConvertLevel($sLevel) {
		switch ($sLevel) {
			case 'debug':
				return Monolog\Logger::DEBUG;

			case 'info':
				return Monolog\Logger::INFO;

			case 'notice':
				return Monolog\Logger::NOTICE;

			case 'warning':
				return Monolog\Logger::WARNING;

			case 'error':
				return Monolog\Logger::ERROR;

			case 'critical':
				return Monolog\Logger::CRITICAL;

			case 'alert':
				return Monolog\Logger::ALERT;

			case 'emergency':
				return Monolog\Logger::EMERGENCY;

			default:
				throw new Exception("Invalid log level: {$sLevel}");
		}
	}
}