<?php
//Disable YOAST nag messages
function displayHelloWorld() {
remove_action( 'admin_notices', array( Yoast_Notification_Center::get(), 'display_notifications' ) );
remove_action( 'all_admin_notices', array( Yoast_Notification_Center::get(), 'display_notifications' ) );
}

// AND remove it from the adminbar
function mytheme_admin_bar_render() {
	global $wp_admin_bar;
	$wp_admin_bar->remove_menu('wpseo-menu');
}
add_action( 'wp_before_admin_bar_render', 'mytheme_admin_bar_render' );
?>