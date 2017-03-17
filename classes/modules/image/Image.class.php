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
 * Модуль обработки изображений
 * Используется библиотека Imagine
 *
 * @package framework.modules
 * @since 2.0
 */
class ModuleImage extends Module
{

    const INTERLACE_NONE = 'none';
    const INTERLACE_LINE = 'line';
    const INTERLACE_PLANE = 'plane';
    const INTERLACE_PARTITION = 'partition';

    const ERROR_CODE_UNDEFINED = 0;
    const ERROR_CODE_WRONG_FORMAT = 1;
    const ERROR_CODE_WRONG_MAX_SIZE = 2;

    /**
     * Дефолтные параметры
     * Основная задача в определении списка доступных ключей массива с параметрами
     *
     * @var array
     */
    protected $aParamsDefault = array(
        'size_max_width'       => 7000,
        'size_max_height'      => 7000,
        'format'               => 'jpg',
        'format_auto'          => true,
        'quality'              => 95,
        'interlace'            => self::INTERLACE_PLANE,
        'watermark_use'        => false,
        'watermark_type'       => 'image',
        'watermark_image'      => null,
        'watermark_position'   => 'bottom-right',
        'watermark_min_width'  => 100,
        'watermark_min_height' => 100,
    );
    /**
     * Тескт последней ошибки
     *
     * @var string
     */
    protected $sLastErrorText = null;
    /**
     * Код последней ошибки
     *
     * @var int
     */
    protected $iLastErrorCode = null;
    /**
     * Список поддерживаемых драйверов обработки изображений
     *
     * @var array
     */
    protected $aDriversSupport = array(
        'gd',
        'imagick',
        'gmagick'
    );

    /**
     * Текущий драйвер обработки изображений
     *
     * @var string
     */
    protected $sDriverCurrent = 'gd';

    public function Init()
    {
        $this->SetDriverCurrent(Config::Get('module.image.driver'));
    }

    /**
     * Устанавливает текущий драйвер обработки изображений
     *
     * @param $sDriver
     *
     * @return bool
     */
    public function SetDriverCurrent($sDriver)
    {
        $sDriver = strtolower($sDriver);
        if (in_array($sDriver, $this->aDriversSupport)) {
            $this->sDriverCurrent = $sDriver;
            return true;
        }
        return false;
    }

    /**
     * Возвращает текущий драйвер
     *
     * @return string
     */
    public function GetDriverCurrent()
    {
        return $this->sDriverCurrent;
    }

    /**
     * Получает текст последней ошибки
     *
     * @return string
     */
    public function GetLastError()
    {
        return $this->sLastErrorText;
    }

    /**
     * Получает код последней ошибки
     *
     * @return int
     */
    public function GetLastErrorCode()
    {
        return $this->iLastErrorCode;
    }

    /**
     * Устанавливает текст последней ошибки
     *
     * @param string $sText Текст ошибки
     * @param int|null $iCode Код ошибки
     */
    public function SetLastError($sText, $iCode = null)
    {
        $this->sLastErrorText = $sText;
        $this->SetLastErrorCode($iCode);
    }

    /**
     * Устанавливает код последней ошибки
     *
     * @param int|null $iCode
     */
    public function SetLastErrorCode($iCode)
    {
        $this->iLastErrorCode = $iCode;
    }

    /**
     * Очищает текст последней ошибки
     *
     */
    public function ClearLastError()
    {
        $this->SetLastError(null);
    }

    /**
     * Возвращает класс текущего драйвера
     *
     * @return string
     */
    protected function GetClassDriverCurrent()
    {
        $sDrive = ucfirst($this->sDriverCurrent);
        return "Imagine\\{$sDrive}\\Imagine";
    }

