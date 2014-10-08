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
 * Абстрактный класс типа assets, от него должны наследоваться все конечные типы
 *
 * @package framework.modules
 * @since 2.0
 */
abstract class ModuleAsset_EntityType extends Entity
{
    /**
     * Производит предварительную обработку содержимого
     *
     */
    abstract public function prepare();

    /**
     * Выполняет сжатие содержимого
     *
     * @return mixed
     */
    abstract public function compress();

    /**
     * Возвращает HTML обертку для файла
     *
     * @param $sFile
     * @param $aParams
     *
     * @return string
     */
    abstract public function getHeadHtml($sFile, $aParams);

    /**
     * Возвращает контент
     *
     * @return string|null
     */
    public function getContent()
    {
        return $this->_getDataOne('content');
    }

    /**
     * Устанавливает контент
     *
     * @param string $sContent
     */
    public function setContent($sContent)
    {
        $this->_aData['content'] = $sContent;
    }

    /**
     * Возвращает исходный файл
     *
     * @return string|null
     */
    public function getFile()
    {
        return $this->_getDataOne('file');
    }

    /**
     * Устанавливает исходный файл
     *
     * @param string $sContent
     */
    public function setFile($sFile)
    {
        $this->_aData['file'] = $sFile;
    }

    /**
     * Оборачивает HTML в зависимости от условия по браузеру
     *
     * @param $sHtml
     * @param $aParams
     *
     * @return string
     */
    public function wrapForBrowser($sHtml, $aParams)
    {
        if (isset($aParams['browser']) and $aParams['browser']) {
            return "<!--[if {$aParams['browser']}]>$sHtml<![endif]-->";
        }
        return $sHtml;
    }
}