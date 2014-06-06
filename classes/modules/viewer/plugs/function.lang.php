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
	if(empty($aParams['name'])) {
		trigger_error("Lang: missing 'name' parametr",E_USER_WARNING);
		return ;
	}

    $sName=$aParams['name'];
    /**
     * Получаем параметры для передачи в текстовку
     */
    $aReplace=array();
    if (isset($aParams['params']) and is_array($aParams['params'])) {
        $aReplace=$aParams['params'];
    } else {
        unset($aParams['name']);
        $aReplace=$aParams;
    }
    /**
     * Получаем текстовку
     */
    $sReturn=Engine::getInstance()->Lang_Get($sName,$aReplace);
    /**
     * Возвращаем результат
     */
    if (!empty($aParams['assign'])) {
        $oSmarty->assign($aParams['assign'], $sReturn);
    } else {
        return $sReturn;
    }
}