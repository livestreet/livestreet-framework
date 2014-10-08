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
 * CDateValidator class file.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @link http://www.yiiframework.com/
 * @copyright Copyright &copy; 2008-2011 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

/**
 * Валидатор даты
 * Валидатор использует внешний класс DateTimeParser
 *
 * @package framework.modules.validate
 * @since 1.0
 */
class ModuleValidate_EntityValidatorDate extends ModuleValidate_EntityValidator {
	/**
	 * Формат допустимой даты, может содержать список форматов в массиве
	 *
	 * @var string|array
	 */
	public $format='yyyy-MM-dd';
	/**
	 * Допускать или нет пустое значение
	 *
	 * @var bool
	 */
	public $allowEmpty=true;

	/**
	 * Запуск валидации
	 *
	 * @param mixed $sValue	Данные для валидации
	 *
	 * @return bool|string
	 */
	public function validate($sValue) {
		if (is_array($sValue)) {
			return $this->getMessage($this->Lang_Get('validate_date_format_invalid',null,false),'msg');
		}
		if($this->allowEmpty && $this->isEmpty($sValue)) {
			return true;
		}

		require_once(Config::Get('path.framework.libs_vendor.server').'/DateTime/DateTimeParser.php');

		$aFormats=is_string($this->format) ? array($this->format) : $this->format;
		$bValid=false;
		foreach($aFormats as $sFormat) {
			$iTimestamp=DateTimeParser::parse($sValue,$sFormat,array('month'=>1,'day'=>1,'hour'=>0,'minute'=>0,'second'=>0));
			if($iTimestamp!==false) {
				$bValid=true;
				break;
			}
		}

		if(!$bValid) {
			return $this->getMessage($this->Lang_Get('validate.date.format_invalid',null,false),'msg');
		}
		return true;
	}
}