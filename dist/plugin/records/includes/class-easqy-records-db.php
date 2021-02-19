<?php


class Easqy_Records_DB
{
    private static function tableRecord($wpdb) { return  $wpdb->prefix . 'easqy_records_record'; }
    private static function tableAthlete($wpdb) { return  $wpdb->prefix . 'easqy_records_athlete'; }
    private static function tableRecordHasAthletes($wpdb) { return  $wpdb->prefix . 'easqy_records_record_has_athlete'; }

    public static function activate() {
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();
        $table_name = self::tableRecord($wpdb);

        $sql ="CREATE TABLE IF NOT EXISTS `$table_name` (
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
            `categorie` TINYINT UNSIGNED NOT NULL,
            `epreuve` TINYINT UNSIGNED NOT NULL,
            `indoor` TINYINT UNSIGNED NOT NULL,
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
        $res1 = $wpdb::delete( tableRecord($wpdb), array( ‘id’ => intval( $recordId ),  array( "%d" ) ));
        $res2 = $wpdb::delete( tableRecordHasAthletes($wpdb), array( ‘record’ => intval( $recordId ),  array( "%d" ) ));
        //purge athletes ??
        return ($res1 !== false) && ($res2 !== false);
    }

}