<?php
/**
 * Custom Post Type Registration
 *
 * @package Certifications_Plugin
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class to handle registration of the Certifications custom post type.
 */
class Certifications_CPT {

    /**
     * Constructor.
     */
    public function __construct() {
        // Register the custom post type.
        add_action( 'init', array( $this, 'register_post_type' ) );

        // Add meta boxes
        add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
        add_action( 'save_post_certification', array( $this, 'save_meta_box_data' ) );

        // Add custom admin columns
        add_filter( 'manage_certification_posts_columns', array( $this, 'add_admin_columns' ) );
        add_action( 'manage_certification_posts_custom_column', array( $this, 'custom_column_content' ), 10, 2 );
        add_filter( 'manage_edit-certification_sortable_columns', array( $this, 'sortable_columns' ) );
        add_action( 'pre_get_posts', array( $this, 'sort_by_display_order' ) );

        // Add template filters
        add_filter( 'single_template', array( $this, 'single_template' ) );
        add_filter( 'archive_template', array( $this, 'archive_template' ) );

        // Add logging for debugging.
        if ( WP_DEBUG ) {
            error_log( 'Certifications_CPT initialized' );
        }
    }

    /**
     * Register Certifications custom post type.
     */
    public function register_post_type() {
        register_post_type( 'certification', $this->get_post_type_args() );

        // Log registration for debugging
        if ( WP_DEBUG ) {
            error_log( 'Certification post type registered' );
        }
    }

    /**
     * Get post type arguments
     */
    private function get_post_type_args() {
        return array(
            'labels'              => $this->get_post_type_labels(),
            'description'         => __( 'Certifications custom post type', 'certifications-plugin' ),
            'public'              => true,
            'hierarchical'        => false,
            'exclude_from_search' => false,
            'publicly_queryable'  => true,
            'show_ui'             => true,
            'show_in_menu'        => true,
            'show_in_admin_bar'   => true,
            'show_in_nav_menus'   => true,
            'show_in_rest'        => true,
            'menu_position'       => null,
            'menu_icon'           => 'dashicons-media-document',
            'capability_type'     => 'post',
            'supports'            => array(
                'title',
                'editor',
                'page-attributes',
                'thumbnail',
                'custom-fields',
            ),
            'taxonomies'          => array(
                'category',
                'post_tag',
            ),
            'has_archive'         => false,
            'rewrite' => array(
	            'slug' => 'credentials/certification',
	            'with_front' => false,
	            'feeds' => false,
	            'pages' => true,
            ),
            'query_var'           => true,
            'can_export'          => true,
        );
    }

    /**
     * Get post type labels
     */
    private function get_post_type_labels() {
        return array(
            'name'                  => _x( 'Certifications', 'Post type general name', 'certifications-plugin' ),
            'singular_name'         => _x( 'Certification', 'Post type singular name', 'certifications-plugin' ),
            'menu_name'             => _x( 'Certifications', 'Admin Menu text', 'certifications-plugin' ),
            'all_items'             => __( 'All Certifications', 'certifications-plugin' ),
            'edit_item'             => __( 'Edit Certification', 'certifications-plugin' ),
            'view_item'             => __( 'View Certification', 'certifications-plugin' ),
            'view_items'            => __( 'View Certifications', 'certifications-plugin' ),
            'add_new_item'          => __( 'Add New Certification', 'certifications-plugin' ),
            'add_new'               => __( 'Add New Certification', 'certifications-plugin' ),
            'new_item'              => __( 'New Certification', 'certifications-plugin' ),
            'parent_item_colon'     => __( 'Parent Certification:', 'certifications-plugin' ),
            'search_items'          => __( 'Search Certifications', 'certifications-plugin' ),
            'not_found'             => __( 'No certifications found', 'certifications-plugin' ),
            'not_found_in_trash'    => __( 'No certifications found in Trash', 'certifications-plugin' ),
            'archives'              => __( 'Certification Archives', 'certifications-plugin' ),
            'attributes'            => __( 'Certification Attributes', 'certifications-plugin' ),
            'insert_into_item'      => __( 'Insert into certification', 'certifications-plugin' ),
            'uploaded_to_this_item' => __( 'Uploaded to this certification', 'certifications-plugin' ),
            'filter_items_list'     => __( 'Filter certifications list', 'certifications-plugin' ),
            'filter_by_date'        => __( 'Filter certifications by date', 'certifications-plugin' ),
            'items_list_navigation' => __( 'Certifications list navigation', 'certifications-plugin' ),
            'items_list'            => __( 'Certifications list', 'certifications-plugin' ),
            'item_published'        => __( 'Certification published.', 'certifications-plugin' ),
            'item_published_privately' => __( 'Certification published privately.', 'certifications-plugin' ),
            'item_reverted_to_draft' => __( 'Certification reverted to draft.', 'certifications-plugin' ),
            'item_scheduled'        => __( 'Certification scheduled.', 'certifications-plugin' ),
            'item_updated'          => __( 'Certification updated.', 'certifications-plugin' ),
            'item_link'             => __( 'Certification Link', 'certifications-plugin' ),
            'item_link_description' => __( 'A link to a certification.', 'certifications-plugin' ),
        );
    }

