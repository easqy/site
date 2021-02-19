<?php

add_action( 'wp_enqueue_scripts', 'my_theme_enqueue_styles' );
function my_theme_enqueue_styles() {

    $parent_style = 'parent-style'; // This is 'twentyfifteen-style' for the Twenty Fifteen theme.
 
    wp_enqueue_style( $parent_style, get_template_directory_uri() . '/style.css' );
    wp_enqueue_style( 'child-style',
        get_stylesheet_directory_uri() . '/style.css',
        array( $parent_style ),
        wp_get_theme()->get('Version')
    );
 
}
/*
add_action('wp_head', 'frachop_wp_head');
function frachop_wp_head(){
    //Close PHP tags 
    ?>
<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-156389407-1"></script>
<script>
	window.dataLayer = window.dataLayer || [];
	function gtag(){dataLayer.push(arguments);}
	gtag('js', new Date());

	gtag('config', 'UA-156389407-1');
</script>
	<?php //Open PHP tags
}
*/


add_filter( 'hu_post_thumbnail_html', 'frachop_post_thumbnail_html');
function frachop_post_thumbnail_html($html, $size = 'post-thumbnail', $attr= 'default')
{
	return $html;
}

add_filter( 'hu-use-svg-thumb-placeholder', 'frachop_use_svg_thumb_placeholder' );
function frachop_use_svg_thumb_placeholder($use)
{
	return false;	
}

add_filter( 'hu_placeholder_thumb_src', 'frachop_placeholder_thumb_src');
function frachop_placeholder_thumb_src($img_src, $requested_size= 'post-thumbnail')
{
	$post= get_post();
	$url = get_post_meta($post->ID, '_frachop_gphoto_featured', true);
	if ($url)
		return $url . "=w980-h300-c";

	return $img_src;
}

add_filter( 'hu_placeholder_thumb_filter', 'frachop_placeholder_thumb_filter' );
function frachop_placeholder_thumb_filter($use)
{
	return false; //'<style>img.hu-img-placeholder{display:none}</style>';
}

// ---------------------------------------------------------------


// see https://gist.github.com/carlodaniele/121841f92956c3304436

function frachop_build_meta_box( $post ){
	// our code here
	wp_nonce_field( basename( __FILE__ ), 'frachop_meta_box_nonce' );

	$current_featured = get_post_meta( $post->ID, '_frachop_gphoto_featured', true );

	?>

	<h3><?php _e( 'Featured image [ideally 980x410px]', 'frachop-gphoto-gallery' ); ?></h3>
	<p>
		<label><?php _e( 'Url', 'frachop-gphoto-gallery' ); ?><br/>
		<input type="text" name="featured" value="<?php echo $current_featured; ?>" /> 
		</label>
	</p>

	<?php
}

function frachop_add_meta_boxes( $post ){
	add_meta_box( 'frachop_meta_box', __( 'Frachop Theme', 'frachop-gphoto-gallery' ), 'frachop_build_meta_box', 'post', 'side', 'low' );
}

function frachop_save_meta_box_data( $post_id, $post, $update )
{
	// verify meta box nonce
	if ( !isset( $_POST['frachop_meta_box_nonce'] ) || !wp_verify_nonce( $_POST['frachop_meta_box_nonce'], basename( __FILE__ ) ) ){
		return;
	}

	// Check the user's permissions.
	if ( ! current_user_can( 'edit_post', $post_id ) ){
		return;
	}

	// store custom fields values
	// cholesterol string
	if ( isset( $_REQUEST['featured'] ) ) {
		if ($_REQUEST['featured'] === '')
			delete_post_meta( $post_id, '_frachop_gphoto_featured' );
		else
			update_post_meta( $post_id, '_frachop_gphoto_featured', sanitize_text_field( $_POST['featured'] ) );
	}
}

add_action( 'add_meta_boxes_post', 'frachop_add_meta_boxes' );
add_action( 'save_post', 'frachop_save_meta_box_data', 10, 3 );
