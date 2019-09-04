<?php

/* Meta boxes */

function envisioned_add_metabox(){
	add_meta_box("et_post_meta", "ET Settings", "envisioned_display_metabox_settings", "post", "normal", "high");
}
add_action("admin_init", "envisioned_add_metabox");

function envisioned_display_metabox_settings() {
	global $post;

	$temp_array = array();

	$temp_array = maybe_unserialize(get_post_meta($post->ID,'et_envisioned_settings',true));
	$et_post_type = isset( $temp_array['et_post_type'] ) ? (int) $temp_array['et_post_type'] : 1;
	$et_custom_post_video = get_post_meta($post->ID,'et_videolink',true) ? get_post_meta($post->ID,'et_videolink',true) : '';
	$et_custom_portrait_detection = get_post_meta($post->ID,'et_image_detection',true) ? (bool) get_post_meta($post->ID,'et_image_detection',true) : 0;
	$et_custom_image_type = get_post_meta($post->ID,'et_imagetype',true) ? get_post_meta($post->ID,'et_imagetype',true) : 'l';

	wp_nonce_field( basename( __FILE__ ), 'et_settings_nonce' );
?>

	<div id="et_custom_settings" style="margin: 13px 0 17px 4px;">
		<p style="font-weight: bold; padding-bottom: 7px;">This is a:</p>
		<label title="Blog post"><input type="radio" name="et_post_type" value="1"<?php if ($et_post_type == 1) echo ' checked="checked"'; ?>> <span>Blog post</span></label><br/><br/>
		<label title="Photo post"><input type="radio" name="et_post_type" value="2"<?php if ($et_post_type == 2) echo ' checked="checked"'; ?>> <span>Photo post</span></label><br/><br/>
		<label title="Video post"><input type="radio" name="et_post_type" value="3"<?php if ($et_post_type == 3) echo ' checked="checked"'; ?>> <span>Video post</span></label>
	</div> <!-- #et_custom_settings -->

	<div style="margin: 13px 0 26px 4px;">
		<label for="et_custom_post_video" style="color: #000; font-weight: bold;"> Video url: </label>
		<input type="text" style="width: 30em;" value="<?php echo esc_url($et_custom_post_video); ?>" id="et_custom_post_video" name="et_custom_post_video" size="67" />
		<br />
		<small style="position: relative; top: 8px;">ex: <code><?php echo htmlspecialchars("http://www.youtube.com/watch?v=WkuHbkaieZ4");?></code></small>
	</div>

	<div style="margin: 13px 0 26px 4px;">
		<label class="selectit" for="et_custom_portrait_detection" style="font-weight: bold; position: relative; top: 6px; margin-bottom: 10px;">
			<input type="checkbox" name="et_custom_portrait_detection" id="et_custom_portrait_detection" value=""<?php checked( $et_custom_portrait_detection ); ?> /> Disable Portrait Image Detection
		</label>
	</div>

	<div style="margin: 13px 0 17px 4px;">
		<p style="font-weight: bold; padding-bottom: 7px;">This is:</p>
		<label title="Landscape Image"><input type="radio" name="et_custom_image_type" value="l"<?php if ($et_custom_image_type == 'l') echo ' checked="checked"'; ?>> <span>Landscape Image</span></label><br/><br/>
		<label title="Portrait Image"><input type="radio" name="et_custom_image_type" value="p"<?php if ($et_custom_image_type == 'p') echo ' checked="checked"'; ?>> <span>Portrait Image</span></label><br/><br/>
		<small>make sure you checked 'Disable Portrait Image Detection' to use this option</small>
	</div> <!-- #et_custom_settings -->

	<?php
}

add_action( 'save_post', 'envisioned_save_metabox_settings', 10, 2 );
function envisioned_save_metabox_settings( $post_id, $post ){
	global $pagenow;

	if ( 'post.php' != $pagenow ) return $post_id;

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
		return $post_id;

	$post_type = get_post_type_object( $post->post_type );
	if ( ! current_user_can( $post_type->cap->edit_post, $post_id ) )
		return $post_id;

	if ( !isset( $_POST['et_settings_nonce'] ) || ! wp_verify_nonce( $_POST['et_settings_nonce'], basename( __FILE__ ) ) )
        return $post_id;

	$temp_array = array();

	if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE )
		return $post_id;

	$temp_array['et_post_type'] = isset($_POST["et_post_type"]) ? (int) $_POST["et_post_type"] : 1;
	update_post_meta( $post_id, "et_envisioned_settings", $temp_array );

	$et_custom_post_video = isset($_POST["et_custom_post_video"]) ? esc_url_raw($_POST["et_custom_post_video"]) : '';
	update_post_meta( $post_id, "et_videolink", $et_custom_post_video );

	$et_custom_portrait_detection = isset( $_POST["et_custom_portrait_detection"] ) ? 1 : 0;
	update_post_meta( $post_id, "et_image_detection", (int) $et_custom_portrait_detection );

	$et_custom_image_type = isset($_POST["et_custom_image_type"]) ? sanitize_text_field($_POST["et_custom_image_type"]) : 'l';
	update_post_meta( $post_id, "et_imagetype", $et_custom_image_type );
}