    /**
     * Открывает файл изображения и возвращает объект
     *
     * @param $sFile    Локальный путь до изображения
     * @param $aParams    Параметры
     * @return ModuleImage_EntityImage|bool
     */
    public function Open($sFile, $aParams = null)
    {
        if (!is_array($aParams)) {
            $aParams = $this->BuildParams();
        } else {
            $aParams = func_array_merge_assoc($this->aParamsDefault, $aParams);
        }

        $sClassDriver = $this->GetClassDriverCurrent();

        try {
            /**
             * Создаем объект изображения библиотеки Imagine
             */
            $oImagine = new $sClassDriver();
            $oImageObject = $oImagine->open($sFile);

            if (!$aSize = getimagesize($sFile, $aImageInfo)) {
                $this->SetLastError('The file is not an image', self::ERROR_CODE_WRONG_FORMAT);
                return false;
            }
            /**
             * Проверяем на максимальный размер
             */
            $oBox = $oImageObject->getSize();
            if ($oBox->getWidth() > $aParams['size_max_width'] or $oBox->getHeight() > $aParams['size_max_height']) {
                $this->SetLastError('Maximum size image ' . $aParams['size_max_width'] . 'x' . $aParams['size_max_height'], self::ERROR_CODE_WRONG_MAX_SIZE);
                return false;
            }
            /**
             * Создаем объект для работы с изображением
             */
            $oImage = Engine::GetEntity('ModuleImage_EntityImage');
            $oImage->setImage($oImageObject);
            $oImage->setParams($aParams);
            $oImage->setInfoSize($aSize);
            $oImage->setFileOriginalPath($sFile);
            $oImage->setInfoAdditional($aImageInfo);
            return $oImage;
        } catch (Imagine\Exception\Exception $e) {
            $this->SetLastError($this->Lang_Get('image.error.not_open'), self::ERROR_CODE_UNDEFINED);
            // write to log
            $this->Logger_Warning('Image error: ' . $e->getMessage(), array('exception' => $e));
            return false;
        }
    }

    /**
     * Создает пустой объект изображения
     *
     * @param int $iWidth
     * @param int $iHeight
     * @param array|null $aParams
     *
     * @return ModuleImage_EntityImage|bool
     */
    public function Create($iWidth, $iHeight, $aParams = null)
    {
        if (!is_array($aParams)) {
            $aParams = $this->BuildParams();
        } else {
            $aParams = func_array_merge_assoc($this->aParamsDefault, $aParams);
        }

        $sClassDriver = $this->GetClassDriverCurrent();

        try {
            /**
             * Создаем объект изображения библиотеки Imagine
             */
            $oImagine = new $sClassDriver();
            $oImageObject = $oImagine->create(new Imagine\Image\Box($iWidth, $iHeight));
            /**
             * Создаем объект для работы с изображением
             */
            $oImage = Engine::GetEntity('ModuleImage_EntityImage');
            $oImage->setImage($oImageObject);
            $oImage->setParams($aParams);
            return $oImage;
        } catch (Imagine\Exception\Exception $e) {
            $this->SetLastError($e->getMessage());
            // write to log
            $this->Logger_Warning('Image error: ' . $e->getMessage(), array('exception' => $e));
            return false;
        }
    }

    /**
     * Возврашает параметры для группы, если каких-то параметров в группе нет, то используются дефолтные
     *
     * @param  string $sName Имя группы
     * @return array
     */
    public function BuildParams($sName = null)
    {
        $aDefault = func_array_merge_assoc($this->aParamsDefault, (array)Config::Get('module.image.params.default'));
        if (is_null($sName)) {
            return $aDefault;
        }
        $aNamed = (array)Config::Get('module.image.params.' . strtolower($sName));
        return func_array_merge_assoc($aDefault, $aNamed);
    }


