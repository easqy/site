<?php


class Easqy_Records_Ajax
{
    public function define_hooks( $loader )
    {
        $loader->add_action('wp_ajax_easqy_records', $this, 'get_records' );
        $loader->add_action('wp_ajax_nopriv_easqy_records', $this, 'get_records' );
        $loader->add_action('wp_ajax_easqy_record_del', $this, 'del_record');
    }

    public function get_records() {

        $a = array();
        foreach( Easqy_Records_DB::athletes() as $row)
            $a []= array( 'i' => intval( $row['id'] ), 'n' => $row['nom'], 'p' => $row['prenom']);

        $r = array();
        foreach( Easqy_Records_DB::records() as $row)
            $r []= array(
                'i' => intval( $row['id'] ),
                'c' => intval($row['categorie']),
                'e' => intval($row['epreuve']),
                'fa' => Easqy_Records_Common::getFamily(intval($row['epreuve'])),
                'in' => intval($row['indoor']),
                'g' => intval($row['genre']),
                'd' => $row['date'],
                'l' => $row['lieu'],
                'p' => $row['performance'],
                'f' => $row['infos']
            );

        $ra = array();
        foreach( Easqy_Records_DB::recordsAthletes() as $row) {
            $item = array(
                'r' => intval($row['record']),
                'a' => intval($row['athlete'])
            );
            if ($row['categorie'])
                $item['c'] = intval($row['categorie']);

            $ra [] = $item;
        }

        $result = array(
            'status' => 'ok',
            'genres' => Easqy_Records_Common::GENRES,
            'categories' => Easqy_Records_Common::CATEGORIES,
            'epreuves' => Easqy_Records_Common::EPREUVES,
            'familles' => Easqy_Records_Common::EPREUVE_FAMILIES,
            'athletes' => $a,
            'records' => $r,
            'ra' => $ra
        );
        wp_send_json_success( $result );
    }

    public function del_record() {
        if (!isset($_POST['recordId']))
        {
            wp_send_json_error( array('status' => 'error', 'message' => 'no record id') );
            return;
        }

        $recordId = $_POST['recordId'];
        $result = Easqy_Records_DB::deleteRecord($recordId);

        $result = array(
            'status' => 'ok'
        );
        wp_send_json_success($result);
    }

}