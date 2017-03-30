<?php

/**
 * Модуль для управления sitemap сайта
 */
class ModuleSitemap extends Module
{

    /**
     * Список зарегистрированных типов
     *
     * @var array
     */
    protected $aTargetTypes = array(/*
        'topics' => array(
            'callback_counters' => null, // коллбэк для возврата числа страниц
            'callback_links'    => null, // коллбэк для возврата списка ссылок для индексного файла
            'callback_data'     => null, // коллбэк для возврата ссылок для конкретной страницы, в параметрах передается номер страницы
            'cache_lifetime'    => 60*60*24 // время кеширования данных, при false кеширования не будет
        )
        */
    );

    public function Init()
    {

    }

    /**
     * Возвращает типов объектов
     *
     * @param bool|false $bOnlyTypes
     * @return array
     */
    public function GetTargetTypes($bOnlyTypes = false)
    {
        return $bOnlyTypes ? array_keys($this->aTargetTypes) : $this->aTargetTypes;
    }

    /**
     * Добавляет в разрешенные новый тип
     *
     * @param string $sTargetType Тип
     * @param array $aParams Параметры
     * @param bool|false $bRewrite
     * @return bool
     */
    public function AddTargetType($sTargetType, $aParams = array(), $bRewrite = false)
    {
        if ($bRewrite or !array_key_exists($sTargetType, $this->aTargetTypes)) {
            $this->aTargetTypes[$sTargetType] = $aParams;
            return true;
        }
        return false;
    }

    /**
     * Проверяет разрешен ли данный тип
     *
     * @param string $sTargetType Тип
     * @return bool
     */
    public function IsAllowTargetType($sTargetType)
    {
        return is_string($sTargetType) && array_key_exists($sTargetType, $this->aTargetTypes);
    }

    /**
     * Возвращает параметры нужного типа
     *
     * @param string $sTargetType
     *
     * @return array|null
     */
    public function GetTargetTypeParams($sTargetType)
    {
        if ($this->IsAllowTargetType($sTargetType)) {
            return $this->aTargetTypes[$sTargetType];
        }
        return null;
    }

    /**
     * Возвращает конкретный параметр нужного типа
     *
     * @param string $sTargetType
     * @param string $sName
     *
     * @return mixed|null
     */
    public function GetTargetTypeParam($sTargetType, $sName)
    {
        $aParams = $this->GetTargetTypeParams($sTargetType);
        if ($aParams and array_key_exists($sName, $aParams)) {
            return $aParams[$sName];
        }
        return null;
    }

    /**
     * Формирует sitemap
     *
     * @return string|null
     */
    public function ShowSitemap()
    {
        header("Content-type: application/xml");
        $sTarget = Router::GetActionEvent();

        $sTemplateDir = Config::Get('path.framework.server') . '/frontend/common/sitemap/';
        $sTemplateDirWeb = Config::Get('path.framework.web') . '/frontend/common/sitemap/';
        if ($sTarget and $this->IsAllowTargetType($sTarget)) {
            /**
             * Показываем ссылки для типа
             */
            $iPage = (int)Router::GetParam(0) ?: 1;
            $iPage = $iPage > 0 ? $iPage : 1;

            $aData = array();
            $fCallbackData = $this->GetTargetTypeParam($sTarget, 'callback_data');
            if (is_callable($fCallbackData)) {
                $sCacheKey = "sitemap_type_{$sTarget}_data_{$iPage}";
                $iLifetime = $this->GetTargetTypeParam($sTarget, 'cache_lifetime');
                if (is_null($iLifetime)) {
                    $iLifetime = 60 * 60 * 1;
                }

                if ($iLifetime === false or false === ($aData = $this->Cache_Get($sCacheKey))) {
                    $aData = call_user_func($fCallbackData, $iPage);
                    if ($iLifetime !== false) {
                        $this->Cache_Set($aData, $sCacheKey, array("sitemap_data"), $iLifetime);
                    }
                }
            }
            $this->Viewer_Assign('aData', $aData);
            $this->Viewer_Assign('sTemplateDirWeb', $sTemplateDirWeb);
            return $this->Viewer_Fetch($sTemplateDir . 'sitemap.tpl');
        } else {
            /**
             * Выводим индексный файл
             */
            $aData = array();
            $aTypes = $this->GetTargetTypes(true);
            foreach ($aTypes as $sType) {
                $fCallback = $this->GetTargetTypeParam($sType, 'callback_counters');
                if (is_callable($fCallback) and $iPage = call_user_func($fCallback)) {
                    for ($i = 1; $i <= $iPage; ++$i) {
                        $aData[] = array(
                            'loc' => Router::GetPath('/') . 'sitemap_' . $sType . '_' . $i . '.xml'
                        );
                    }
                }
                $fCallback = $this->GetTargetTypeParam($sType, 'callback_links');
                if (is_callable($fCallback) and $aUrls = call_user_func($fCallback)) {
                    foreach ($aUrls as $sUrl) {
                        $aData[] = array(
                            'loc' => $sUrl
                        );
                    }
                }
            }
            $this->Viewer_Assign('aData', $aData);
            $this->Viewer_Assign('sTemplateDirWeb', $sTemplateDirWeb);
            return $this->Viewer_Fetch($sTemplateDir . 'index.tpl');
        }
        return null;
    }

    /**
     * Конвертирует дату в формат W3C Datetime
     *
     * @param mixed $mDate - UNIX timestamp или дата в формате понимаемом функцией strtotime()
     * @return string - дата в формате W3C Datetime (http://www.w3.org/TR/NOTE-datetime)
     */
    private function ConvertDateToLastMod($mDate = null)
    {
        if (is_null($mDate)) {
            return null;
        }

        $mDate = is_int($mDate) ? $mDate : strtotime($mDate);
        return date('Y-m-d\TH:i:s+00:00', $mDate);
    }

    /**
     * Возвращает массив с данными для генерации sitemap'а
     *
     * @param string $sUrl
     * @param mixed $sLastMod
     * @param mixed $sChangeFreq
     * @param mixed $sPriority
     * @return array
     */
    public function GetDataForSitemapRow($sUrl, $sLastMod = null, $sChangeFreq = null, $sPriority = null)
    {
        return array(
            'loc'        => $sUrl,
            'lastmod'    => $this->ConvertDateToLastMod($sLastMod),
            'priority'   => $sChangeFreq,
            'changefreq' => $sPriority,
        );
    }
}