    /**
     * Add meta boxes for certification post type
     */
    public function add_meta_boxes() {
        add_meta_box(
            'certification_display_order',
            __( 'Display Order', 'certifications-plugin' ),
            array( $this, 'display_order_meta_box' ),
            'certification',
            'side',
            'high'
        );
    }

    /**
     * Display order meta box callback
     */
    public function display_order_meta_box( $post ) {
        // Add nonce for security
        wp_nonce_field( 'certification_display_order_nonce', 'certification_display_order_nonce' );

        // Get current value
        $value = get_post_meta( $post->ID, '_certification_display_order', true );

        echo '<label for="certification_display_order">';
        echo __( 'Enter display order (lower numbers appear first):', 'certifications-plugin' );
        echo '</label> ';
        echo '<input type="number" id="certification_display_order" name="certification_display_order" value="' . esc_attr( $value ) . '" min="1" step="1" style="width: 100%">';
    }

    /**
     * Save meta box data
     */
    public function save_meta_box_data( $post_id ) {
        // Check if our nonce is set and verify it
        if ( ! isset( $_POST['certification_display_order_nonce'] ) ||
            ! wp_verify_nonce( $_POST['certification_display_order_nonce'], 'certification_display_order_nonce' ) ) {
            return;
        }

        // Check user permissions
        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }

        // Don't save on autosave
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }

        // Save the display order
        if ( isset( $_POST['certification_display_order'] ) ) {
            $display_order = sanitize_text_field( $_POST['certification_display_order'] );
            update_post_meta( $post_id, '_certification_display_order', $display_order );
        }
    }

    /**
     * Add custom columns to admin list
     */
    public function add_admin_columns( $columns ) {
        $new_columns = array();

        // Insert display order after checkbox but before title
        foreach( $columns as $key => $value ) {
            if ( $key === 'cb' ) {
                $new_columns[$key] = $value;
                $new_columns['display_order'] = __( 'Order', 'certifications-plugin' );
            } else {
                $new_columns[$key] = $value;
            }
        }

        return $new_columns;
    }

    /**
     * Display content for custom columns
     */
    public function custom_column_content( $column, $post_id ) {
        if ( 'display_order' === $column ) {
            $order = get_post_meta( $post_id, '_certification_display_order', true );
            echo esc_html( $order ?: '-' );
        }
    }

    /**
     * Make custom columns sortable
     */
    public function sortable_columns( $columns ) {
        $columns['display_order'] = 'display_order';
        return $columns;
    }

    /**
     * Sort by display order in admin
     */
    public function sort_by_display_order( $query ) {
        if ( ! is_admin() ) {
            return;
        }

        $orderby = $query->get( 'orderby' );

        if ( 'display_order' === $orderby ) {
            $query->set( 'meta_key', '_certification_display_order' );
            $query->set( 'orderby', 'meta_value_num' );
        }
    }

    /**
     * Use custom template for single certification
     *
     * @param string $template The path of the template to include.
     * @return string The path of the template to include.
     */
    public function single_template( $template ) {
        if ( is_singular( 'certification' ) ) {
            // Check if a custom template exists in the theme
            $theme_template = locate_template( array( 'single-certification.php' ) );

            // If a theme template exists, use that
            if ( $theme_template ) {
                return apply_filters( 'certifications_plugin_theme_single_template', $theme_template );
            }

            // Otherwise use the plugin template
            $plugin_template = CERTIFICATIONS_PLUGIN_PATH . 'templates/single-certification.php';

            if ( file_exists( $plugin_template ) ) {
                return apply_filters( 'certifications_plugin_single_template', $plugin_template );
            }
        }

        return $template;
    }

    /**
     * Use custom template for certification archives
     *
     * @param string $template The path of the template to include.
     * @return string The path of the template to include.
     */
    public function archive_template( $template ) {
        if ( is_post_type_archive( 'certification' ) ) {
            // Check if a custom template exists in the theme
            $theme_template = locate_template( array( 'archive-certification.php' ) );

            // If a theme template exists, use that
            if ( $theme_template ) {
                return apply_filters( 'certifications_plugin_theme_archive_template', $theme_template );
            }

            // Otherwise use the plugin template
            $plugin_template = CERTIFICATIONS_PLUGIN_PATH . 'templates/archive-certification.php';

            if ( file_exists( $plugin_template ) ) {
                return apply_filters( 'certifications_plugin_archive_template', $plugin_template );
            }
        }

        return $template;
    }
}