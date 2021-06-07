<?php

class Easqy_Trombinoscope_Ajax {

	public function __construct(Easqy $easqy) {

		$loader = $easqy->get_loader();

		$loader->add_action( 'wp_ajax_easqy_dirigeants', $this, 'dirigeants' );
		$loader->add_action( 'wp_ajax_nopriv_easqy_dirigeants', $this,'dirigeants' );

		if (is_admin())
			$loader->add_action( 'wp_ajax_easqy_adm_trombi', $this,'adm_trombi');
	}

	public function dirigeants() {
		Easqy::check_ajax_nonce();

		require_once __DIR__ . '/class-easqy-trombinoscope-db.php';
		$dirigeants = Easqy_Trombinoscope_DB::dirigeants();

		$team = array();
		foreach ($dirigeants as $dirigeant)
		{
			if ($dirigeant['photo_id']) {
				$attachement = wp_get_attachment_image_src( intval($dirigeant['photo_id']), [300,300] );
				if (is_array($attachement))
					$dirigeant['img'] = $attachement[0]; 
			}
			$team []= $dirigeant;
		}
		$result = array( 'status' => 'ok', 'dirigeants' => $team );
		wp_send_json_success( $result );
		die();
	}

	public function adm_trombi() {
        Easqy::check_ajax_nonce();

		$command = 'list_all';
        if (isset($_REQUEST['command'])) {
            $command = $_REQUEST['command'];
        }

		require_once __DIR__ . '/class-easqy-trombinoscope-db.php';

		$result = array();
        switch ($command) {

			case 'list_all':
                $result = Easqy_Trombinoscope_DB::trombines();
                break;

            default:
                wp_send_json_error();
        }

        wp_send_json_success($result);
		die();
    }

}