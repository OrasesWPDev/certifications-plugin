<?php
/**
 * Plugin Name: Certifications Plugin
 * Plugin URI: https://yourwebsite.com/
 * Description: A custom plugin for managing and displaying certifications with ACF integration.
 * Version: 1.0.18
 * Author: Orases
 * Author URI: https://orases.com/
 * Text Domain: certifications-plugin
 * Domain Path: /languages
 * Requires at least: 5.8
 * Requires PHP: 7.4
 *
 * @package Certifications_Plugin
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Define plugin constants.
define( 'CERTIFICATIONS_PLUGIN_VERSION', '1.0.18' );
define( 'CERTIFICATIONS_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
define( 'CERTIFICATIONS_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'CERTIFICATIONS_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

// Check if ACF is active
function certif_plugin_has_acf() {
	return class_exists( 'ACF' );
}

// Plugin initialization
function certifications_plugin_init() {
	// Load plugin textdomain
	load_plugin_textdomain( 'certifications-plugin', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );

	// Include required files
	require_once CERTIFICATIONS_PLUGIN_PATH . 'includes/class-certifications-cpt.php';

	// Only load ACF integration if ACF is active
	if ( certif_plugin_has_acf() ) {
		require_once CERTIFICATIONS_PLUGIN_PATH . 'includes/class-certifications-acf.php';
	} else {
		// Admin notice if ACF is not active
		add_action( 'admin_notices', 'certif_plugin_acf_missing_notice' );
	}

	// Load shortcode functionality
	require_once CERTIFICATIONS_PLUGIN_PATH . 'includes/class-certifications-shortcode.php';

	// Initialize classes
	new Certifications_CPT();
	if ( certif_plugin_has_acf() ) {
		new Certifications_ACF();
	}
	new Certifications_Shortcode();

	// Register assets
	add_action( 'wp_enqueue_scripts', 'certifications_plugin_register_assets' );
	add_action( 'admin_enqueue_scripts', 'certifications_plugin_register_admin_assets' );
}
add_action( 'plugins_loaded', 'certifications_plugin_init' );

// Admin notice for missing ACF
function certif_plugin_acf_missing_notice() {
	?>
    <div class="notice notice-error">
        <p><?php _e( 'Certifications Plugin requires Advanced Custom Fields PRO to be installed and activated.', 'certifications-plugin' ); ?></p>
    </div>
	<?php
}

// Register front-end assets
function certifications_plugin_register_assets() {
	// Main CSS
	wp_register_style(
		'certifications-plugin-style',
		CERTIFICATIONS_PLUGIN_URL . 'assets/css/certifications.css',
		array(),
		CERTIFICATIONS_PLUGIN_VERSION
	);

	// Responsive CSS
	wp_register_style(
		'certifications-plugin-responsive-style',
		CERTIFICATIONS_PLUGIN_URL . 'assets/css/responsive-certifications.css',
		array('certifications-plugin-style'), // This makes it load after the main CSS
		CERTIFICATIONS_PLUGIN_VERSION
	);

	// Shortcode CSS
	wp_register_style(
		'certifications-shortcode-style',
		CERTIFICATIONS_PLUGIN_URL . 'assets/css/certifications-shortcode.css',
		array('certifications-plugin-style'),
		CERTIFICATIONS_PLUGIN_VERSION
	);

	// JavaScript
	wp_register_script(
		'certifications-plugin-script',
		CERTIFICATIONS_PLUGIN_URL . 'assets/js/certifications.js',
		array( 'jquery' ),
		CERTIFICATIONS_PLUGIN_VERSION,
		true
	);

	// Enqueue the assets
	wp_enqueue_style( 'certifications-plugin-style' );
	wp_enqueue_style( 'certifications-plugin-responsive-style' );
	wp_enqueue_script( 'certifications-plugin-script' );

	// Note: We don't enqueue shortcode CSS here because it will be enqueued
	// by the shortcode function only when the shortcode is used
}

// Register admin assets
function certifications_plugin_register_admin_assets($hook) {
	// Only load on specific admin pages if needed
	if ('post.php' === $hook || 'post-new.php' === $hook) {
		global $post;
		if ($post && 'certification' === $post->post_type) {
			// Main CSS
			wp_enqueue_style(
				'certifications-plugin-style',
				CERTIFICATIONS_PLUGIN_URL . 'assets/css/certifications.css',
				array(),
				CERTIFICATIONS_PLUGIN_VERSION
			);

			// Shortcode CSS
			wp_enqueue_style(
				'certifications-shortcode-style',
				CERTIFICATIONS_PLUGIN_URL . 'assets/css/certifications-shortcode.css',
				array('certifications-plugin-style'),
				CERTIFICATIONS_PLUGIN_VERSION
			);

			// Responsive CSS
			wp_enqueue_style(
				'certifications-responsive-style',
				CERTIFICATIONS_PLUGIN_URL . 'assets/css/responsive-certifications.css',
				array('certifications-plugin-style'),
				CERTIFICATIONS_PLUGIN_VERSION
			);

			// Main JS
			wp_enqueue_script(
				'certifications-plugin-script',
				CERTIFICATIONS_PLUGIN_URL . 'assets/js/certifications.js',
				array('jquery'),
				CERTIFICATIONS_PLUGIN_VERSION,
				true
			);
		}
	}
}

// Custom breadcrumbs shortcode for certification pages
add_shortcode('certification_breadcrumbs', 'certifications_breadcrumbs_shortcode');
function certifications_breadcrumbs_shortcode() {
	if (!is_singular('certification')) {
		return do_shortcode('[wpseo_breadcrumb]');
	}

	ob_start();

	$post_title = get_the_title();
	?>
    <span>
        <span><a href="<?php echo home_url(); ?>">Home</a></span>
        <span class="yoast-divider">/</span>
        <span><a href="<?php echo home_url('/credentials/'); ?>">Credentials</a></span>
        <span class="yoast-divider">/</span>
        <span><a href="<?php echo home_url('/credentials/certification/'); ?>">Certification</a></span>
        <span class="yoast-divider">/</span>
        <span class="breadcrumb_last" aria-current="page"><?php echo esc_html($post_title); ?></span>
    </span>
	<?php

	return ob_get_clean();
}

// Force registration of ACF field groups from JSON
function certif_force_acf_sync() {
	if (!function_exists('acf_get_field_groups') || !function_exists('acf_add_local_field_group')) {
		return;
	}

	// Path to the ACF JSON file
	$json_file = CERTIFICATIONS_PLUGIN_PATH . 'acf-json/group_67bf615a25b23.json';
	if (file_exists($json_file)) {
		$json_content = file_get_contents($json_file);

		// Check if json content is valid
		if (!$json_content) {
			error_log('Failed to read JSON file: ' . $json_file);
			return;
		}

		$json_data = json_decode($json_content, true);

		// Check if json_decode was successful
		if (json_last_error() !== JSON_ERROR_NONE) {
			error_log('JSON decoding error: ' . json_last_error_msg());
			return;
		}

		// Verify json_data is an array
		if (!is_array($json_data)) {
			error_log('JSON data is not an array');
			return;
		}

		// Process each field group
		foreach ($json_data as $field_group) {
			// Verify field_group is an array
			if (!is_array($field_group)) {
				error_log('Field group is not an array');
				continue;
			}

			// Verify field_group has a title
			if (!isset($field_group['title'])) {
				error_log('Field group has no title');
				continue;
			}

			acf_add_local_field_group($field_group);
			error_log('Registered field group: ' . $field_group['title']);
		}
	} else {
		error_log('ACF JSON file not found: ' . $json_file);
	}
}
add_action('acf/init', 'certif_force_acf_sync', 20);

// Activation hook
register_activation_hook( __FILE__, 'certif_plugin_activate' );
function certif_plugin_activate() {
	// Flush rewrite rules on activation
	require_once CERTIFICATIONS_PLUGIN_PATH . 'includes/class-certifications-cpt.php';
	$cpt = new Certifications_CPT();
	$cpt->register_post_type();
	flush_rewrite_rules();

	// Debug log on activation
	if ( WP_DEBUG ) {
		error_log( 'Certifications Plugin activated' );
	}
}

// Deactivation hook
register_deactivation_hook( __FILE__, 'certif_plugin_deactivate' );
function certif_plugin_deactivate() {
	// Flush rewrite rules on deactivation
	flush_rewrite_rules();

	// Debug log on deactivation
	if ( WP_DEBUG ) {
		error_log( 'Certifications Plugin deactivated' );
	}
}