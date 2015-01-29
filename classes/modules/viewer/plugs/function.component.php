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
 * Плагин для смарти
 * Подключает шаблон компонента
 *
 * @param   array $aParams
 * @param   Smarty $oSmarty
 * @return  string
 */
function smarty_function_component($aParams, &$oSmarty)
{
    if (isset($aParams['_default_short'])) {
        $aParams['component'] = $aParams['_default_short'];
    }
    if (empty($aParams['component'])) {
        trigger_error("Config: missing 'component' parametr", E_USER_WARNING);
        return;
    }
    $sName = $aParams['component'];
    $sTemplate = null;
    if (isset($aParams['template'])) {
        $sTemplate = $aParams['template'];
    }
    /**
     * Получаем параметры компонента
     */
    $aComponentParams = array();

    if (isset($aParams['params']) and is_array($aParams['params'])) {
        $aComponentParams = array_merge($aParams['params'], $aParams);
    } else {
        $aComponentParams = $aParams;
    }

    unset($aComponentParams['_default_short']);
    unset($aComponentParams['component']);
    unset($aComponentParams['template']);
    unset($aComponentParams['params']);

    $aComponentParams['params'] = $aComponentParams;

    /**
     * Получаем путь до шаблона
     */
    if ($sPathTemplate = Engine::getInstance()->Component_GetTemplatePath($sName,
            $sTemplate) and Engine::getInstance()->Viewer_TemplateExists($sPathTemplate)
    ) {
        $sResult = $oSmarty->getSubTemplate($sPathTemplate, $oSmarty->cache_id, $oSmarty->compile_id, null, null,
            $aComponentParams, Smarty::SCOPE_LOCAL);
    } else {
        $sResult = 'Component template not found: ' . $sName . '/' . ($sTemplate ? $sTemplate : $sName) . '.tpl';
    }

    if (!empty($aParams['assign'])) {
        $oSmarty->assign($aParams['assign'], $sResult);
    } else {
        return $sResult;
    }

    return '';
}