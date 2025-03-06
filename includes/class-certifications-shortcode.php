<?php
/**
 * Shortcode for displaying certifications
 *
 * @package Certifications_Plugin
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class to handle certification shortcodes.
 */
class Certifications_Shortcode {

    /**
     * Constructor.
     */
    public function __construct() {
        // Register shortcodes
        add_shortcode( 'certifications', array( $this, 'certifications_grid_shortcode' ) );

        // Add logging for debugging
        if ( WP_DEBUG ) {
            error_log( 'Certifications_Shortcode initialized' );
        }
    }

    /**
     * Shortcode to display certifications in a grid layout
     *
     * @param array $atts Shortcode attributes.
     * @return string HTML output.
     */
    public function certifications_grid_shortcode( $atts ) {
        // Enqueue styles
        wp_enqueue_style( 'certifications-plugin-style' );

        // Shortcode attributes
        $atts = shortcode_atts(
            array(
                'count'    => -1,         // How many to display. -1 for all.
                'columns'  => 4,          // Number of columns per row
                'category' => '',         // Filter by category slug
                'order'    => 'ASC',      // ASC or DESC
            ),
            $atts,
            'certifications'
        );

        // Start output buffering
        ob_start();

        // Get certifications
        $certifications = $this->get_certifications( $atts );

        // Check if any certifications exist
        if ( $certifications && $certifications->have_posts() ) {

            // Output grid container
            echo '<div class="certifications-grid row">';

            $count = 0;
            while ( $certifications->have_posts() ) {
                $certifications->the_post();

                // Calculate column classes based on Flatsome's grid system
                $column_class = 'small-12 large-' . (12 / intval($atts['columns']));

                // New row every X items (where X is the columns setting)
                if ( $count > 0 && $count % intval($atts['columns']) === 0 ) {
                    echo '</div><div class="certifications-grid row">';
                }

                // Get certification data
                $title = get_the_title();
                $permalink = get_permalink();
                $description = has_excerpt() ? get_the_excerpt() : wp_trim_words(get_the_content(), 20);

                // Output certification item
                ?>
                <div class="certification-item col <?php echo esc_attr($column_class); ?>">
                    <a href="<?php echo esc_url($permalink); ?>" class="certification-link">
                        <div class="certification-card">
                            <div class="certification-card-body">
                                <?php if (has_post_thumbnail()) : ?>
                                    <div class="certification-image">
                                        <?php the_post_thumbnail('medium', array('class' => 'certification-thumbnail')); ?>
                                    </div>
                                <?php endif; ?>

                                <div class="certification-content">
                                    <h3 class="certification-title"><?php echo esc_html($title); ?></h3>
                                    <div class="certification-description">
                                        <?php echo wp_kses_post($description); ?>
                                    </div>
                                    <div class="certification-button">
                                        <span class="button secondary">Learn More</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
                <?php

                $count++;
            }

            // Close grid container
            echo '</div>';

            // Reset post data
            wp_reset_postdata();

        } else {
            // No certifications found
            echo '<p class="no-certifications">No certifications found.</p>';
        }

        // Get buffer contents and clean buffer
        $output = ob_get_clean();

        return $output;
    }

    /**
     * Get certifications query
     *
     * @param array $atts Query parameters.
     * @return WP_Query Certifications query.
     */
    private function get_certifications( $atts ) {
        // Query arguments
        $args = array(
            'post_type'      => 'certification',
            'posts_per_page' => $atts['count'],
            'order'          => $atts['order'],
            'orderby'        => 'meta_value_num',
            'meta_key'       => '_certification_display_order',
        );

        // Add category filter if specified
        if ( ! empty( $atts['category'] ) ) {
            $args['tax_query'] = array(
                array(
                    'taxonomy' => 'category',
                    'field'    => 'slug',
                    'terms'    => explode( ',', $atts['category'] ),
                ),
            );
        }

        // Create and return query
        return new WP_Query( $args );
    }
}