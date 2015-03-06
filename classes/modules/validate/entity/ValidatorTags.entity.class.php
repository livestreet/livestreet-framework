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
 * Валидатор тегов - строка с перечислением тегов
 *
 * @package framework.modules.validate
 * @since 1.0
 */
class ModuleValidate_EntityValidatorTags extends ModuleValidate_EntityValidator
{
    /**
     * Максимальня длина тега
     *
     * @var int
     */
    public $max = 50;
    /**
     * Минимальня длина тега
     *
     * @var int
     */
    public $min = 2;
    /**
     * Максимальное количество тегов
     *
     * @var int
     */
    public $countMax = 15;
    /**
     * Минимальное количество тегов
     *
     * @var int
     */
    public $countMin = 1;
    /**
     * Разделитель тегов
     *
     * @var string
     */
    public $sep = ',';
    /**
     * Допускать или нет пустое значение
     *
     * @var bool
     */
    public $allowEmpty = false;

    /**
     * Запуск валидации
     *
     * @param mixed $sValue Данные для валидации
     *
     * @return bool|string
     */
    public function validate($sValue)
    {
        if (is_array($sValue)) {
            return $this->getMessage($this->Lang_Get('validate.tags.not_valid', null, false), 'msg');
        }
        if ($this->allowEmpty && $this->isEmpty($sValue)) {
            return true;
        }

        $aTags = explode($this->sep, trim($sValue, "\r\n\t\0\x0B ."));
        $aTagsNew = array();
        $aTagsNewLow = array();
        foreach ($aTags as $sTag) {
            $sTag = trim($sTag, "\r\n\t\0\x0B .");
            $iLength = mb_strlen($sTag, 'UTF-8');
            if ($iLength >= $this->min and $iLength <= $this->max and !in_array(mb_strtolower($sTag, 'UTF-8'),
                    $aTagsNewLow)
            ) {
                $aTagsNew[] = $sTag;
                $aTagsNewLow[] = mb_strtolower($sTag, 'UTF-8');
            }
        }
        $iCount = count($aTagsNew);
        if ($iCount > $this->countMax or $iCount < $this->countMin) {
            return $this->getMessage($this->Lang_Get('validate.tags.count', null, false), 'msg',
                array('max' => $this->countMax, 'min' => $this->countMin));
        } elseif (!$iCount) {
            return $this->getMessage($this->Lang_Get('validate.tags.empty', null, false), 'msg',
                array('min' => $this->min, 'max' => $this->max));
        }
        /**
         * Если проверка от сущности, то возвращаем обновленное значение
         */
        if ($this->oEntityCurrent) {
            $this->setValueOfCurrentEntity($this->sFieldCurrent, join($this->sep, $aTagsNew));
        }
        return true;
    }
}