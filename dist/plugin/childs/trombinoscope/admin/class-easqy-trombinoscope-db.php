<?php

class Easqy_Trombinoscope_DB
{
    private static function tableTrombinoscope($wpdb) { return  $wpdb->prefix . 'easqy_trombinoscope'; }

    private static function normalizeFields($result)
	{
		$intFields= ['trombinoscope_id', 'user_id', 'photo_id', 'dirigeant'];

		if (is_array($result)) {
			for ($i=0; $i<count($result);++$i) {
				foreach ($intFields as $f)
					if (key_exists($f, $result[$i]))
						$result[$i][$f] = ($result[$i][$f] === null) ? null : intval($result[$i][$f]);
			}
		}

		 return $result;
	}


    public static function activate() {
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();
        $table_name = self::tableTrombinoscope($wpdb);

		$sql ="CREATE TABLE `$table_name` (
			`trombinoscope_id` int(10) UNSIGNED NOT NULL,
			`user_id` int(10) UNSIGNED DEFAULT NULL,
			`nom` varchar(64) NOT NULL,
			`prenom` varchar(64) NOT NULL,
			`topo` text DEFAULT NULL,
			`photo_id` int(10) UNSIGNED DEFAULT NULL,
			`dirigeant` tinyint(1) NOT NULL DEFAULT 0,
			`dirigeant_poste` varchar(64) DEFAULT NULL,
			PRIMARY KEY (`trombinoscope_id`),
			INDEX `dirigeant` (`dirigeant`)
			) $charset_collate;";
        dbDelta( $sql );

        $easqy_trombinoscope_db_version = '1.0';
        add_option( 'easqy_trombinoscope_db_version', $easqy_trombinoscope_db_version );
    }

    public static function deactivate() {}

	public static function trombines() {
        global $wpdb;
        $t = self::tableTrombinoscope($wpdb);
        return self::normalizeFields($wpdb->get_results( "SELECT * FROM `$t`", ARRAY_A ));
    }
	public static function dirigeants() {
        global $wpdb;
        $t = self::tableTrombinoscope($wpdb);
        return self::normalizeFields($wpdb->get_results( "SELECT * FROM `$t` WHERE `dirigeant` != 0 ", ARRAY_A ));
    }

}