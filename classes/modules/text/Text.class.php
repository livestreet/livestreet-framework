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

require_once(Config::Get('path.framework.libs_vendor.server') . '/Jevix/jevix.class.php');

/**
 * Модуль обработки текста на основе типографа Jevix
 * Позволяет вырезать из текста лишние HTML теги и предотвращает различные попытки внедрить в текст JavaScript
 * <pre>
 * $sText=$this->Text_Parser($sTestSource);
 * </pre>
 * Настройки парсинга находятся в конфиге /config/jevix.php
 *
 * @package framework.modules
 * @since 1.0
 */
class ModuleText extends Module
{
    /**
     * Объект типографа
     *
     * @var Jevix
     */
    protected $oJevix;
    /**
     * Дополнительные параметры, которые необходимо учитывать при обработке текста
     * Можно задавать произвольные параметры, главное чтобы логика обработки текста их учитывала
     * Желеательно после установки параметров и выполнения обработки эти параметры сбросить методом
     * @see ModuleText::AddExecHook
     *
     * @var array
     */
    protected $aParams = array();

    /**
     * Инициализация модуля
     *
     */
    public function Init()
    {
        /**
         * Создаем объект типографа и запускаем его конфигурацию
         */
        $this->oJevix = new Jevix();
        $this->JevixConfig();
    }

    /**
     * Конфигурирует типограф
     *
     */
    protected function JevixConfig()
    {
        // загружаем конфиг
        $this->LoadJevixConfig();
    }

    /**
     * Загружает конфиг Jevix'а
     *
     * @param string $sType Тип конфига
     * @param bool $bClear Очищать предыдущий конфиг или нет
     */
    public function LoadJevixConfig($sType = 'default', $bClear = true)
    {
        if ($bClear) {
            $this->oJevix->tagsRules = array();
        }
        $aConfig = Config::Get('jevix.' . $sType);
        if (is_array($aConfig)) {
            foreach ($aConfig as $sMethod => $aExec) {
                foreach ($aExec as $aParams) {
                    if (in_array(strtolower($sMethod),
                        array_map("strtolower", array('cfgSetTagCallbackFull', 'cfgSetTagCallback')))) {
                        if (isset($aParams[1][0]) and $aParams[1][0] == '_this_') {
                            $aParams[1][0] = $this;
                        }
                    }
                    call_user_func_array(array($this->oJevix, $sMethod), $aParams);
                }
            }
            /**
             * Хардкодим некоторые параметры
             */
            unset($this->oJevix->entities1['&']); // разрешаем в параметрах символ &
            if (Config::Get('view.noindex') and isset($this->oJevix->tagsRules['a'])) {
                $this->oJevix->cfgSetTagParamDefault('a', 'rel', 'nofollow', true);
            }
        }
    }

    /**
     * Возвращает объект Jevix
     *
     * @return Jevix
     */
    public function GetJevix()
    {
        return $this->oJevix;
    }

    /**
     * Добавляет параметры
     *
     * @param $aParams
     */
    public function AddParams($aParams)
    {
        $this->aParams = array_merge($this->aParams, $aParams);
    }

    /**
     * Возвращает параметр по имени или сразу все параметры
     *
     * @param string|null $sName Если null, то вернет массив всех параметров
     *
     * @return array|mixed
     */
    public function GetParam($sName = null)
    {
        if (is_null($sName)) {
            return $this->aParams;
        }
        if (isset($this->aParams[$sName])) {
            return $this->aParams[$sName];
        }
        return null;
    }

    /**
     * Удаляет параметры
     *
     * @param array|string|null $aNames Название параметра или список названий параметров. Если null, то удалятся все параметры
     */
    public function RemoveParams($aNames = null)
    {
        if (is_null($aNames)) {
            $this->aParams = array();
        }
        if (!is_array($aNames)) {
            $aNames = array($aNames);
        }
        foreach ($aNames as $sName) {
            unset($this->aParams[$sName]);
        }
    }

    /**
     * Парсинг текста с помощью Jevix
     *
     * @param string $sText Исходный текст
     * @param array $aError Возвращает список возникших ошибок
     * @return string
     */
    public function JevixParser($sText, &$aError = null)
    {
        // Если конфиг пустой, то загружаем его
        if (!count($this->oJevix->tagsRules)) {
            $this->LoadJevixConfig();
        }
        $sResult = $this->oJevix->parse($sText, $aError);
        return $sResult;
    }

