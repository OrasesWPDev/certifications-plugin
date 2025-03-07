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

		// Register the new shortcode-specific stylesheet
		add_action( 'wp_enqueue_scripts', array( $this, 'register_shortcode_styles' ) );

		// Add logging for debugging
		if ( WP_DEBUG ) {
			error_log( 'Certifications_Shortcode initialized' );
		}
	}

	/**
	 * Register shortcode-specific stylesheet
	 */
	public function register_shortcode_styles() {
		wp_register_style(
			'certifications-shortcode-style',
			CERTIFICATIONS_PLUGIN_URL . 'assets/css/certifications-shortcode.css',
			array(),
			CERTIFICATIONS_PLUGIN_VERSION
		);
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
		wp_enqueue_style( 'certifications-responsive-style' );
		wp_enqueue_style( 'certifications-shortcode-style' );

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
			echo '<div class="row justify-content-start mt-15 mb-5">';
			echo '<div class="certifications-grid row">';

			while ( $certifications->have_posts() ) {
				$certifications->the_post();

				// Get certification data
				$title = get_the_title();
				$permalink = get_permalink();

				// Get card description from ACF field if available
				$description = '';
				if (function_exists('get_field')) {
					$card_description = get_field('card_description');
					if (!empty($card_description)) {
						$description = wp_kses_post($card_description);
					}
					else {
						// Fall back to intro field if card description is empty
						$intro_field = get_field('intro');
						if (!empty($intro_field)) {
							$description = wp_strip_all_tags($intro_field);
							$description = wp_trim_words($description, 25);
						}
					}
				}

                // If no ACF fields available, fall back to excerpt or content
				if (empty($description)) {
					$description = has_excerpt() ? get_the_excerpt() : wp_trim_words(get_the_content(), 20);
				}

				// Calculate column classes based on Flatsome's grid system
				$column_class = 'small-12 large-' . (12 / intval($atts['columns']));

				// Output certification item
				?>
                <div class="certification-item col <?php echo esc_attr($column_class); ?>">
                    <a href="<?php echo esc_url($permalink); ?>" class="certification-link">
                        <div class="certification-card">
                            <div class="certification-card-body">
								<?php if (has_post_thumbnail()) : ?>
                                    <div class="certification-image">
										<?php the_post_thumbnail('medium', array('class' => 'certification-thumbnail rounded mx-auto d-block')); ?>
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
			}

			// Close grid containers
			echo '</div>';
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