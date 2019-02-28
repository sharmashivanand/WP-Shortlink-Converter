<?php

function bt_replace() {
	$post_id = get_the_id();
	if ( 'page' == get_post_type($post_id) || 'post' == get_post_type($post_id)) { // If we are on a post or a page (we want to avoid attachments etc.)
		$content = get_the_content(); // get the unfiltered content
		$siteurl = get_site_url(); // get the address of the current site
		$siteurl = trailingslashit($siteurl);
		$siteurl = preg_quote($siteurl); // 
		$exp = "/href=["|']($siteurl[^"']+)/";
		
		$content = preg_replace_callback($exp, 'bt_replace_url', $content); // replacement magic. Match urls begining with our WP site.	

		if(is_user_logged_in()) { 
			wp_update_post( array('ID' => $post_id, 'post_content' => $content) ,$error); // update the post with the new content
			bt_log($error);
		}
	}
}

function bt_replace_url($matches) {
	$postid = url_to_postid( $matches[1] ); // get the post id from the matched url
	if ( $postid && ('page' == get_post_type($postid) || 'post' == get_post_type($postid))){ // If this is the link to a post or page (we want to avoid attachments etc.)
		$shorturl = wp_get_shortlink($postid); // convert it into shorturl
		return str_replace($matches[1], $shorturl, $matches[0]);
	}
	return $matches[0];
}

add_action('genesis_before_entry','bt_replace'); // any hook within the loop should do

function bt_log($str) {
	echo '<pre>';
	print_r($str);
	echo '</pre>';
}
