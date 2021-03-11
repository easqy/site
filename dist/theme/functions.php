<?php
#define('EASQY_THEME_ENVIRONMENT_PROD', '1');
define('EASQY_THEME_ENVIRONMENT_DEV', '1');


add_filter( 'widget_text', 'do_shortcode' );

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
if (defined('EASQY_0'))
{
    add_filter( 'hu_post_thumbnail_html', 'easqy_theme_post_thumbnail_html');
    function easqy_theme_post_thumbnail_html($html, $size = 'post-thumbnail', $attr= 'default')
    {
        return $html;
    }

    add_filter( 'hu-use-svg-thumb-placeholder', 'easqy_theme_use_svg_thumb_placeholder' );
    function easqy_theme_use_svg_thumb_placeholder($use)
    {
        return false;
    }

    add_filter( 'hu_placeholder_thumb_src', 'easqy_theme_placeholder_thumb_src');
    function easqy_theme_placeholder_thumb_src($img_src, $requested_size= 'post-thumbnail')
    {
        $post= get_post();
        $url = get_post_meta($post->ID, '_easqy_theme_gphoto_featured', true);
        if ($url)
            return $url . "=w980-h300-c";

        return $img_src;
    }

    add_filter( 'hu_placeholder_thumb_filter', 'easqy_theme_placeholder_thumb_filter' );
    function easqy_theme_placeholder_thumb_filter($use)
    {
        return false; //'<style>img.hu-img-placeholder{display:none}</style>';
    }

    // ---------------------------------------------------------------


    // see https://gist.github.com/carlodaniele/121841f92956c3304436

    function easqy_theme_build_meta_box( $post ){
        // our code here
        wp_nonce_field( basename( __FILE__ ), 'easqy_theme_meta_box_nonce' );

        $current_featured = get_post_meta( $post->ID, '_easqy_theme_gphoto_featured', true );

        ?>

        <h3><?php _e( 'Featured image [ideally 980x410px]', 'frachop-gphoto-gallery' ); ?></h3>
        <p>
            <label><?php _e( 'Url', 'frachop-gphoto-gallery' ); ?><br/>
            <input type="text" name="featured" value="<?php echo $current_featured; ?>" />
            </label>
        </p>

        <?php
    }

    function easqy_theme_add_meta_boxes( $post ){
        add_meta_box( 'easqy_theme_meta_box', __( 'Frachop Theme', 'frachop-gphoto-gallery' ), 'easqy_theme_build_meta_box', 'post', 'side', 'low' );
    }

    function easqy_theme_save_meta_box_data( $post_id, $post, $update )
    {
        // verify meta box nonce
        if ( !isset( $_POST['easqy_theme_meta_box_nonce'] ) || !wp_verify_nonce( $_POST['easqy_theme_meta_box_nonce'], basename( __FILE__ ) ) ){
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
                delete_post_meta( $post_id, '_easqy_theme_gphoto_featured' );
            else
                update_post_meta( $post_id, '_easqy_theme_gphoto_featured', sanitize_text_field( $_POST['featured'] ) );
        }
    }

    add_action( 'add_meta_boxes_post', 'easqy_theme_add_meta_boxes' );
    add_action( 'save_post', 'easqy_theme_save_meta_box_data', 10, 3 );
}

if (defined('EASQY_THEME_ENVIRONMENT_PROD'))
{
    function easqy_theme_header_metadata() {

        ?>
        <script type="text/javascript">
            if (location.protocol !== 'https:') {
                location.replace(`https:${location.href.substring(location.protocol.length)}`);
            }
        </script>
        <?php
    }
    add_action( 'wp_head', 'easqy_theme_header_metadata' );
}