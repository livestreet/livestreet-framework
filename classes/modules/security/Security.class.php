<?php
/*-------------------------------------------------------
*
*   LiveStreet Engine Social Networking
*   Copyright © 2008 Mzhelskiy Maxim
*
*--------------------------------------------------------
*
*   Official site: www.livestreet.ru
*   Contact e-mail: rus.engine@gmail.com
*
*   GNU General Public License, version 2:
*   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*
---------------------------------------------------------
*/

/**
 * Модуль безопасности
 * Необходимо использовать перед обработкой отправленной формы:
 * <pre>
 * if (getRequest('submit_add')) {
 * 	$this->Security_ValidateSendForm();
 * 	// далее код обработки формы
 *  ......
 * }
 * </pre>
 *
 * @package engine.modules
 * @since 1.0
 */
class ModuleSecurity extends Module {
	/**
	 * Инициализируем модуль
	 *
	 */
	public function Init() {

	}
	/**
	 * Производит валидацию отправки формы/запроса от пользователя, позволяет избежать атаки CSRF
	 *
	 * @param bool $bDie	Определяет завершать работу скрипта или нет
	 *
	 * @return bool
	 */
	public function ValidateSendForm($bDie=true) {
		if (!$this->ValidateSecurityKey()) {
			if ($bDie) {
				die("Hacking attemp!");
			} else {
				return false;
			}
		}
		return true;
	}
	/**
	 * Проверка на соотвествие реферала
	 *
	 * @return bool
	 */
	public function ValidateReferal() {
		if (isset($_SERVER['HTTP_REFERER'])) {
			$aUrl=parse_url($_SERVER['HTTP_REFERER']);
			if (isset($aUrl['host'])) {
				if (strcasecmp($aUrl['host'],$_SERVER['HTTP_HOST'])==0) {
					return true;
				} elseif (preg_match("/\.".quotemeta($_SERVER['HTTP_HOST'])."$/i",$aUrl['host'])) {
					return true;
				}
			}
		}
		return false;
	}
	/**
	 * Проверяет наличие security-ключа в сессии
	 *
	 * @param null|string $sCode	Код для проверки, если нет то берется из реквеста
	 * @return bool
	 */
	public function ValidateSecurityKey($sCode=null) {
		if(!$sCode) $sCode=getRequestStr('security_ls_key');
		return ($sCode==$this->GetSecurityKey());
	}
	/**
	 * Возвращает текущий security-ключ
	 */
	public function GetSecurityKey() {
		return $this->GenerateSecurityKey();
	}
	/**
	 * Генерирует и возвращает security-ключ
	 *
	 * @return string
	 */
	protected function GenerateSecurityKey() {
		/**
		 * Сначала получаем уникальные данные пользователя по его браузеру и IP
		 */
		$sDataForHash=isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
		$sDataForHash.=isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';
		/**
		 * Далее добавляем ID сессии и уникальный ключ из конфига
		 */
		$sDataForHash.=$this->Session_GetId().Config::Get('module.security.hash');
		return md5($sDataForHash);
	}
}