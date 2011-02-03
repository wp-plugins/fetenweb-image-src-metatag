<?php
/*
Plugin Name:  FetenWeb image_src Metatag
Plugin URI:   http://fetenweb.com/fetenweb-image-src-metatag-plugin-for-wordpress
Description:  Adds an image_src metatag to your header using post thumbnnail, the first image on post/page content, or a defined default image. So helps sites like FaceBook use a relevant image as thumbnail when sharing content.
Version:      1.1
Author:       Aitor de la Puente
Author URI:   http://fetenweb.com
License:      GPL 2

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License v2 as published by
	the Free Software Foundation; either version 2 of the License, or
	(at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

*/


add_action('wp_head', 'fetenweb_image_src_metatag');
add_action('admin_menu', 'fetenweb_image_src_metatag_admin_menu');


function fetenweb_image_src_metatag() {
	
	global $post;
	
	if (is_single() || is_page()) {
		
		if (function_exists("has_post_thumbnail") && has_post_thumbnail($post->ID)) {
			$thumb_id = get_post_thumbnail_id($post->ID);
			$image = wp_get_attachment_image_src($thumb_id);
			$image = $image[0];
		}
		
		if (!$image) {
			preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $post->post_content, $matches);
			$image = $matches[1][0];
		}
		
	}
	
	if (!$image) {
		if(get_option('fetenweb_image_src_metatag_default') != '')
			$image = get_option('fetenweb_image_src_metatag_default');
	}
	
	if ($image) {
		if (strpos($image, '../') === 0) $image = substr($image,3);
		if (strpos($image, '/') === 0)   $image = substr($image,1);
		if (strpos($image,'http://') !== 0 && strpos($image, get_bloginfo('url')) !== 0 && $image != get_option('fetenweb_image_src_metatag_default'))
			$image = (get_bloginfo('url')) . '/' . $image;
		echo '<link rel="image_src" href="' . esc_attr($image) . '"></link>';
		echo '<meta property="og:image" content="' . esc_attr($image) . '"/>';
	}
	
}


function fetenweb_image_src_metatag_admin_menu() {
  add_options_page('FetenWeb image_src Metatag Options', 'FetenWeb image_src Metatag Options', 'manage_options', 'FetenWeb-image_src-Metatag-Options', 'fetenweb_image_src_metatag_options');
}


function fetenweb_image_src_metatag_options() {
	
	if (!current_user_can('manage_options')) {
		wp_die( __('You do not have sufficient permissions to access this page.') );
	}
	
	?>
	<div class="wrap">
		<h2>FetenWeb image_src Metatag Options</h2>
		<form method="post" action="options.php">
			<?php wp_nonce_field('update-options'); ?>
			<table class="form-table">
				<tr valign="top">
					<th scope="row"><label for="option_fetenweb_image_src_metatag_default">Default image:</label></th>
					<td>
						<input type="text" size="90" id="option_fetenweb_image_src_metatag_default" name="fetenweb_image_src_metatag_default" value="<?php echo get_option('fetenweb_image_src_metatag_default'); ?>" /><br />
						<i>Absolute path</i>
					</td>
				</tr>
			</table>
			<input type="hidden" name="action" value="update" />
			<input type="hidden" name="page_options" value="fetenweb_image_src_metatag_default" />
			<p class="submit">
				<input type="submit" value="Update Options" />
			</p>
		</form>
	</div>
	<?php
	
}

?>
