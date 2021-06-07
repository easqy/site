<?php


class Easqy_Google_Sheet_Service {

	private static $_client = null;

	public static function getNewSheetService() : \Google_Service_Sheets {

		if (self::$_client === null) {
			require_once EASQY_DIR . '/third-party/ggle/vendor/autoload.php';

			self::$_client = new \Google_Client();
			self::$_client->setApplicationName('Web site service');
			self::$_client->setScopes([\Google_Service_Sheets::SPREADSHEETS_READONLY]);
			self::$_client->setAccessType('offline');
			self::$_client->setApprovalPrompt("consent");
			self::$_client->setIncludeGrantedScopes(true);   // incremental auth

			$jsonAuth = json_decode( file_get_contents( __DIR__ . '/Easqy.site.WebSite.key.json'), true );
			self::$_client->setAuthConfig($jsonAuth);
		}
		return $sheets = new \Google_Service_Sheets(self::$_client);
	}
}