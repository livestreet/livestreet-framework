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
 * Сущность для работы с изображением
 *
 * @package framework.modules
 * @since 2.0
 */
class ModuleImage_EntityImage extends Entity
{
    /**
     * Возвращает конкретный параметр
     *
     * @param $sName
     *
     * @return null
     */
    public function getParam($sName)
    {
        $aParams = $this->getParams();
        return isset($aParams[$sName]) ? $aParams[$sName] : null;
    }

    /**
     * Возвращает ширину изображения
     *
     * @return int|null
     */
    public function getWidth()
    {
        if ($oImage = $this->getImage()) {
            $oBox = $oImage->getSize();
            return $oBox->getWidth();
        }
        return null;
    }

    /**
     * Возвращает высоту изображения
     *
     * @return int|null
     */
    public function getHeight()
    {
        if ($oImage = $this->getImage()) {
            $oBox = $oImage->getSize();
            return $oBox->getHeight();
        }
        return null;
    }

    /**
     * Возвращает формат изображения (его расширение)
     *
     * @return null|string
     */
    public function getFormat()
    {
        $aSize = $this->getInfoSize();
        if (isset($aSize['mime'])) {
            switch ($aSize['mime']) {
                case 'image/png':
                case "image/x-png":
                    return 'png';
                    break;
                case 'image/gif':
                    return 'gif';
                    break;
                case "image/pjpeg":
                case "image/jpeg":
                case "image/jpg":
                    return 'jpg';
                    break;
                default:
                    return 'jpg';
            }
        }
        return null;
    }

    /**
     * Изменяет размеры изображения
     *
     * @param int|null $iWidthDest Ширина необходимого изображения на выходе
     * @param int|null $iHeightDest Высота необходимого изображения на выходе
     * @param bool $bForcedMinSize Растягивать изображение по ширине или нет, если исходное меньше. При false - изображение будет растянуто
     *
     * @return ModuleImage_EntityImage
     */
    public function resize($iWidthDest, $iHeightDest = null, $bForcedMinSize = true)
    {
        if ($oImage = $this->getImage()) {
            try {
                $oBox = $oImage->getSize();

                if ($bForcedMinSize) {
                    if ($iWidthDest and $iWidthDest > $oBox->getWidth()) {
                        $iWidthDest = $oBox->getWidth();
                    }
                    if ($iHeightDest and $iHeightDest > $oBox->getHeight()) {
                        $iHeightDest = $oBox->getHeight();
                    }
                }
                if (!$iHeightDest) {
                    /**
                     * Производим пропорциональное уменьшение по ширине
                     */
                    $oBoxResize = $oBox->widen($iWidthDest);
                } elseif (!$iWidthDest) {
                    /**
                     * Производим пропорциональное уменьшение по высоте
                     */
                    $oBoxResize = $oBox->heighten($iHeightDest);
                } else {
                    $oBoxResize = new Imagine\Image\Box($iWidthDest, $iHeightDest);
                }

                $oImage->resize($oBoxResize);
                return $this;
            } catch (Imagine\Exception\Exception $e) {
                $this->setLastError($e->getMessage());
                // write to log
                $this->Logger_Warning('Image error: ' . $e->getMessage(), array('exception' => $e));
            }
        }
        return $this;
    }

    /**
     * Вырезает максимально возможный прямоугольный в нужной пропорции
     *
     * @param float $fProp Пропорция в которой вырезать кроп, расчитывается как Width/Height
     * @param string $sPosition Вырезать из центра
     * @return ModuleImage_EntityImage
     */
    public function cropProportion($fProp, $sPosition = 'center')
    {
        if ($oImage = $this->getImage()) {
            try {
                $oBox = $oImage->getSize();
                $iWidth = $oBox->getWidth();
                $iHeight = $oBox->getHeight();
                /**
                 * Если высота и ширина уже в нужных пропорциях, то возвращаем изначальный вариант
                 */
                $iProp = round($fProp, 2);
                if (round($iWidth / $iHeight, 2) == $iProp) {
                    return $this;
                }
                /**
                 * Вырезаем прямоугольник из центра
                 */
                if (round($iWidth / $iHeight, 2) <= $iProp) {
                    $iNewWidth = $iWidth;
                    $iNewHeight = round($iNewWidth / $iProp);
                } else {
                    $iNewHeight = $iHeight;
                    $iNewWidth = $iNewHeight * $iProp;
                }

                $oBoxCrop = new Imagine\Image\Box($iNewWidth, $iNewHeight);
                if ($sPosition == 'center') {
                    $oPointStart = new Imagine\Image\Point(($iWidth - $iNewWidth) / 2, ($iHeight - $iNewHeight) / 2);
                } else {
                    $oPointStart = new Imagine\Image\Point(0, 0);
                }
                $oImage->crop($oPointStart, $oBoxCrop);
                return $this;
            } catch (Imagine\Exception\Exception $e) {
                $this->setLastError($e->getMessage());
                // write to log
                $this->Logger_Warning('Image error: ' . $e->getMessage(), array('exception' => $e));
            }
        }
        return $this;
    }

