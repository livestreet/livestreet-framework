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
 * Тип JS скриптов
 *
 * @package framework.modules
 * @since 2.0
 */
class ModuleAsset_EntityTypeJs extends ModuleAsset_EntityType
{
    /**
     * Производит предварительную обработку содержимого
     *
     */
    public function prepare()
    {
        $this->setContent(
            rtrim($this->getContent(), ";") . ";" . PHP_EOL
        );
    }

    /**
     * Выполняет сжатие
     *
     * @return mixed|void
     */
    public function compress()
    {
        try {
            $this->setContent(\JShrink\Minifier::minify($this->getContent()));
        } catch (Exception $e) {
            $this->Logger_Warning('Asset js compress: ' . $e->getMessage());
        }
    }

    /**
     * Возвращает HTML обертку для файла
     *
     * @param $sFile
     * @param $aParams
     *
     * @return string
     */
    public function getHeadHtml($sFile, $aParams)
    {
        $sDefer = (isset($aParams['defer']) and $aParams['defer']) ? ' defer ' : '';
        $sAsync = (isset($aParams['async']) and $aParams['async']) ? ' async ' : '';
        $sHtml = '<script type="text/javascript" src="' . $sFile . '" ' . $sDefer . $sAsync . '></script>';
        return $this->wrapForBrowser($sHtml, $aParams);
    }
}