    /**
     * Парсит текст, применя все парсеры
     *
     * @param string $sText Исходный текст
     * @return string
     */
    public function Parser($sText)
    {
        if (!is_string($sText)) {
            return '';
        }
        $sResult = $this->FlashParamParser($sText);
        $sResult = $this->JevixParser($sResult);
        return $sResult;
    }

    /**
     * Заменяет все вхождения короткого тега <param/> на длиную версию <param></param>
     * Заменяет все вхождения короткого тега <embed/> на длиную версию <embed></embed>
     *
     * @param string $sText Исходный текст
     * @return string
     */
    protected function FlashParamParser($sText)
    {
        if (preg_match_all("@(<\s*param\s*name\s*=\s*(?:\"|').*(?:\"|')\s*value\s*=\s*(?:\"|').*(?:\"|'))\s*/?\s*>(?!</param>)@Ui",
            $sText, $aMatch)) {
            foreach ($aMatch[1] as $key => $str) {
                $str_new = $str . '></param>';
                $sText = str_replace($aMatch[0][$key], $str_new, $sText);
            }
        }
        if (preg_match_all("@(<\s*embed\s*.*)\s*/?\s*>(?!</embed>)@Ui", $sText, $aMatch)) {
            foreach ($aMatch[1] as $key => $str) {
                $str_new = $str . '></embed>';
                $sText = str_replace($aMatch[0][$key], $str_new, $sText);
            }
        }
        /**
         * Удаляем все <param name="wmode" value="*"></param>
         */
        if (preg_match_all("@(<param\s.*name=(?:\"|')wmode(?:\"|').*>\s*</param>)@Ui", $sText, $aMatch)) {
            foreach ($aMatch[1] as $key => $str) {
                $sText = str_replace($aMatch[0][$key], '', $sText);
            }
        }
        /**
         * А теперь после <object> добавляем <param name="wmode" value="opaque"></param>
         * Решение не фантан, но главное работает :)
         */
        if (preg_match_all("@(<object\s.*>)@Ui", $sText, $aMatch)) {
            foreach ($aMatch[1] as $key => $str) {
                $sText = str_replace($aMatch[0][$key], $aMatch[0][$key] . '<param name="wmode" value="opaque"></param>',
                    $sText);
            }
        }
        return $sText;
    }

    /**
     * Производить резрезание текста по тегу cut.
     * Возвращаем массив вида:
     * <pre>
     * array(
     *        $sTextShort - текст до тега <cut>
     *        $sTextNew   - весь текст за исключением удаленного тега
     *        $sTextCut   - именованное значение <cut>
     * )
     * </pre>
     *
     * @param  string $sText Исходный текст
     * @return array
     */
    public function Cut($sText)
    {
        $sTextShort = $sText;
        $sTextNew = $sText;
        $sTextCut = null;

        if (preg_match("#^(.*)<cut([^>]*+)>(.*)$#Usi", $sText, $aMatch)) {
            $sTextShort = $aMatch[1];
            $sTextNew = $aMatch[1] . ' <a name="cut"></a> ' . $aMatch[3];
            if (preg_match('#^\s++name\s*+=\s*+"([^"]++)"\s*+\/?$#i', $aMatch[2], $aMatchCut)) {
                $sTextCut = trim($aMatchCut[1]);
            }
        }

        return array($sTextShort, $sTextNew, $sTextCut ? htmlspecialchars($sTextCut) : null);
    }

    /**
     * Выполняет транслитерацию текста
     *
     * @param $sText
     * @param bool $bLower
     * @return mixed|string
     */
    public function Transliteration($sText, $bLower = true)
    {
        $aConverter = (array)Config::Get('module.text.transliteration_map');
        $sRes = strtr(trim($sText), $aConverter);
        if ($sResIconv = @iconv("UTF-8", "ISO-8859-1//IGNORE//TRANSLIT", $sRes)) {
            $sRes = $sResIconv;
        }
        $sRes = preg_replace('/[^A-Za-z0-9\-]/', '', $sRes);
        $sRes = preg_replace('/\-{2,}/', '-', $sRes);
        if ($bLower) {
            $sRes = strtolower($sRes);
        }
        return $sRes;
    }

    public function CallbackParserTag($sTag, $aParams, $sContent)
    {
        $sParserClass = 'ModuleText_EntityParserTag' . func_camelize($sTag);
        if (class_exists($sParserClass)) {
            $oParser = Engine::GetEntity($sParserClass);
            return $oParser->parse($sContent, $aParams);
        }
        return '';
    }
}
