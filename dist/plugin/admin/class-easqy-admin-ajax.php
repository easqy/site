<?php

class Easqy_Admin_Ajax {

	public function __construct(Easqy $easqy) {

		$loader = $easqy->get_loader();
		$loader->add_action( 'wp_ajax_easqy_image_from_media_lib', $this, 'image_from_media_lib' );
		$loader->add_action( 'wp_ajax_nopriv_easqy_image_from_media_lib', $this,'image_from_media_lib' );
	}

	public function image_from_media_lib()
	{
		Easqy::check_ajax_nonce();

		if (isset($_GET['id']))
		{
			$imageId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
			$imgSize = 'medium';
			if (isset($_GET['size']))
				$imgSize = $_GET['size'];

			$image = wp_get_attachment_image(
				$imageId,
				$imgSize,
				false,
				array( /*'id' => 'image_preview' */)
			);
			$data = array(
				'image' => $image,
			);
			error_log( print_r($_GET, true));
			wp_send_json_success($data);
		} else {
			wp_send_json_error();
		}
		die();

    }

}