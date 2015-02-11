<?php
/*
 * LiveStreet CMS
 * Copyright © 2015 OOO "ЛС-СОФТ"
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
 * @copyright 2015 OOO "ЛС-СОФТ"
 * @author Maxim Mzhelskiy <rus.engine@gmail.com>
 *
 */

/**
 * Сущность для хранения и вывода данных Open Graph - специальный контент для социальных сетей
 *
 * @package framework.modules.viewer
 * @since 2.0
 */
class ModuleViewer_EntityOpenGraph extends Entity
{
    /**
     * Устанавливает параметр
     *
     * @param $sName
     * @param $mValue
     */
    public function setProperty($sName, $mValue)
    {
        $this->_aData['og'][$sName] = $mValue;
    }

    /**
     * Добавляет параметр
     * Если параметр уже есть, то он преобразуется в массив
     *
     * @param $sName
     * @param $mValue
     */
    public function addProperty($sName, $mValue)
    {
        $mValueCurrent = $this->getProperty($sName);
        if ($mValueCurrent) {
            if (!is_array($mValueCurrent)) {
                $mValueCurrent = array($mValueCurrent);
            }
            if (is_array($mValue)) {
                $mValueCurrent = array_merge($mValueCurrent, $mValue);
            } else {
                $mValueCurrent[] = $mValue;
            }
        } else {
            $mValueCurrent = $mValue;
        }
        $this->setProperty($sName, $mValueCurrent);
    }

    /**
     * Возвращает параметр
     *
     * @param $sName
     * @return null
     */
    public function getProperty($sName)
    {
        if (isset($this->_aData['og'][$sName])) {
            return $this->_aData['og'][$sName];
        }
        return null;
    }

    /**
     * Загружает данные Open Graph из другого объекта
     *
     * @param $oOpenGraph
     */
    public function loadFromOpenGraphObject($oOpenGraph)
    {
        $this->_aData['og'] = $oOpenGraph->_getDataOne('og');
    }


    /**
     * Возвращает HTML код для всех параметров
     *
     * @return bool|string
     */
    public function render()
    {
        $aOg = $this->_getDataOne('og');
        if (!$aOg or !is_array($aOg)) {
            $aOg = array();
        }

        /**
         * Определяем дефолтные параметры
         */
        $aDefault = array_diff_key($this->getDefaultProperties(), $aOg);
        $aOg = array_merge($aDefault, $aOg);

        $sHtml = '';
        foreach ($aOg as $sName => $aValue) {
            if (!is_array($aValue)) {
                $aValue = array($aValue);
            }
            foreach ($aValue as $sValue) {
                if (is_scalar($sValue) and $sValue) {
                    $sHtml .= $this->renderProperty($sName, $sValue) . "\n";
                }
            }
        }
        return $sHtml;
    }

    /**
     * Возвращает HTML код для конкретного параметра
     *
     * @param $sName
     * @param $sValue
     * @return string
     */
    protected function renderProperty($sName, $sValue)
    {
        return '<meta property="' . htmlspecialchars($sName) . '" content="' . htmlspecialchars($sValue) . '" />';
    }

    /**
     * Возвращает список дефолтных параметров
     *
     * @return array
     */
    protected function getDefaultProperties()
    {
        if (file_exists(Config::Get('path.smarty.template') . DIRECTORY_SEPARATOR . 'og-default.png')) {
            $sImage = Config::Get('path.skin.web') . '/og-default.png';
        } else {
            $sImage = Config::Get('path.framework.web') . '/frontend/common/images/ls-logo.png';
        }

        return array(
            'og:title'     => $this->Viewer_GetHtmlTitle(),
            'og:type'      => 'website',
            'og:image'     => $sImage,
            'og:url'       => Router::GetPathWebCurrent(),
            'og:site_name' => Config::Get('view.name'),
        );
    }
}