    /**
     * Сохраняет(копирует) файл изображения на сервер
     * Если переопределить данный метод, то можно сохранять изображения, например, на Amazon S3
     *
     * @param string $sFileSource Полный путь до исходного файла
     * @param string $sDirDest Каталог для сохранения файла относительно корня сайта
     * @param string $sFileDest Имя файла для сохранения
     * @param int|null $iMode Права chmod для файла, например, 0777
     * @param bool $bRemoveSource Удалять исходный файл или нет
     * @return bool | string    При успешном сохранении возвращает относительный путь до файла с типом, например, [relative]/image.jpg
     */
    public function SaveFileSmart($sFileSource, $sDirDest, $sFileDest, $iMode = null, $bRemoveSource = false)
    {
        if ($sPathFile = $this->Fs_SaveFileLocalSmart($sFileSource, $sDirDest, $sFileDest, $iMode, $bRemoveSource)) {
            return $this->Fs_MakePath($this->Fs_GetPathRelativeFromServer($sPathFile), ModuleFs::PATH_TYPE_RELATIVE);
        }
        return false;
    }

    /**
     * Сохраняет(копирует) файл изображения на сервер
     * Если переопределить данный метод, то можно сохранять изображения, например, на Amazon S3
     *
     * @param string $sFileSource Полный путь до исходного файла
     * @param string $sFileDest Полный путь до файла для сохранения с типом, например, [server]/home/var/site.ru/image.jpg
     * @param int|null $iMode Права chmod для файла, например, 0777
     * @param bool $bRemoveSource Удалять исходный файл или нет
     * @return bool | string    При успешном сохранении возвращает относительный путь до файла с типом, например, [relative]/image.jpg
     */
    public function SaveFile($sFileSource, $sFileDest, $iMode = null, $bRemoveSource = false)
    {
        if ($this->Fs_SaveFileLocal($sFileSource, $this->Fs_GetPathServer($sFileDest), $iMode, $bRemoveSource)) {
            return $this->Fs_MakePath($this->Fs_GetPathRelativeFromServer($sFileDest), ModuleFs::PATH_TYPE_RELATIVE);
        }
        return false;
    }

    /**
     * Удаляет файл изображения
     * Если переопределить данный метод, то можно удалять изображения, например, с Amazon S3
     *
     * @param string $sPathFile Полный путь до файла с типом, например, [relative]/image.jpg
     *
     * @return mixed
     */
    public function RemoveFile($sPathFile)
    {
        $sPathFile = $this->Fs_GetPathServer($sPathFile);
        return $this->Fs_RemoveFileLocal($sPathFile);
    }

    /**
     * Проверяет изображение на существование
     * Если переопределить данный метод, то можно проверить существование изображения, например, на Amazon S3
     *
     * @param string $sPathFile Полный путь до файла с типом, например, [relative]/image.jpg
     *
     * @return mixed
     */
    public function IsExistsFile($sPathFile)
    {
        $sPathFile = $this->Fs_GetPathServer($sPathFile);
        return $this->Fs_IsExistsFileLocal($sPathFile);
    }

    /**
     * Открывает файл изображения, в качестве источника изображения может использоваться полный путь до файла с типом, например, [relative]/image.jpg
     * Если переопределить данный метод, то можно открывать изображения, например, с Amazon S3
     *
     * @param string $sFile Полный путь до файла с типом, например, [relative]/image.jpg
     * @param null $aParams
     *
     * @return bool|ModuleImage_EntityImage
     */
    public function OpenFrom($sFile, $aParams = null)
    {
        $sFile = $this->Fs_GetPathServer($sFile);
        return $this->Open($sFile, $aParams);
    }

    /**
     * Получает директорию для загрузки изображений
     * Используется фомат хранения данных (/images/subdir/obj/ect/id/yyyy/mm/dd/file.jpg)
     * Например, для хранения изображений пользователя (аватары и т.п.) c ID=1 можно так: /images/users/000/000/001/2014/02/15/avatar.jpg
     *
     * @param  int $sId Целое число, обычно это ID объекта
     * @param  string $sSubDir Подкаталог
     * @return string
     */
    public function GetIdDir($sId, $sSubDir = null)
    {
        return Config::Get('path.uploads.images') . '/' . ($sSubDir ? $sSubDir . '/' : '') . preg_replace('~(.{3})~U',
            "\\1/", str_pad($sId, 9, "0", STR_PAD_LEFT)) . date('Y/m/d');
    }
}
