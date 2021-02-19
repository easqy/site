<?php


class Easqy_Records_Activator
{
    public static function activate() {

        require_once EASQY_DIR . '/records/includes/class-easqy-records-db.php';
        Easqy_Records_DB::activate();

        //update_user_meta(3, 'easqy_records_can_manage', '1');

        //$role = get_role('editor');
        //$role->add_cap(Easqy_Records::MANAGE_CAPABILITY, true);
        //$role = get_role('administrator');
        //$role->add_cap(Easqy_Records::MANAGE_CAPABILITY, true);
/*
        wp_roles()->add_cap( 'editor',Easqy_Records::MANAGE_CAPABILITY, true );
        wp_roles()->add_cap( 'administrator', Easqy_Records::MANAGE_CAPABILITY, true );
*/
    }

}