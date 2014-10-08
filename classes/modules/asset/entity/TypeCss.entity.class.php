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
 * Тип CSS стилей
 *
 * @package framework.modules
 * @since 2.0
 */
class ModuleAsset_EntityTypeCss extends ModuleAsset_EntityType {
	/**
	 * Производит предварительную обработку содержимого
	 *
	 */
	public function prepare() {
		$this->setContent(
			$this->convertUrls($this->getContent(),$this->getFile())
		);
	}
	/**
	 * Выполняет сжатие
	 *
	 * @return mixed|void
	 */
	public function compress() {

	}
	/**
	 * Возвращает HTML обертку для файла
	 *
	 * @param $sFile
	 * @param $aParams
	 *
	 * @return string
	 */
	public function getHeadHtml($sFile,$aParams) {
		$sHtml='<link rel="stylesheet" type="text/css" href="'.$sFile.'" />';
		return $this->wrapForBrowser($sHtml,$aParams);
	}
	/**
	 * Конвертирует относительные пути до файлов внутри css
	 *
	 * @param $sContent
	 * @param $sFile
	 *
	 * @return mixed
	 */
	protected function convertUrls($sContent,$sFile) {
		if (preg_match_all( "/url\((.*?)\)/is",$sContent,$aMatches)) {
			/**
			 * Обрабатываем список файлов
			 */
			$aFiles = array_unique($aMatches[1]);
			$sDir = dirname($sFile)."/";
			foreach($aFiles as $sFilePath) {
				/**
				 * Don't touch data URIs
				 */
				if(strstr($sFilePath,"data:")) {
					continue;
				}
				$sFilePathAbsolute = preg_replace("@'|\"@","",trim($sFilePath));
				/**
				 * Если путь является абсолютным, необрабатываем
				 */
				if(substr($sFilePathAbsolute,0,1) == "/" || in_array(substr($sFilePathAbsolute,0,6),array('http:/','https:'))) {
					continue;
				}
				/**
				 * Обрабатываем относительный путь
				 */
				$aPath=explode('?',$sFilePathAbsolute,2);
				$sFilePathAbsolute=$aPath[0];
				$sGetParams=isset($aPath[1]) ? $aPath[1] : '';
				$sFilePathAbsolute=$this->Asset_GetRealpath($sDir.$sFilePathAbsolute);
				$sFilePathAbsolute=$this->Fs_GetPathWebFromServer($sFilePathAbsolute).($sGetParams ? "?{$sGetParams}" : '');
				/**
				 * Заменяем относительные пути в файле на абсолютные
				 */
				$sContent=str_replace($sFilePath,$sFilePathAbsolute,$sContent);
			}
		}
		return $sContent;
	}
}