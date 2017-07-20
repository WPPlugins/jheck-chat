<?php
/**
 * Shortcode chat form.
 * [jheck_chat]
 * @since 1.0
 * Removed, automatically inserted on footer.
 * @since 1.1
 */

function jc_shortcode_function(){
	
	if (is_user_logged_in()) {
		return '<p style="font-size: 10px;">Shortcode [jheck_chat] removed since Ver. 1.1 as it is already declared at the footer of your site. Kindly remove the declared shortcode inside your template.</p>';
	}
	return null;
	
}
add_shortcode( 'jheck_chat', 'jc_shortcode_function' );