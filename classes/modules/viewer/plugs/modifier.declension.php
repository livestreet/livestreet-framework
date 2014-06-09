<?php
/**
 * Smarty plugin - declension modifier
 * @package Smarty
 * @subpackage plugins
 */

/**
 * Модификатор declension: склонение существительных в зависимости от множественного числа
 *
 * @param      $iCount
 * @param      $mForms
 * @param null $sLang
 *
 * @return mixed
 */
function smarty_modifier_declension($iCount,$mForms,$sLang=null) {
	return Engine::getInstance()->Lang_Pluralize($iCount,$mForms,$sLang);
}