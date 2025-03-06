<?php
/**
 * ACF Field Group Registration
 *
 * @package Certifications_Plugin
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class to handle registration and synchronization of ACF field groups.
 */
class Certifications_ACF {

    /**
     * Constructor.
     */
    public function __construct() {
        // Register local JSON save point
        add_filter( 'acf/settings/save_json', array( $this, 'acf_json_save_point' ) );

        // Register local JSON load point
        add_filter( 'acf/settings/load_json', array( $this, 'acf_json_load_point' ) );

        // Register field groups via PHP if needed
        add_action( 'acf/init', array( $this, 'register_field_groups' ) );

        // Add logging for debugging
        if ( WP_DEBUG ) {
            error_log( 'Certifications_ACF initialized' );
        }
    }

    /**
     * Define ACF JSON save point
     *
     * @param string $path The path to save ACF JSON files.
     * @return string The modified path.
     */
    public function acf_json_save_point( $path ) {
        // Create acf-json directory in plugin if it doesn't exist
        $plugin_acf_path = CERTIFICATIONS_PLUGIN_PATH . 'acf-json';

        if ( ! file_exists( $plugin_acf_path ) ) {
            mkdir( $plugin_acf_path, 0755, true );

            if ( WP_DEBUG ) {
                error_log( 'Created ACF JSON directory at: ' . $plugin_acf_path );
            }
        }

        // Set save point to plugin directory
        return $plugin_acf_path;
    }

    /**
     * Register ACF JSON load point
     *
     * @param array $paths Array of paths ACF will load JSON files from.
     * @return array Modified array of paths.
     */
    public function acf_json_load_point( $paths ) {
        // Add our path to the load paths
        $paths[] = CERTIFICATIONS_PLUGIN_PATH . 'acf-json';

        if ( WP_DEBUG ) {
            error_log( 'Added ACF JSON load path: ' . CERTIFICATIONS_PLUGIN_PATH . 'acf-json' );
        }

        return $paths;
    }

    /**
     * Register field groups programmatically if needed
     *
     * This is a fallback in case the JSON synchronization doesn't work
     * or if you prefer to register fields via PHP.
     */
    public function register_field_groups() {
        // If no field groups exist in ACF, register them programmatically
        if ( ! $this->field_groups_exist() ) {
            $this->register_certifications_fields();

            if ( WP_DEBUG ) {
                error_log( 'Registered certification field groups programmatically' );
            }
        }
    }

    /**
     * Check if certification field groups already exist
     *
     * @return bool True if field groups exist, false otherwise.
     */
    private function field_groups_exist() {
        // Check if ACF function exists
        if ( ! function_exists( 'acf_get_field_groups' ) ) {
            return false;
        }

        // Get field groups
        $field_groups = acf_get_field_groups( array(
            'post_type' => 'certification',
        ) );

        // Return true if certification field groups exist
        return ! empty( $field_groups );
    }

    /**
     * Register certification field groups
     */
    private function register_certifications_fields() {
        // Only proceed if ACF function exists
        if ( ! function_exists( 'acf_add_local_field_group' ) ) {
            return;
        }

        // Register Certifications Field Group
        acf_add_local_field_group( array(
            'key' => 'group_67bf615a25b23',
            'title' => 'Certifications Field Group',
            'fields' => array(
                array(
                    'key' => 'field_67bf615add99b',
                    'label' => 'Intro',
                    'name' => 'intro',
                    'aria-label' => '',
                    'type' => 'wysiwyg',
                    'instructions' => '',
                    'required' => 0,
                    'conditional_logic' => 0,
                    'wrapper' => array(
                        'width' => '',
                        'class' => '',
                        'id' => '',
                    ),
                    'relevanssi_exclude' => 0,
                    'default_value' => '',
                    'allow_in_bindings' => 0,
                    'tabs' => 'all',
                    'toolbar' => 'full',
                    'media_upload' => 1,
                    'delay' => 0,
                ),
                array(
                    'key' => 'field_67bf6184dd99c',
                    'label' => 'Prepare & Apply',
                    'name' => 'prepare_&_apply',
                    'aria-label' => '',
                    'type' => 'wysiwyg',
                    'instructions' => '',
                    'required' => 0,
                    'conditional_logic' => 0,
                    'wrapper' => array(
                        'width' => '',
                        'class' => '',
                        'id' => '',
                    ),
                    'relevanssi_exclude' => 0,
                    'default_value' => '',
                    'allow_in_bindings' => 0,
                    'tabs' => 'all',
                    'toolbar' => 'full',
                    'media_upload' => 1,
                    'delay' => 0,
                ),
                array(
                    'key' => 'field_67bf6196dd99d',
                    'label' => 'Get Certified',
                    'name' => 'get_certified',
                    'aria-label' => '',
                    'type' => 'wysiwyg',
                    'instructions' => '',
                    'required' => 0,
                    'conditional_logic' => 0,
                    'wrapper' => array(
                        'width' => '',
                        'class' => '',
                        'id' => '',
                    ),
                    'relevanssi_exclude' => 0,
                    'default_value' => '',
                    'allow_in_bindings' => 0,
                    'tabs' => 'all',
                    'toolbar' => 'full',
                    'media_upload' => 1,
                    'delay' => 0,
                ),
                array(
                    'key' => 'field_67bf61a3dd99e',
                    'label' => 'After The Exam',
                    'name' => 'after_the_exam',
                    'aria-label' => '',
                    'type' => 'wysiwyg',
                    'instructions' => '',
                    'required' => 0,
                    'conditional_logic' => 0,
                    'wrapper' => array(
                        'width' => '',
                        'class' => '',
                        'id' => '',
                    ),
                    'relevanssi_exclude' => 0,
                    'default_value' => '',
                    'allow_in_bindings' => 0,
                    'tabs' => 'all',
                    'toolbar' => 'full',
                    'media_upload' => 1,
                    'delay' => 0,
                ),
                array(
                    'key' => 'field_67bf61afdd99f',
                    'label' => 'Documents',
                    'name' => 'documents',
                    'aria-label' => '',
                    'type' => 'wysiwyg',
                    'instructions' => '',
                    'required' => 0,
                    'conditional_logic' => 0,
                    'wrapper' => array(
                        'width' => '',
                        'class' => '',
                        'id' => '',
                    ),
                    'relevanssi_exclude' => 0,
                    'default_value' => '',
                    'allow_in_bindings' => 0,
                    'tabs' => 'all',
                    'toolbar' => 'full',
                    'media_upload' => 1,
                    'delay' => 0,
                ),
            ),
            'location' => array(
                array(
                    array(
                        'param' => 'post_type',
                        'operator' => '==',
                        'value' => 'certification',
                    ),
                ),
            ),
            'menu_order' => 0,
            'position' => 'normal',
            'style' => 'default',
            'label_placement' => 'top',
            'instruction_placement' => 'label',
            'hide_on_screen' => '',
            'active' => true,
            'description' => '',
            'show_in_rest' => 0,
        ));
    }
}