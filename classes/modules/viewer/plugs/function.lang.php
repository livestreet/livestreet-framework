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
 * Плагин для смарти
 * Выводит текстовку из языкового файла
 *
 * @param   array $aParams
 * @param   Smarty $oSmarty
 * @return  string
 */
function smarty_function_lang($aParams,&$oSmarty) {
	if (isset($aParams['_default_short'])) {
		$aParams['name']=$aParams['_default_short'];
	}
	if(empty($aParams['name'])) {
		trigger_error("Lang: missing 'name' parametr",E_USER_WARNING);
		return ;
	}

    $sName=$aParams['name'];
    $bPlural=(isset($aParams['plural']) && $aParams['plural']) ? true : false;
    /**
     * Получаем параметры для передачи в текстовку
     */
    $aReplace=array();
    if (isset($aParams['params']) and is_array($aParams['params'])) {
        $aReplace=$aParams['params'];
		if ($bPlural and isset($aParams['count'])) {
			$aReplace['count']=$aParams['count'];
		}
    } else {
        unset($aParams['name']);
        $aReplace=$aParams;
    }
    /**
     * Получаем текстовку
     */
    $sReturn=Engine::getInstance()->Lang_Get($sName,$aReplace);
	/**
	 * Если необходимо получить правильную форму множественного числа
	 */
	if ($bPlural and isset($aParams['count'])) {
		if ($aParams['count']==0 and isset($aParams['empty'])) {
			$sReturn=Engine::getInstance()->Lang_Get($aParams['empty']);
		} else {
			$sReturn=Engine::getInstance()->Lang_Pluralize((int)$aParams['count'],$sReturn);
		}
	}
    /**
     * Возвращаем результат
     */
    if (!empty($aParams['assign'])) {
        $oSmarty->assign($aParams['assign'], $sReturn);
    } else {
        return $sReturn;
    }
}