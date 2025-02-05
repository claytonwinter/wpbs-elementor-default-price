<?php
/**
 * Plugin Name: WP Booking System Default Price Elementor Widget
 * Description: Displays the WP Booking System default calendar price via an Elementor widget.
 * Version: 2.6
 * Author: Clayton Winter 
 * Text Domain: wpbs-elementor-default-price
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Check if Elementor is active.
 */
function wpbs_elementor_widget_requirements() {
	if ( ! did_action( 'elementor/loaded' ) ) {
		add_action( 'admin_notices', 'wpbs_elementor_missing_notice' );
		return false;
	}
	return true;
}

function wpbs_elementor_missing_notice() {
	?>
	<div class="notice notice-warning">
		<p><?php esc_html_e( 'WP Booking System Default Price Elementor Widget requires Elementor to be installed and activated.', 'wpbs-elementor-default-price' ); ?></p>
	</div>
	<?php
}

/**
 * Register the widget with Elementor.
 */
function wpbs_register_default_price_widget( $widgets_manager ) {
	if ( ! wpbs_elementor_widget_requirements() ) {
		return;
	}

	// Include the widget file.
	require_once( __DIR__ . '/widgets/default-price-widget.php' );

	// Register the widget.
	$widgets_manager->register( new \WPBS_Default_Price_Widget() );
}
add_action( 'elementor/widgets/register', 'wpbs_register_default_price_widget' );