    /**
     * Вырезает максимально возможный квадрат
     *
     * @param string $sPosition Вырезать из центра
     * @return ModuleImage_EntityImage
     */
    public function cropSquare($sPosition = 'center')
    {
        return $this->cropProportion(1, $sPosition);
    }

    /**
     * Вырезает область выделенную пользователем с помощью библиотеки jCrop
     *
     * @param      $aSelectedSize
     * @param null $iCanvasWidth
     *
     * @return ModuleImage_EntityImage
     */
    public function cropFromSelected($aSelectedSize, $iCanvasWidth = null)
    {
        if ($oImage = $this->getImage()) {
            $iWSource = $this->getWidth();
            $iHSource = $this->getHeight();
            /**
             * Определяем коэффициент масштабируемости
             */
            $fRation = 1;
            if ($iWSource and $iCanvasWidth) {
                $fRation = $iWSource / $iCanvasWidth;
                if ($fRation < 1) {
                    $fRation = 1;
                }
            }
            /**
             * Проверяем корректность выделенной области
             */
            if (isset($aSelectedSize['x']) and is_numeric($aSelectedSize['x'])
                and isset($aSelectedSize['y']) and is_numeric($aSelectedSize['y'])
                and isset($aSelectedSize['x2']) and is_numeric($aSelectedSize['x2'])
                and isset($aSelectedSize['y2']) and is_numeric($aSelectedSize['y2'])
            ) {
                $aSelectedSize = array(
                    'x1' => round($fRation * $aSelectedSize['x']),
                    'y1' => round($fRation * $aSelectedSize['y']),
                    'x2' => round($fRation * $aSelectedSize['x2']),
                    'y2' => round($fRation * $aSelectedSize['y2'])
                );
            } else {
                $this->setLastError('Incorrect image selected size');
                return $this;
            }
            /**
             * Достаем переменные x1 и т.п. из $aSelectedSize
             */
            extract($aSelectedSize, EXTR_PREFIX_SAME, 'ops');
            if ($x1 > $x2) {
                // меняем значения переменных
                $x1 = $x1 + $x2;
                $x2 = $x1 - $x2;
                $x1 = $x1 - $x2;
            }
            if ($y1 > $y2) {
                $y1 = $y1 + $y2;
                $y2 = $y1 - $y2;
                $y1 = $y1 - $y2;
            }
            if ($x1 < 0) {
                $x1 = 0;
            }
            if ($y1 < 0) {
                $y1 = 0;
            }
            if ($x2 > $iWSource) {
                $x2 = $iWSource;
            }
            if ($y2 > $iHSource) {
                $y2 = $iHSource;
            }

            $iW = $x2 - $x1;
            // Допускаем минимальный клип в 32px (исключая маленькие изображения)
            if ($iW < 32 && $x1 + 32 <= $iWSource) {
                $iW = 32;
            }
            $iH = $y2 - $y1;
            /**
             * Вырезаем
             */
            try {
                $oPointStart = new Imagine\Image\Point($x1, $y1);
                $oBoxCrop = new Imagine\Image\Box($iW, $iH);
                $oImage->crop($oPointStart, $oBoxCrop);
            } catch (Imagine\Exception\Exception $e) {
                $this->setLastError($e->getMessage());
                // write to log
                $this->Logger_Warning('Image error: ' . $e->getMessage(), array('exception' => $e));
            }
        }
        return $this;
    }

