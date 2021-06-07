<?php


class Easqy_Trombinoscope_Activator
{
    public static function activate() {

        require_once EASQY_CHILDS_DIR . '/trombinoscope/admin/class-easqy-trombinoscope-db.php';
        Easqy_Trombinoscope_DB::activate();
    }

}