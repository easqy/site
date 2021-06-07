<?php

class Easqy_Effectifs_public
{
	const APEX_SCRIPT_STYLE_HANDLE = EASQY_NAME . '-apexcharts';
	const SCRIPT_STYLE_HANDLE = EASQY_NAME . '-effectifs-pub';
	private static $codeIndex = 0;

    public function __construct(Easqy $easqy) {

        $this->load_dependencies();
        $this->define_hooks($easqy->get_loader());
    }

    private function load_dependencies() {
    }

    private function define_hooks(Easqy_Loader $loader)
    {
        $loader->add_shortcode('easqy_effectifs', $this, 'shortcode');
	    $loader->add_action('wp_enqueue_scripts', $this, 'enqueue_styles');
	    $loader->add_action('wp_enqueue_scripts', $this, 'enqueue_scripts');

    }

	public function enqueue_styles() {
		wp_register_style(
			self::SCRIPT_STYLE_HANDLE,
			plugins_url( 'js/index.css', __FILE__ ), []
		);
	}

	public function enqueue_scripts() {

		wp_register_script( self::APEX_SCRIPT_STYLE_HANDLE,
			'https://cdn.jsdelivr.net/npm/apexcharts', null, null, true );

		wp_register_script(
			self::SCRIPT_STYLE_HANDLE,
			plugins_url( 'js/index.js', __FILE__ ),
			array('jquery', 'wp-polyfill'),
			time(), true );
	}

    public function shortcode( $atts = [], $content = null, $tag = '') {

	    self::$codeIndex = self::$codeIndex + 1;
	    $type = ( (is_array($atts) && array_key_exists ( 'type', $atts)) ? $atts['type'] : 'evolution_globale');
	    $title= (is_string($content) ? $content : '');

	    ob_start();
	    echo '
		<div class="easqy-effectifs-container">
			<div id="easqy-effectifs-' . self::$codeIndex . '" easqy-effectifs-index="' . self::$codeIndex
	            . '" class="easqy-effectifs">
				Initialisation ...
			</div>
		</div>
	';

	    wp_enqueue_script(self::APEX_SCRIPT_STYLE_HANDLE);
	    wp_enqueue_script(self::SCRIPT_STYLE_HANDLE);
	    wp_enqueue_style(self::SCRIPT_STYLE_HANDLE);

	    $inlineCode = '';
	    if (self::$codeIndex === 1)
	    {
		    $inlineCode = 'easqy.effectifs=';
		    $inlineCode.= json_encode( [
			    'charts' => array()
		    ] ) ;
		    $inlineCode.= ';';
	    }
	    $inlineCode .= 'easqy.effectifs.charts.push('
	                   .	json_encode( array( 'type' => $type, 'title' => $title))
	                   .	');';

	    $res = wp_add_inline_script(self::SCRIPT_STYLE_HANDLE, $inlineCode,'before');
	    return ob_get_clean();

    }

}