    /**
     * Сохраняет изображение в файл
     *
     * @param string $sFile Полный путь до файла сохранения
     * @param array $aParamsSave Дополнительные опции сохранения, например, не делать вотермарк
     *
     * @return bool | string    При успешном сохранении возвращает полный путь до файла
     */
    public function save($sFile, $aParamsSave = array())
    {
        $_this = $this;
        return $this->callExceptionMethod(function ($oImage) use ($_this, $sFile, $aParamsSave) {

            $sFormat = ($_this->getParam('format_auto') && $_this->getFormat()) ? $_this->getFormat() : $_this->getParam('format');
            $sFileTmp = Config::Get('path.tmp.server') . DIRECTORY_SEPARATOR . func_generator(20);
            $_this->internalSave($sFileTmp, $sFormat, $aParamsSave);

            return $_this->Image_SaveFile($sFileTmp, $sFile, 0666, true);

        });
    }

    /**
     * Сохраняет изображение во временный локальный файл
     *
     * @return bool | string    При успешном сохранении возвращает полный локальный путь до файла
     */
    public function saveTmp()
    {
        $_this = $this;
        return $this->callExceptionMethod(function ($oImage) use ($_this) {

            $sDirTmp = Config::Get('path.tmp.server') . DIRECTORY_SEPARATOR . 'image';
            if (!is_dir($sDirTmp)) {
                @mkdir($sDirTmp, 0777, true);
            }
            $sFormat = ($_this->getParam('format_auto') && $_this->getFormat()) ? $_this->getFormat() : $_this->getParam('format');
            $sFileTmp = $sDirTmp . DIRECTORY_SEPARATOR . func_generator(20);
            $_this->internalSave($sFileTmp, $sFormat, array('skip_watermark' => true));
            return $sFileTmp;

        });
    }

    /**
     * Сохраняет изображение в файл
     *
     * @param string $sDir Директория куда нужно сохранить изображение относительно корня сайта (path.root.server)
     * @param string $sFile Имя файла для сохранения, без расширения (расширение подставляется автоматически в зависимости от типа изображения)
     * @param array $aParamsSave Дополнительные опции сохранения, например, не делать вотермарк
     *
     * @return bool | string    При успешном сохранении возвращает полный путь до файла
     */
    public function saveSmart($sDir, $sFile, $aParamsSave = array())
    {
        $_this = $this;
        return $this->callExceptionMethod(function ($oImage) use ($_this, $sDir, $sFile, $aParamsSave) {

            $sFormat = ($_this->getParam('format_auto') && $_this->getFormat()) ? $_this->getFormat() : $_this->getParam('format');
            $sFileTmp = Config::Get('path.tmp.server') . DIRECTORY_SEPARATOR . func_generator(20);
            $_this->internalSave($sFileTmp, $sFormat, $aParamsSave);

            $sFile .= '.' . $sFormat;
            return $_this->Image_SaveFileSmart($sFileTmp, $sDir, $sFile, 0666, true);

        });
    }

    /**
     * Сохраняет оригинальный файл без изменений
     *
     * @param string $sFile Полный путь до файла сохранения
     * @return bool
     */
    public function saveOriginal($sFile)
    {
        if ($sFileOriginal = $this->getFileOriginalPath()) {
            return $this->Image_SaveFile($sFileOriginal, $sFile, 0666);
        }
        return false;
    }

    /**
     * Сохраняет оригинальный файл без изменений
     *
     * @param string $sDir Директория куда нужно сохранить изображение относительно корня сайта (path.root.server)
     * @param string $sFile Имя файла для сохранения, без расширения (расширение подставляется автоматически в зависимости от типа изображения)
     * @return bool
     */
    public function saveOriginalSmart($sDir, $sFile)
    {
        if ($sFileOriginal = $this->getFileOriginalPath()) {
            $sFormat = ($this->getParam('format_auto') && $this->getFormat()) ? $this->getFormat() : $this->getParam('format');
            $sFile .= '.' . $sFormat;
            return $this->Image_SaveFileSmart($sFileOriginal, $sDir, $sFile, 0666);
        }
        return false;
    }

    /**
     * Обертка для удобного вызова методов Imagine с обработкой исключений
     *
     * @param callable $fCallback
     * @return bool
     */
    public function callExceptionMethod(\Closure $fCallback)
    {
        if (!$oImage = $this->getImage()) {
            return false;
        }
        try {
            return $fCallback($oImage);
        } catch (Exception $e) {
            $this->setLastError($e->getMessage());
            // write to log
            $this->Logger_Warning('Image error: ' . $e->getMessage(), array('exception' => $e));
            // TODO: fix exception for Gd driver
            if (strpos($e->getFile(), 'Imagine' . DIRECTORY_SEPARATOR . 'Gd')) {
                restore_error_handler();
            }
        }
        return false;
    }

