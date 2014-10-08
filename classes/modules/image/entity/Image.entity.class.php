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
class ModuleImage_EntityImage extends Entity {
	/**
	 * Возвращает конкретный параметр
	 *
	 * @param $sName
	 *
	 * @return null
	 */
	public function getParam($sName) {
		$aParams=$this->getParams();
		return isset($aParams[$sName]) ? $aParams[$sName] : null;
	}
	/**
	 * Возвращает ширину изображения
	 *
	 * @return int|null
	 */
	public function getWidth() {
		if ($oImage=$this->getImage()) {
			$oBox=$oImage->getSize();
			return $oBox->getWidth();
		}
		return null;
	}
	/**
	 * Возвращает высоту изображения
	 *
	 * @return int|null
	 */
	public function getHeight() {
		if ($oImage=$this->getImage()) {
			$oBox=$oImage->getSize();
			return $oBox->getHeight();
		}
		return null;
	}
	/**
	 * Возвращает формат изображения (его расширение)
	 *
	 * @return null|string
	 */
	public function getFormat() {
		$aSize=$this->getInfoSize();
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
	 * @param int|null  $iWidthDest	Ширина необходимого изображения на выходе
	 * @param int|null $iHeightDest	Высота необходимого изображения на выходе
	 * @param bool $bForcedMinSize	Растягивать изображение по ширине или нет, если исходное меньше. При false - изображение будет растянуто
	 *
	 * @return ModuleImage_EntityImage
	 */
	public function resize($iWidthDest,$iHeightDest=null,$bForcedMinSize=true) {
		if ($oImage=$this->getImage()) {
			try {
				$oBox=$oImage->getSize();

				if ($bForcedMinSize) {
					if ($iWidthDest and $iWidthDest>$oBox->getWidth()) {
						$iWidthDest=$oBox->getWidth();
					}
					if ($iHeightDest and $iHeightDest>$oBox->getHeight()) {
						$iHeightDest=$oBox->getHeight();
					}
				}
				if (!$iHeightDest) {
					/**
					 * Производим пропорциональное уменьшение по ширине
					 */
					$oBoxResize=$oBox->widen($iWidthDest);
				} elseif(!$iWidthDest) {
					/**
					 * Производим пропорциональное уменьшение по высоте
					 */
					$oBoxResize=$oBox->heighten($iHeightDest);
				} else {
					$oBoxResize=new Imagine\Image\Box($iWidthDest,$iHeightDest);
				}

				$oImage->resize($oBoxResize);
				return $this;
			} catch (Imagine\Exception\Exception $e) {
				$this->setLastError($e->getMessage());
			}
		}
		return $this;
	}
	/**
	 * Вырезает максимально возможный прямоугольный в нужной пропорции
	 *
	 * @param float $fProp	Пропорция в котрой вырезать кроп, расчитывается как Width/Height
	 * @param string $sPosition	Вырезать из центра
	 * @return ModuleImage_EntityImage
	 */
	public function cropProportion($fProp,$sPosition='center') {
		if ($oImage=$this->getImage()) {
			try {
				$oBox=$oImage->getSize();
				$iWidth=$oBox->getWidth();
				$iHeight=$oBox->getHeight();
				/**
				 * Если высота и ширина уже в нужных пропорциях, то возвращаем изначальный вариант
				 */
				$iProp=round($fProp, 2);
				if(round($iWidth/$iHeight, 2)==$iProp) {
					return $this;
				}
				/**
				 * Вырезаем прямоугольник из центра
				 */
				if (round($iWidth/$iHeight, 2)<=$iProp) {
					$iNewWidth=$iWidth;
					$iNewHeight=round($iNewWidth/$iProp);
				} else {
					$iNewHeight=$iHeight;
					$iNewWidth=$iNewHeight*$iProp;
				}

				$oBoxCrop=new Imagine\Image\Box($iNewWidth,$iNewHeight);
				if ($sPosition=='center') {
					$oPointStart=new Imagine\Image\Point(($iWidth-$iNewWidth)/2,($iHeight-$iNewHeight)/2);
				} else {
					$oPointStart=new Imagine\Image\Point(0,0);
				}
				$oImage->crop($oPointStart,$oBoxCrop);
				return $this;
			} catch (Imagine\Exception\Exception $e) {
				$this->setLastError($e->getMessage());
			}
		}
		return $this;
	}
	/**
	 * Вырезает максимально возможный квадрат
	 *
	 * @param string $sPosition	Вырезать из центра
	 * @return ModuleImage_EntityImage
	 */
	public function cropSquare($sPosition='center') {
		return $this->cropProportion(1,$sPosition);
	}

	/**
	 * Вырезает область выделенную пользователем с помощью библиотеки jCrop
	 *
	 * @param      $aSelectedSize
	 * @param null $iCanvasWidth
	 *
	 * @return ModuleImage_EntityImage
	 */
	public function cropFromSelected($aSelectedSize,$iCanvasWidth=null) {
		if ($oImage=$this->getImage()) {
			$iWSource=$this->getWidth();
			$iHSource=$this->getHeight();
			/**
			 * Определяем коэффициент масштабируемости
			 */
			$fRation=1;
			if ($iWSource and $iCanvasWidth) {
				$fRation=$iWSource/$iCanvasWidth;
				if ($fRation<1) {
					$fRation=1;
				}
			}
			/**
			 * Проверяем корректность выделенной области
			 */
			if (isset($aSelectedSize['x']) and is_numeric($aSelectedSize['x'])
				and isset($aSelectedSize['y']) and is_numeric($aSelectedSize['y'])
					and isset($aSelectedSize['x2']) and is_numeric($aSelectedSize['x2'])
						and isset($aSelectedSize['y2']) and is_numeric($aSelectedSize['y2'])) {
				$aSelectedSize=array('x1'=>round($fRation*$aSelectedSize['x']),'y1'=>round($fRation*$aSelectedSize['y']),'x2'=>round($fRation*$aSelectedSize['x2']),'y2'=>round($fRation*$aSelectedSize['y2']));
			} else {
				$this->setLastError('Incorrect image selected size');
				return $this;
			}
			/**
			 * Достаем переменные x1 и т.п. из $aSelectedSize
			 */
			extract($aSelectedSize,EXTR_PREFIX_SAME,'ops');
			if ($x1>$x2) {
				// меняем значения переменных
				$x1 = $x1 + $x2;
				$x2 = $x1 - $x2;
				$x1 = $x1 - $x2;
			}
			if ($y1>$y2) {
				$y1 = $y1 + $y2;
				$y2 = $y1 - $y2;
				$y1 = $y1 - $y2;
			}
			if ($x1<0) {
				$x1=0;
			}
			if ($y1<0) {
				$y1=0;
			}
			if ($x2>$iWSource) {
				$x2=$iWSource;
			}
			if ($y2>$iHSource) {
				$y2=$iHSource;
			}

			$iW=$x2-$x1;
			// Допускаем минимальный клип в 32px (исключая маленькие изображения)
			if ($iW<32 && $x1+32<=$iWSource) {
				$iW=32;
			}
			$iH=$y2-$y1;
			/**
			 * Вырезаем
			 */
			try {
				$oPointStart=new Imagine\Image\Point($x1,$y1);
				$oBoxCrop=new Imagine\Image\Box($iW,$iH);
				$oImage->crop($oPointStart,$oBoxCrop);
			} catch (Imagine\Exception\Exception $e) {
				$this->setLastError($e->getMessage());
			}
		}
		return $this;
	}
	/**
	 * Сохраняет изображение в файл
	 *
	 * @param string $sFile	Полный путь до файла сохранения
	 *
	 * @return bool | string	При успешном сохранении возвращает полный путь до файла
	 */
	public function save($sFile) {
		if (!$oImage=$this->getImage()) {
			return false;
		}
		try {
			$sFormat=($this->getParam('format_auto') && $this->getFormat()) ? $this->getFormat() : $this->getParam('format');
			$sFileTmp=Config::Get('path.tmp.server').DIRECTORY_SEPARATOR.func_generator(20);
			$oImage->save($sFileTmp,array(
				'format'=>$sFormat,
				'quality'=>$this->getParam('quality'),
			));

			return $this->Image_SaveFile($sFileTmp,$sFile,0666,true);
		} catch (Imagine\Exception\Exception $e) {
			$this->setLastError($e->getMessage());
			// TODO: fix exception for Gd driver
			if (strpos($e->getFile(),'Imagine'.DIRECTORY_SEPARATOR.'Gd')) {
				restore_error_handler();
			}
		}
		return false;
	}
	/**
	 * Сохраняет изображение во временный локальный файл
	 *
	 * @return bool | string	При успешном сохранении возвращает полный локальный путь до файла
	 */
	public function saveTmp() {
		if (!$oImage=$this->getImage()) {
			return false;
		}
		try {
			$sDirTmp=Config::Get('path.tmp.server').DIRECTORY_SEPARATOR.'image';
			if (!is_dir($sDirTmp)) {
				@mkdir($sDirTmp,0777,true);
			}
			$sFormat=($this->getParam('format_auto') && $this->getFormat()) ? $this->getFormat() : $this->getParam('format');
			$sFileTmp=$sDirTmp.DIRECTORY_SEPARATOR.func_generator(20);
			$oImage->save($sFileTmp,array(
				'format'=>$sFormat,
				'quality'=>$this->getParam('quality'),
			));

			return $sFileTmp;
		} catch (Imagine\Exception\Exception $e) {
			$this->setLastError($e->getMessage());
			// TODO: fix exception for Gd driver
			if (strpos($e->getFile(),'Imagine'.DIRECTORY_SEPARATOR.'Gd')) {
				restore_error_handler();
			}
		}
		return false;
	}
	/**
	 * Сохраняет изображения в файл
	 *
	 * @param string $sDir	Директория куда нужно сохранить изображение относительно корня сайта (path.root.server)
	 * @param string $sFile	Имя файла для сохранения, без расширения (расширение подставляется автоматически в зависимости от типа изображения)
	 *
	 * @return bool | string	При успешном сохранении возвращает полный путь до файла
	 */
	public function saveSmart($sDir,$sFile) {
		if (!$oImage=$this->getImage()) {
			return false;
		}
		try {
			$sFormat=($this->getParam('format_auto') && $this->getFormat()) ? $this->getFormat() : $this->getParam('format');
			$sFileTmp=Config::Get('path.tmp.server').DIRECTORY_SEPARATOR.func_generator(20);
			$oImage->save($sFileTmp,array(
				'format'=>$sFormat,
				'quality'=>$this->getParam('quality'),
			));

			$sFile.='.'.$sFormat;
			return $this->Image_SaveFileSmart($sFileTmp,$sDir,$sFile,0666,true);
		} catch (Exception $e) {
			$this->setLastError($e->getMessage());
			// TODO: fix exception for Gd driver
			if (strpos($e->getFile(),'Imagine'.DIRECTORY_SEPARATOR.'Gd')) {
				restore_error_handler();
			}
		}
		return false;
	}
}