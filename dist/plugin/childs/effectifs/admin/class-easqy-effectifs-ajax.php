<?php


class Easqy_Effectifs_Ajax {

	private const SPREADSHEET_ID = '1KVvW9axxfOTcahJkSk9MnbwtWGgRW_Na3k9hdb5WGsg';

	private const URL_ROOT='https://docs.google.com/spreadsheets/d/e';
	const EFFECTIFS_DOC_NAME='2PACX-1vRUx4pUTZPMhuVOUoujzVjv4kKn4I1dnSLGCL76lkQ-ELT7qEAsiDNgoy_pw8plkP0i7t8CfSq17Gv0';
	const EFFECTIFS_URL=self::URL_ROOT . '/' . self::EFFECTIFS_DOC_NAME . '/pub?output=csv';

	const RENOUVELLEMENTS_DOC_NAME='2PACX-1vSqCPKYpB9Obvwa-Fcf1ZYQ2q7yoAuQOVMFDJjp0SW1f5ulQgw7Wyi0qXoei1_vVPG3XYGA8vMnujdR';
	const RENOUVELLEMENTS_URL=self::URL_ROOT . '/' . self::RENOUVELLEMENTS_DOC_NAME . '/pub?output=csv';

	const GEOGRAPHIQUES_DOC_NAME='2PACX-1vQfnsx4NxDrBGkHVeYu3i7Yge7TsUNgnU3WLd269K4nVzxe3EnDfXLeEMA1UY24RqmyFHVC7GQc0GVn';
	const GEOGRAPHIQUES_URL=self::URL_ROOT . '/' . self::GEOGRAPHIQUES_DOC_NAME . '/pub?output=csv';

	public function __construct(Easqy $easqy) {

		$loader = $easqy->get_loader();
		$loader->add_action( 'wp_ajax_easqy_effectifs', $this, 'effectifs' );
		$loader->add_action( 'wp_ajax_nopriv_easqy_effectifs', $this,'effectifs' );
		$loader->add_action( 'wp_ajax_easqy_renouvellements', $this,'renouvellements' );
		$loader->add_action( 'wp_ajax_nopriv_easqy_renouvellements', $this,'renouvellements' );
		$loader->add_action( 'wp_ajax_easqy_effectifs_geographiques', $this,'geographiques' );
		$loader->add_action( 'wp_ajax_nopriv_easqy_effectifs_geographiques', $this,'geographiques' );
	}

	static public function loadEffectifs()
	{
		require_once EASQY_DIR . '/includes/class-easqy-google-sheet-service.php';

		try {
			$sheets = Easqy_Google_Sheet_Service::getNewSheetService();

			$datas =$sheets->spreadsheets_values->get(self::SPREADSHEET_ID,
				'effectifs!A1:Z', ['majorDimension' => 'ROWS'])->getValues();

			$Years = array_shift($datas);
			array_shift($Years);
			for ($i= count($Years)-1; $i >= 0; --$i) {
				if ($Years[$i] === '')
					array_splice($Years, $i, 1);
			}

			$Genres = array_shift($datas);
			array_shift($Genres);

			$lines = [];
			for ($i=0; $i<count($datas);++$i)
			{
				$cat = array_shift($datas[$i] );
				$lines[$cat]= $datas[$i];
				for ($j=0; $j<count($lines[$cat]);++$j)
					$lines[$cat][$j] = intval($lines[$cat][$j]);
			}


			$effectifs = array(
				'years' => $Years,
				'genres' => $Genres,
				'categories' => $lines
			);
			return $effectifs;
		}
		catch (Exception $e) {
			error_log($e->getMessage());
		}
		return false;
	}

	public function effectifs() {
		Easqy::check_ajax_nonce();

		$effectifs = self::loadEffectifs();
		if ($effectifs === false)
		{
			wp_send_json_error();
		}
		else {
			$result = array( 'status' => 'ok', 'effectifs' => $effectifs );
			wp_send_json_success( $result );
		}
		die();
	}

	static public function loadRenouvellements()
	{
		require_once EASQY_DIR . '/includes/class-easqy-google-sheet-service.php';

		try {
			$sheets = Easqy_Google_Sheet_Service::getNewSheetService();

			$datas =$sheets->spreadsheets_values->get(self::SPREADSHEET_ID,
				'renouvellements!A1:C', ['majorDimension' => 'ROWS'])->getValues();


			$categories = [];
			/* remove label */ array_shift($datas);
			for ($l=0; $l<count($datas); ++$l ) {
				$c = array_shift($datas[ $l] );
				$line = $datas[$l];
				for ($i=0; $i<count($line);++$i)
					$line[$i] = intval($line[$i]);
				$categories[ $c ] = $line;
			}

			return $categories;
		}
		catch (Exception $e) {
			error_log($e->getMessage());
		}
		return false;
	}

	public function renouvellements()
	{
		Easqy::check_ajax_nonce();

		$r = self::loadRenouvellements();
		if ($r === false)
		{
			wp_send_json_error();
		}
		else {
			$result = array( 'status' => 'ok', 'renouvellements' => $r );
			wp_send_json_success( $result );
		}
		die();
/*
		$content = file_get_contents(self::RENOUVELLEMENTS_URL);
		$rows = explode(PHP_EOL, $content);

		if (!is_array( $rows ))
		{
			$result = array('status' => 'ko', 'message' => 'network error');
		}
		else if (count( $rows ) < 2)
		{
			$result = array('status' => 'ko', 'message' => 'bad format');
		}
		else
		{
			$s = array();

			$csv_years = str_getcsv($rows[0], ',');
			array_shift($rows);

			$years = array();
			foreach( $csv_years as $year) {
				if ($year == "") continue;
				$years []= intval($year);
			}
			$s ['years'] = $years;

			$cat_names = [];
			$cats = [];

			array_shift($rows);
			foreach($rows as $row) {
				$r = str_getcsv($row, ',');
				$cat_names []= array_shift($r);

				$cat = array();
				foreach($r as $e)
					$cat []= intval($e);

				$cats []= $cat;
			}

			$s['categories'] = array( 'names' => $cat_names, 'renouvellements' => $cats);

			$result = array('status' => 'ok', 'renouvellements' => $s);
		}

		wp_send_json_success( $result );
*/
	}

	public function geographiques() {
		Easqy::check_ajax_nonce();

		$content = file_get_contents(self::GEOGRAPHIQUES_URL);
		$rows = explode(PHP_EOL, $content);

		if (!is_array( $rows ))
		{
			$result = array('status' => 'ko', 'message' => 'network error');
		}
		else if (count( $rows ) < 2)
		{
			$result = array('status' => 'ko', 'message' => 'bad format');
		}
		else
		{
			$s = array();
			$e['ville']= array_shift($rows);

			foreach($rows as $row) {
				$r = str_getcsv($row, ',');
				$e = array();
				$e['ville']= $r[0];
				$e['effectif']= intval($r[1]);
				$e['inSQY']= intval($r[2]);
				$s []= $e;
			}

			$result = array('status' => 'ok', 'effectifs' => $s);
		}

		wp_send_json_success( $result );
		die();
	}

}