    /**
     * Устанавливает режим сохранения изображения
     * ModuleImage::INTERLACE_PLANE - прогрессивный режим
     *
     * @param $sScheme  ModuleImage::INTERLACE_*
     * @return $this
     */
    public function interlace($sScheme)
    {
        $_this = $this;
        $this->callExceptionMethod(function ($oImage) use ($_this, $sScheme) {

            $aMap = array(
                ModuleImage::INTERLACE_NONE      => \Imagine\Image\ImageInterface::INTERLACE_NONE,
                ModuleImage::INTERLACE_LINE      => \Imagine\Image\ImageInterface::INTERLACE_LINE,
                ModuleImage::INTERLACE_PLANE     => \Imagine\Image\ImageInterface::INTERLACE_PLANE,
                ModuleImage::INTERLACE_PARTITION => \Imagine\Image\ImageInterface::INTERLACE_PARTITION,
            );
            $sScheme = array_key_exists($sScheme,
                $aMap) ? $aMap[$sScheme] : \Imagine\Image\ImageInterface::INTERLACE_NONE;
            $oImage->interlace($sScheme);

        });
        return $this;
    }

    /**
     * Накладывает ватермарк-изображение
     *
     * @param string $sFile Полный локальный путь до ватермарка
     * @param string|\Imagine\Image\Point $mPosition Позиция в которую нужно вставить ватермарк. bottom-left, bottom-right, top-left, top-right, center
     * @return $this
     */
    public function watermark($sFile, $mPosition)
    {
        $_this = $this;
        $this->callExceptionMethod(function ($oImage) use ($_this, $sFile, $mPosition) {

            $oWatermark = $_this->Image_Open($sFile);
            if (!$oWatermark or !($oWatermark = $oWatermark->getImage())) {
                return false;
            }

            $oSize = $oImage->getSize();
            /**
             * Проверяем минимальный допустимый размер изображения
             */
            if (($_this->getParam('watermark_min_width') and $_this->getParam('watermark_min_width') > $oSize->getWidth())
                or ($_this->getParam('watermark_min_height') and $_this->getParam('watermark_min_height') > $oSize->getHeight())
            ) {
                return false;
            }
            if (!is_object($mPosition)) {
                $oSizeW = $oWatermark->getSize();
                /**
                 * Определяем координаты позиции ватермарка
                 */
                if ($mPosition == 'bottom-left') {
                    $oPosition = new Imagine\Image\Point(0, $oSize->getHeight() - $oSizeW->getHeight());
                } elseif ($mPosition == 'top-left') {
                    $oPosition = new Imagine\Image\Point(0, 0);
                } elseif ($mPosition == 'top-right') {
                    $oPosition = new Imagine\Image\Point($oSize->getWidth() - $oSizeW->getWidth(), 0);
                } elseif ($mPosition == 'center') {
                    $oPosition = new Imagine\Image\Point(round(($oSize->getWidth() - $oSizeW->getWidth()) / 2),
                        round(($oSize->getHeight() - $oSizeW->getHeight()) / 2));
                } else {
                    // bottom-right and other
                    $oPosition = new Imagine\Image\Point($oSize->getWidth() - $oSizeW->getWidth(),
                        $oSize->getHeight() - $oSizeW->getHeight());
                }
            } else {
                $oPosition = $mPosition;
            }
            $oImage->paste($oWatermark, $oPosition);

        });
        return $this;
    }

    /**
     * Сохраняет изображение в локальный файл
     * Не рекомендуется использовать этот метод напрямую
     *
     * @param string $sFile Полный путь до локального файла
     * @param string $sFormat Формат сохранения: jpg, gif, png
     * @param array $aParamsSave Дополнительные опции сохранения, например, не делать вотермарк
     *
     * @return bool
     */
    public function internalSave($sFile, $sFormat, $aParamsSave = array())
    {
        if (!$oImage = $this->getImage()) {
            return false;
        }
        $aParamsSave = array_merge(array(
            'skip_watermark' => false
        ), $aParamsSave);
        if ($this->getParam('interlace')) {
            $this->interlace($this->getParam('interlace'));
        }
        if (!$aParamsSave['skip_watermark'] and $this->getParam('watermark_use')) {
            if ($this->getParam('watermark_type') == 'image') {
                $this->watermark($this->getParam('watermark_image'), $this->getParam('watermark_position'));
            }
        }
        $oImage->save($sFile, array(
            'format'  => $sFormat,
            'quality' => $this->getParam('quality'),
        ));
    }
}
