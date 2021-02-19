<?php


class Easqy_Records_Deactivator
{
    public static function deactivate() {

        require_once EASQY_DIR . '/records/includes/class-easqy-records-db.php';
        Easqy_Records_DB::deactivate();

        wp_roles()->remove_cap( 'editor',Easqy_Records::MANAGE_CAPABILITY);
        wp_roles()->remove_cap( 'administrator', Easqy_Records::MANAGE_CAPABILITY);
    }

}