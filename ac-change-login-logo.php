<?php
/*
Plugin Name: AC Change Login Logo
Plugin URI: https://antoniocampos.net/2009/02/26/wordpress-login-form-image/
Description: Change the Login screen logo. After activate go to Appearance -> Login logo and choose your logo.
Author: Antonio Campos
Version: 1.0.1
Author URI: https://antoniocampos.net
Text Domain: ac-change-login-logo
Domain Path: /languages
License:     GPL2

Copyright 2009-2024  Antonio Campos  (email : jantoniofcampos@sapo.pt)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

if (!defined('WPINC')) {
    die;
}

require_once plugin_dir_path(__FILE__) . 'config.php';
add_action('admin_menu', 'ac_login_logo_admin_menu');
add_action('login_head', 'ac_login_css');
add_filter('login_headerurl', 'ac_login_logo_link');
add_filter('login_headertitle', 'ac_login_logo_link_title');

function ac_change_login_logo_load_plugin_textdomain() {
    load_plugin_textdomain( 'ac-change-login-logo', FALSE, basename( dirname( __FILE__ ) ) . '/languages/' );
}

add_action( 'plugins_loaded', 'ac_change_login_logo_load_plugin_textdomain' );

function ac_login_css()
{
    $uploaded_login_logo = esc_url(wp_get_attachment_url(get_option(AC_LOGIN_LOGO_ID)));
    if ($uploaded_login_logo != "") {
        echo '<style type="text/css">body.login div#login h1 a { background-image: url(' . $uploaded_login_logo . ') !important; }</style>';
    }
}

function ac_login_logo_link()
{
    return get_bloginfo('url');
}

function ac_login_logo_link_title()
{
    return '';
}

function ac_login_logo_admin_menu()
{
    if (is_admin()) {
        add_theme_page(__('Choose Login Logo', 'ac-change-login-logo'), __('Login Logo', 'ac-change-login-logo'), 'manage_options', 'ac_login_logo_admin_menu', 'ac_login_logo_admin_page');
    }
}

function ac_login_logo_admin_page()
{
    if (is_admin()) {
        add_action('admin_footer', 'ac_login_logo_admin_css');
        echo '<h3>' . esc_html__('Choose the image to use as login Logo', 'ac-change-login-logo') . '</h3>';
        if (isset($_POST['submit_image_selector']) && isset($_POST['image_attachment_id'])):
            update_option(AC_LOGIN_LOGO_ID, absint($_POST['image_attachment_id']));
        endif;
        wp_enqueue_media();
        ?><form method='post'>
    <div class='image-preview-wrapper'>
	<?php $uploaded_login_logo = esc_url(wp_get_attachment_url(get_option(AC_LOGIN_LOGO_ID)));?>
    <img id='image-preview'  height='100px' src='<?php echo $uploaded_login_logo; ?>'>
    </div>
    <input id="upload_image_button" type="button" class="button" value="<?php esc_attr_e('Upload image', 'ac-change-login-logo');?>" />
    <input type='hidden' name='image_attachment_id' id='image_attachment_id' value='<?php echo esc_attr(get_option(AC_LOGIN_LOGO_ID)); ?>'>
    <input type="submit" name="submit_image_selector" value="<?php esc_attr_e('Save', 'ac-change-login-logo');?>" class="button-primary">
</form> <?php ;
    }
}

function ac_login_logo_admin_css()
{
    if (is_admin()) {
        $ac_login_logo_attachment_id = get_option(AC_LOGIN_LOGO_ID, 0);?><script type='text/javascript'>
		jQuery( document ).ready( function( $ ) {
			var file_frame;
			var wp_media_post_id = wp.media.model.settings.post.id; 
			var set_to_post_id = <?php echo $ac_login_logo_attachment_id; ?>;
			jQuery('#upload_image_button').on('click', function( event ){
				event.preventDefault();
				if ( file_frame ) {
					file_frame.uploader.uploader.param( 'post_id', set_to_post_id );
					file_frame.open();
					return;
				} else {
					wp.media.model.settings.post.id = set_to_post_id;
				}
				file_frame = wp.media.frames.file_frame = wp.media({
					title: "<?php _e('Select a image to upload', 'ac-change-login-logo');?>",
					button: {
						text: "<?php _e('Use this image', 'ac-change-login-logo');?>",
					},
					multiple: false	
				});
				file_frame.on( 'select', function() {
					attachment = file_frame.state().get('selection').first().toJSON();
					$( '#image-preview' ).attr( 'src', attachment.url ).css( 'width', 'auto' );
					$( '#image_attachment_id' ).val( attachment.id );
					wp.media.model.settings.post.id = wp_media_post_id;
				});
					file_frame.open();
			});
			jQuery( 'a.add_media' ).on( 'click', function() {
				wp.media.model.settings.post.id = wp_media_post_id;
			});
		});
	</script>

<?php }}
