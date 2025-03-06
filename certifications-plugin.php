<?php
/**
 * Plugin Name: Certifications Plugin
 * Plugin URI: https://yourwebsite.com/
 * Description: A custom plugin for managing and displaying certifications with ACF integration.
 * Version: 1.0.5
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
define( 'CERTIFICATIONS_PLUGIN_VERSION', '1.0.5' );
define( 'CERTIFICATIONS_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
define( 'CERTIFICATIONS_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'CERTIFICATIONS_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

// Check if ACF is active
function certifications_plugin_has_acf() {
    return class_exists( 'ACF' );
}

// Plugin initialization
function certifications_plugin_init() {
    // Load plugin textdomain
    load_plugin_textdomain( 'certifications-plugin', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );

    // Include required files
    require_once CERTIFICATIONS_PLUGIN_PATH . 'includes/class-certifications-cpt.php';

    // Only load ACF integration if ACF is active
    if ( certifications_plugin_has_acf() ) {
        require_once CERTIFICATIONS_PLUGIN_PATH . 'includes/class-certifications-acf.php';
    } else {
        // Admin notice if ACF is not active
        add_action( 'admin_notices', 'certifications_plugin_acf_missing_notice' );
    }

    // Load shortcode functionality
    require_once CERTIFICATIONS_PLUGIN_PATH . 'includes/class-certifications-shortcode.php';

    // Initialize classes
    new Certifications_CPT();
    if ( certifications_plugin_has_acf() ) {
        new Certifications_ACF();
    }
    new Certifications_Shortcode();

    // Register assets
    add_action( 'wp_enqueue_scripts', 'certifications_plugin_register_assets' );
    add_action( 'admin_enqueue_scripts', 'certifications_plugin_register_admin_assets' );
}
add_action( 'plugins_loaded', 'certifications_plugin_init' );

// Admin notice for missing ACF
function certifications_plugin_acf_missing_notice() {
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
}

// Register admin assets
function certifications_plugin_register_admin_assets( $hook ) {
    // Only load on specific admin pages if needed
    if ( 'post.php' === $hook || 'post-new.php' === $hook ) {
        global $post;
        if ( $post && 'certification' === $post->post_type ) {
            wp_enqueue_style(
                'certifications-plugin-admin-style',
                CERTIFICATIONS_PLUGIN_URL . 'assets/css/admin.css',
                array(),
                CERTIFICATIONS_PLUGIN_VERSION
            );

            wp_enqueue_script(
                'certifications-plugin-admin-script',
                CERTIFICATIONS_PLUGIN_URL . 'assets/js/admin.js',
                array( 'jquery' ),
                CERTIFICATIONS_PLUGIN_VERSION,
                true
            );
        }
    }
}

// Activation hook
register_activation_hook( __FILE__, 'certifications_plugin_activate' );
function certifications_plugin_activate() {
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
register_deactivation_hook( __FILE__, 'certifications_plugin_deactivate' );
function certifications_plugin_deactivate() {
    // Flush rewrite rules on deactivation
    flush_rewrite_rules();

    // Debug log on deactivation
    if ( WP_DEBUG ) {
        error_log( 'Certifications Plugin deactivated' );
    }
}