<?php


class EasqyEffectifsAjax {

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


	public function effectifs() {

		$content = file_get_contents(self::EFFECTIFS_URL);
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

			// M F Order
			$csv_genre = str_getcsv($rows[0], ',');
			array_shift($rows);

			$s ['order'] = array( $csv_genre[1], $csv_genre[2] );

			$cat_names = [];
			$cats = [];

			foreach($rows as $row) {
				$r = str_getcsv($row, ',');
				$cat_names []= array_shift($r);

				$cat = array();
				foreach($r as $e)
					$cat []= intval($e);

				$cats []= $cat;
			}

			$s['categories'] = array( 'names' => $cat_names, 'effectifs' => $cats);

			$result = array('status' => 'ok', 'effectifs' => $s);
		}

		wp_send_json_success( $result );
	}


	public function renouvellements()
	{
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
	}

	public function geographiques() {

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
	}

}