<?php


class Easqy_Records_Ajax
{
    public function define_hooks( $loader )
    {
	    // nopriv for users that are not logged in
        $loader->add_action('wp_ajax_easqy_records', $this, 'get_records' );
        $loader->add_action('wp_ajax_nopriv_easqy_records', $this, 'get_records' );

	    if (is_admin()) {
		    $loader->add_action( 'wp_ajax_easqy_record_del', $this, 'del_record' );
		    $loader->add_action( 'wp_ajax_easqy_record_save', $this, 'save_record' );

		    $loader->add_action( 'wp_ajax_easqy_record_users', $this, 'users' );
		    $loader->add_action( 'wp_ajax_easqy_record_user_add', $this, 'user_add' );
		    $loader->add_action( 'wp_ajax_easqy_record_user_remove', $this, 'user_remove' );
	    }
    }

    static private function check_nonce(){
	    if ( ! check_ajax_referer( 'record_admin_nonce', 'security', false ) ) {

		    wp_send_json_error( 'Invalid security token sent.' );
		    wp_die();
	    }
	}

    public function get_adm_records() {

		self::check_nonce();
	    return $this->get_records();
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
                'en' => intval($row['environnement']),
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
            'environnements' => Easqy_Records_Common::ENVIRONNEMENT,
            'epreuves' => Easqy_Records_Common::EPREUVES,
            'familles' => Easqy_Records_Common::FAMILIES,
            'athletes' => $a,
            'records' => $r,
            'ra' => $ra
        );
        wp_send_json_success( $result );
    }

    public function del_record() {

	    self::check_nonce();

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

	public function save_record() {
		self::check_nonce();
		if (!isset($_POST['record']))
		{
			wp_send_json_error( array('status' => 'error', 'message' => 'no record') );
			return;
		}
		if (!is_array($_POST['record']))
		{
			wp_send_json_error( array('status' => 'error', 'message' => 'no record') );
			return;
		}

		Easqy_Records_DB::saveRecord($_POST['record']);

		$result = array(
			'status' => 'ok, saved'
		);
		wp_send_json_success($result);
	}

	public function users() {
		self::check_nonce();

		$result= array();
		foreach (get_users() as $u)
		{
			if ($u->ID !== 1) {

				$user = [ 'i' => $u->ID, 'd' => $u->display_name ];
				$user['a'] = ((get_current_user_id() === $u->ID) || in_array( 'administrator', (array) $u->roles )) ? 1 : 0;
				$user['c'] = $u->has_cap(Easqy_Records::MANAGE_CAPABILITY) ? 1 : 0;
				$result []= $user;
			}

			//$u->remove_cap(Easqy_Records::MANAGE_CAPABILITY);
		}

		wp_send_json_success(array(
			'status' => 'ok', 'users' => $result
		));
	}

	public function user_add() {
		self::check_nonce();

		if (!isset($_POST['userId'])){
			wp_send_json_error( 'no user id' );
			return;
		}

		$userId= intval($_POST['userId']);
		if ($userId <= 0 ){
			wp_send_json_error( 'no user id' );
			return;
		}

		$u = get_user_by('id', $userId);
		if (!$u) {
			wp_send_json_error( 'no user with this id ('.$userId.')' );
			return;
		}

		$u->add_cap(Easqy_Records::MANAGE_CAPABILITY);

		wp_send_json_success(array(
			'status' => 'ok'
		));
	}

	public function user_remove() {
		self::check_nonce();

		if (!isset($_POST['userId'])){
			wp_send_json_error( 'no user id' );
			wp_die();
		}

		$userId= intval($_POST['userId']);
		if ($userId <= 0 ){
			wp_send_json_error( 'no user id' );
			wp_die();
		}

		$u = get_user_by('id', $userId);
		if (!$u) {
			wp_send_json_error( 'no user with this id ('.$userId.')' );
			wp_die();
		}

		if ($u->ID === get_current_user_id() )
		{
			wp_send_json_error( "can't remove me ($userId)" );
			wp_die();
		}

		$u->remove_cap(Easqy_Records::MANAGE_CAPABILITY);

		wp_send_json_success(array(
			'status' => 'ok'
		));
		wp_die();
	}

}