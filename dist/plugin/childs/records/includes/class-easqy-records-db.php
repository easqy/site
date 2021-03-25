<?php


class Easqy_Records_DB
{
    private static function tableRecord($wpdb) { return  $wpdb->prefix . 'easqy_records_record'; }
    private static function tableAthlete($wpdb) { return  $wpdb->prefix . 'easqy_records_athlete'; }
    private static function tableRecordHasAthletes($wpdb) { return  $wpdb->prefix . 'easqy_records_record_has_athlete'; }
	private static function noquote($str) : string
	{
		$res= str_replace('\"', '"', $str);
		return str_replace("\'", "'", $res);
	}
	private static function noquoteAndTime($str) : string {
    	$res = self::noquote($str);
		//$res= str_replace('"', '&Prime;', $str);
		return str_replace("''", '"', $res);
	}


    public static function activate() {
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();
        $table_name = self::tableRecord($wpdb);

        $sql ="CREATE TABLE IF NOT EXISTS `$table_name` (
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
            `categorie` TINYINT UNSIGNED NOT NULL,
            `epreuve` TINYINT UNSIGNED NOT NULL,
            `environnement` TINYINT UNSIGNED NOT NULL,
            `genre` TINYINT UNSIGNED NOT NULL,
            `date` DATE NOT NULL,
            `lieu` VARCHAR(64) NOT NULL,
            `performance` VARCHAR(45) NOT NULL,
            `infos` VARCHAR(45) NULL,
            PRIMARY KEY (`id`)
            ) $charset_collate;";
        dbDelta( $sql );

        $table_name = self::tableAthlete($wpdb);
        $sql ="CREATE TABLE IF NOT EXISTS `$table_name` (
            `id` INT UNSIGNED NOT NULL,
            `nom` VARCHAR(45) NOT NULL,
            `prenom` VARCHAR(45) NOT NULL,
            PRIMARY KEY (`id`)
            ) $charset_collate;";
        dbDelta( $sql );

        $table_name = self::tableRecordHasAthletes($wpdb);
        $sql = "CREATE TABLE IF NOT EXISTS `$table_name` (
            `record` INT UNSIGNED NOT NULL,
            `athlete` INT UNSIGNED NOT NULL,
            `categorie` TINYINT NULL,
            PRIMARY KEY (`record`, `athlete`)
            ) $charset_collate;";
        dbDelta( $sql );

        $easqy_records_db_version = '1.0';
        add_option( 'easqy_records_db_version', $easqy_records_db_version );
    }

    public static function deactivate() {}

    public static function athletes() {
        global $wpdb;
        $t = self::tableAthlete($wpdb);
        return $wpdb->get_results( "SELECT * FROM `$t`", ARRAY_A );
    }

    public static function records() {
        global $wpdb;
        $t = self::tableRecord($wpdb);
        return $wpdb->get_results( "SELECT * FROM `$t`", ARRAY_A );
    }

    public static function recordsAthletes() {
        global $wpdb;
        $t = self::tableRecordHasAthletes($wpdb);
        return $wpdb->get_results( "SELECT * FROM `$t`", ARRAY_A );
    }

    public static function deleteRecord($recordId) {
        global $wpdb;

        $res1 = $wpdb->delete( self::tableRecord($wpdb), array( 'id' => intval( $recordId )), array('%d') );
        $res2 = $wpdb->delete( self::tableRecordHasAthletes($wpdb), array( 'record' => intval( $recordId )), array('%d') );

        //purge athletes ??
        return ($res1 !== false) && ($res2 !== false);
    }

	public static function saveRecord( $record ) : bool {
    	global $wpdb;

		$date = sprintf('%04d-%02d-%02d',
			intval($record['date']['y']),
			intval($record['date']['m'])+1,intval($record['date']['d'])
		);

		$r = array(
			'categorie'=> intval( $record['categorie'] ),
			'epreuve'=> intval( $record['epreuve'] ),
			'environnement'=> intval( $record['environnement'] ),
			'genre'=> intval( $record['genre'] ),
			'date'=> $date,
			'lieu'=> self::noquote($record['lieu']),
			'performance'=> self::noquoteAndTime($record['perf']),
			'infos'=> self::noquote($record['infos'])
		);

		if ( intval($record['id'] >= 0) )
		{
			$result = $wpdb->update( self::tableRecord( $wpdb ), $r, array( 'id' => intval( $record['id'] ) ) );
			if ( $result === false )
				return false;

		} else {
			$result= $wpdb->insert( self::tableRecord($wpdb), $r);
			if ( $result === false )
				return false;

			$record['id'] = $wpdb->insert_id;
		}

		// 1. delete ras
		$wpdb->delete( self::tableRecordHasAthletes($wpdb), array('record' => $record['id']) );

		// 2. eventually create athletes and add ra

		$athletes = $record['athletesDuRecord'];
		foreach ($athletes as $athlete) {
			$ra=array('record' => intval($record['id']));
			if (intval( $athlete['catWhenPerf']) < 0)
				$ra['categorie'] = null;
			else
				$ra['categorie'] = intval( $athlete['catWhenPerf']);

			if (intval($athlete['athlete']) > 0) {
				$ra['athlete'] = intval( $athlete['athlete'] );
			} else {
				// create athlete
				$parts = explode(' ', $athlete['athlete'], 2);
				if (is_array($parts) && (count($parts)>0))
				{
					$a= array(
						'nom' => $parts[0],
						'prenom' => count($parts) > 1 ? $parts[1] : ''
					);
					$result= $wpdb->insert( self::tableAthlete($wpdb), $a);
					$ra['athlete'] = $wpdb->insert_id;
				}
			}
			$result= $wpdb->insert( self::tableRecordHasAthletes($wpdb), $ra);

		}
		return true;

	}

}