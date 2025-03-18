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
		add_shortcode( 'certification', array( $this, 'single_certification_shortcode' ) );
		// Add our new shortcode
		add_shortcode( 'certification_images', array( $this, 'certification_images_shortcode' ) );

		// Register the shortcode-specific stylesheet
		add_action( 'wp_enqueue_scripts', array( $this, 'register_shortcode_styles' ) );

		// Add logging for debugging
		if ( WP_DEBUG ) {
			error_log( 'Certifications_Shortcode initialized' );
		}
	}

	/**
	 * Register shortcode-specific stylesheet with file-based versioning
	 */
	public function register_shortcode_styles() {
		$css_file = CERTIFICATIONS_PLUGIN_PATH . 'assets/css/certifications-shortcode.css';
		$css_version = file_exists($css_file) ? filemtime($css_file) : CERTIFICATIONS_PLUGIN_VERSION;

		wp_register_style(
			'certifications-shortcode-style',
			CERTIFICATIONS_PLUGIN_URL . 'assets/css/certifications-shortcode.css',
			array(),
			$css_version
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
				// Basic display parameters
				'display_type'    => 'grid',     // 'grid' or 'list'
				'columns'         => 4,          // Number of columns in grid view
				'count'           => -1,         // Number of certifications to display
				'pagination'      => 'false',    // Whether to show pagination

				// Ordering parameters
				'order'           => 'ASC',      // ASC or DESC
				'orderby'         => 'menu_order', // Options: date, title, menu_order, rand, meta_value
				'meta_key'        => '',         // For ordering by meta_value

				// Filtering parameters
				'category'        => '',         // Filter by category slug
				'tag'             => '',         // Filter by tag slug
				'include'         => '',         // Specific certification IDs to include
				'exclude'         => '',         // Specific certification IDs to exclude

				// Layout & content parameters
				'show_image'      => 'true',     // Whether to show the certification image
				'image_size'      => 'medium',   // Size of thumbnail
				'show_title'      => 'true',     // Display certification's title
				'excerpt_length'  => 25,         // Length of excerpt in words

				// Link parameters
				'link_target'     => '_self',    // Where to open links
				'show_button'     => 'true',     // Display "Learn More" button
				'button_text'     => 'Learn More', // Custom text for button

				// Advanced parameters
				'offset'          => 0,          // Number of posts to offset/skip
				'cache'           => 'true',     // Whether to cache results
				'class'           => '',         // Additional CSS classes
			),
			$atts,
			'certifications'
		);
		
		// Allow cache to be disabled globally for testing
		if ( defined('CERTIFICATIONS_DISABLE_CACHE') && CERTIFICATIONS_DISABLE_CACHE ) {
			$atts['cache'] = false;
		}

		// Convert string booleans to actual booleans
		foreach (array('pagination', 'show_image', 'show_title', 'show_button', 'cache') as $bool_att) {
			$atts[$bool_att] = filter_var($atts[$bool_att], FILTER_VALIDATE_BOOLEAN);
		}

		// Convert numeric attributes
		$atts['columns'] = intval($atts['columns']);
		$atts['count'] = intval($atts['count']);
		$atts['excerpt_length'] = intval($atts['excerpt_length']);
		$atts['offset'] = intval($atts['offset']);

		// Start output buffering
		ob_start();

		// Get cached output if caching is enabled
		$cache_key = 'certifications_' . md5(serialize($atts));
		$cached_output = $atts['cache'] ? get_transient($cache_key) : false;

		if ($cached_output !== false) {
			echo $cached_output;
			return ob_get_clean();
		}

		// Get certifications
		$certifications = $this->get_certifications( $atts );

		// Check if any certifications exist
		if ( $certifications && $certifications->have_posts() ) {
			// Add container class based on display type
			$container_class = 'certifications-container';
			if ($atts['display_type'] === 'grid') {
				$container_class .= ' certifications-grid certifications-row justify-content-start certifications-mt-15 certifications-mb-5';
			} else {
				$container_class .= ' certifications-list';
			}

			// Add custom class if provided
			if (!empty($atts['class'])) {
				$container_class .= ' ' . esc_attr($atts['class']);
			}

			// Output container
			echo '<div class="' . esc_attr($container_class) . '">';

			while ( $certifications->have_posts() ) {
				$certifications->the_post();

				// Get certification data
				$id = get_the_ID();
				$title = get_the_title();
				$permalink = get_permalink();
				$description = '';
				$image = '';

				// Get card description from ACF field if available
				if (function_exists('get_field')) {
					$card_description = get_field('card_description', $id);
					if (!empty($card_description)) {
						$description = wp_kses_post($card_description);
					}
					else {
						// Fall back to intro field if card description is empty
						$intro_field = get_field('intro', $id);
						if (!empty($intro_field)) {
							$description = wp_strip_all_tags($intro_field);
							$description = wp_trim_words($description, $atts['excerpt_length']);
						}
					}
				}

				// If no ACF fields available, fall back to excerpt or content
				if (empty($description)) {
					if (has_excerpt()) {
						$description = get_the_excerpt();
					} else {
						$description = get_the_content();
						$description = strip_shortcodes($description);
						$description = excerpt_remove_blocks($description);
						$description = wp_strip_all_tags($description);
					}
					$description = wp_trim_words($description, $atts['excerpt_length'], '...');
				}

				// Get image if needed
				if ($atts['show_image']) {
					if (has_post_thumbnail()) {
						$image = get_the_post_thumbnail($id, $atts['image_size'], array('class' => 'certifications-thumbnail'));
					} elseif (function_exists('get_field')) {
						// Try to get field image from ACF if featured image is not set
						$acf_image = get_field('certification_logo', $id);
						if ($acf_image && is_array($acf_image)) {
							$image_src = $acf_image['sizes'][$atts['image_size']] ?? $acf_image['url'];
							$image = '<img src="' . esc_url($image_src) . '" alt="' . esc_attr($title) . '" class="certifications-thumbnail" />';
						}
					}
				}

				// Calculate column classes based on display type and columns
				$column_class = 'certifications-item';
				if ($atts['display_type'] === 'grid') {
					$column_class .= ' certifications-col certifications-small-12 certifications-large-' . (12 / intval($atts['columns']));
				} else {
					$column_class .= ' certifications-list-item';
				}

				// Output certification item
				?>
                <div class="<?php echo esc_attr($column_class); ?>">
                    <div class="certifications-card">
                        <div class="certifications-card-body">
							<?php if ($image && $atts['show_image']) : ?>
                                <div class="certifications-image">
                                    <a href="<?php echo esc_url($permalink); ?>" target="<?php echo esc_attr($atts['link_target']); ?>">
										<?php echo $image; ?>
                                    </a>
                                </div>
							<?php endif; ?>

                            <div class="certifications-content">
								<?php if ($atts['show_title']) : ?>
                                    <h3 class="certifications-title">
                                        <a href="<?php echo esc_url($permalink); ?>" target="<?php echo esc_attr($atts['link_target']); ?>">
											<?php echo esc_html($title); ?>
                                        </a>
                                    </h3>
								<?php endif; ?>

                                <div class="certifications-description">
									<?php echo wp_kses_post($description); ?>
                                </div>

								<?php if ($atts['show_button']) : ?>
                                    <div class="certifications-button">
                                        <a href="<?php echo esc_url($permalink); ?>" class="certifications-btn certifications-secondary" target="<?php echo esc_attr($atts['link_target']); ?>">
											<?php echo esc_html($atts['button_text']); ?>
                                        </a>
                                    </div>
								<?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
				<?php
			}

			// Close container
			echo '</div>';

			// Add pagination if enabled
			if ($atts['pagination'] && $certifications->max_num_pages > 1) {
				echo '<div class="certifications-pagination">';
				$big = 999999999; // Need an unlikely integer
				echo paginate_links(array(
					'base' => str_replace($big, '%#%', esc_url(get_pagenum_link($big))),
					'format' => '?paged=%#%',
					'current' => max(1, get_query_var('paged')),
					'total' => $certifications->max_num_pages,
					'prev_text' => '&laquo;',
					'next_text' => '&raquo;',
					'before_page_number' => '',
					'after_page_number' => '',
					'class' => 'certifications-page-numbers',
					'prev_class' => 'certifications-prev',
					'next_class' => 'certifications-next',
					'current_class' => 'certifications-current',
				));
				echo '</div>';
			}

			// Reset post data
			wp_reset_postdata();

		} else {
			// No certifications found
			echo '<p class="certifications-no-results">No certifications found.</p>';
		}

		// Get buffer contents
		$output = ob_get_clean();

		// Cache the output if caching is enabled
		if ($atts['cache']) {
			set_transient($cache_key, $output, HOUR_IN_SECONDS);
		}

		return $output;
	}

	/**
	 * Shortcode to display a single certification
	 *
	 * @param array $atts Shortcode attributes.
	 * @return string HTML output.
	 */
	public function single_certification_shortcode( $atts ) {
		// Enqueue styles
		wp_enqueue_style( 'certifications-plugin-style' );
		wp_enqueue_style( 'certifications-responsive-style' );
		wp_enqueue_style( 'certifications-shortcode-style' );
		wp_enqueue_script( 'certifications-plugin-script' );

		// Shortcode attributes
		$atts = shortcode_atts(
			array(
				'id'                => 0,          // Certification ID
				'show_image'        => 'true',     // Whether to show the certification image
				'show_buttons'      => 'true',     // Display action buttons
				'show_sections'     => 'true',     // Display content sections
				'sections'          => 'all',      // Which sections to display: all, intro, prepare, certified, exam, documents
				'class'             => '',         // Additional CSS classes
				'cache'             => 'true',     // Whether to cache results
			),
			$atts,
			'certification'
		);
		
		// Allow cache to be disabled globally for testing
		if ( defined('CERTIFICATIONS_DISABLE_CACHE') && CERTIFICATIONS_DISABLE_CACHE ) {
			$atts['cache'] = false;
		}

		// Convert string booleans to actual booleans
		foreach ( array( 'show_image', 'show_buttons', 'show_sections', 'cache' ) as $bool_att ) {
			$atts[ $bool_att ] = filter_var( $atts[ $bool_att ], FILTER_VALIDATE_BOOLEAN );
		}

		// Convert ID to integer
		$atts['id'] = intval( $atts['id'] );

		// Start output buffering
		ob_start();

		// Get cached output if caching is enabled
		$cache_key = 'certification_single_' . $atts['id'] . '_' . md5(serialize($atts));
		$cached_output = $atts['cache'] ? get_transient($cache_key) : false;

		if ($cached_output !== false) {
			echo $cached_output;
			return ob_get_clean();
		}

		// Check if we have a valid ID
		if ( $atts['id'] <= 0 ) {
			echo '<p class="certifications-error">' . __( 'Error: No Certification ID specified.', 'certifications-plugin' ) . '</p>';
			return ob_get_clean();
		}

		// Get the certification post
		$certification = get_post( $atts['id'] );

		// Check if the certification exists and is of the correct post type
		if ( !$certification || 'certification' !== $certification->post_type ) {
			echo '<p class="certifications-error">' . __( 'Error: Certification not found.', 'certifications-plugin' ) . '</p>';
			return ob_get_clean();
		}

		// Set up post data
		setup_postdata( $GLOBALS['post'] = $certification );

		// Container class
		$container_class = 'certifications-single certifications-shortcode';
		if ( !empty( $atts['class'] ) ) {
			$container_class .= ' ' . esc_attr( $atts['class'] );
		}

		// Parse sections to display
		$display_sections = array(
			'intro' => true,
			'prepare' => true,
			'certified' => true,
			'exam' => true,
			'documents' => true
		);

		if ( $atts['sections'] !== 'all' ) {
			$sections_to_show = explode( ',', $atts['sections'] );
			$display_sections = array(
				'intro' => in_array( 'intro', $sections_to_show ),
				'prepare' => in_array( 'prepare', $sections_to_show ),
				'certified' => in_array( 'certified', $sections_to_show ),
				'exam' => in_array( 'exam', $sections_to_show ),
				'documents' => in_array( 'documents', $sections_to_show )
			);
		}

		// Get field values if ACF is active
		$intro = $prepare_apply = $get_certified = $after_exam = $documents = '';
		$apply_button_url = $renew_button_url = '#';

		if ( function_exists( 'get_field' ) ) {
			$intro = get_field( 'intro', $certification->ID );
			$prepare_apply = get_field( 'prepare_&_apply', $certification->ID );
			$get_certified = get_field( 'get_certified', $certification->ID );
			$after_exam = get_field( 'after_the_exam', $certification->ID );
			$documents = get_field( 'documents', $certification->ID );

			$apply_button_url = get_field( 'apply_button_url', $certification->ID ) ?: '#';
			$renew_button_url = get_field( 'renew_button_url', $certification->ID ) ?: '#';
		}

		// Start output
		?>
        <div class="<?php echo esc_attr( $container_class ); ?>">
            <h2 class="certifications-title"><?php the_title(); ?></h2>

			<?php if ( $atts['show_image'] && has_post_thumbnail( $certification->ID ) ) : ?>
                <div class="certifications-featured-image">
					<?php echo get_the_post_thumbnail( $certification->ID, 'medium', array(
						'class' => 'certifications-thumbnail',
						'loading' => 'lazy'
					) ); ?>
                </div>
			<?php endif; ?>

			<?php if ( $atts['show_buttons'] ) : ?>
                <div class="certifications-action-buttons">
                    <a href="<?php echo esc_url( $apply_button_url ); ?>" class="certifications-btn certifications-primary">
						<?php _e( 'APPLY NOW', 'certifications-plugin' ); ?>
                    </a>
                    <a href="<?php echo esc_url( $renew_button_url ); ?>" class="certifications-btn certifications-primary">
						<?php _e( 'RENEW', 'certifications-plugin' ); ?>
                    </a>
                </div>
			<?php endif; ?>

			<?php if ( $atts['show_sections'] ) : ?>
                <!-- Intro Section -->
				<?php if ( $display_sections['intro'] && $intro ) : ?>
                    <div class="certifications-section certifications-intro">
                        <h3 class="certifications-text-to-uppercase"><?php _e( 'INTRO', 'certifications-plugin' ); ?></h3>
                        <div class="certifications-field-content">
							<?php echo $intro; ?>
                        </div>
                    </div>
				<?php endif; ?>

                <!-- Prepare & Apply Section -->
				<?php if ( $display_sections['prepare'] && $prepare_apply ) : ?>
                    <div class="certifications-section certifications-prepare">
                        <h3 class="certifications-text-to-uppercase"><?php _e( 'PREPARE & APPLY', 'certifications-plugin' ); ?></h3>
                        <div class="certifications-field-content">
							<?php echo $prepare_apply; ?>
                        </div>
                    </div>
				<?php endif; ?>

                <!-- Get Certified Section -->
				<?php if ( $display_sections['certified'] && $get_certified ) : ?>
                    <div class="certifications-section certifications-get-certified">
                        <h3 class="certifications-text-to-uppercase"><?php _e( 'GET CERTIFIED', 'certifications-plugin' ); ?></h3>
                        <div class="certifications-field-content">
							<?php echo $get_certified; ?>
                        </div>
                    </div>
				<?php endif; ?>

                <!-- After The Exam Section -->
				<?php if ( $display_sections['exam'] && $after_exam ) : ?>
                    <div class="certifications-section certifications-after-exam">
                        <h3 class="certifications-text-to-uppercase"><?php _e( 'AFTER THE EXAM', 'certifications-plugin' ); ?></h3>
                        <div class="certifications-field-content">
							<?php echo $after_exam; ?>
                        </div>
                    </div>
				<?php endif; ?>

                <!-- Documents Section -->
				<?php if ( $display_sections['documents'] && $documents ) : ?>
                    <div class="certifications-section certifications-documents">
                        <h3 class="certifications-text-to-uppercase"><?php _e( 'DOCUMENTS', 'certifications-plugin' ); ?></h3>
                        <div class="certifications-field-content">
							<?php echo $documents; ?>
                        </div>
                    </div>
				<?php endif; ?>
			<?php endif; ?>
        </div>
		<?php

		// Reset post data
		wp_reset_postdata();

		// Get buffer contents and clean buffer
		$output = ob_get_clean();

		// Cache the output if caching is enabled
		if ($atts['cache']) {
			set_transient($cache_key, $output, HOUR_IN_SECONDS);
		}

		return $output;
	}

	/**
	 * Shortcode to display certification featured images in a grid
	 *
	 * @param array $atts Shortcode attributes.
	 * @return string HTML output.
	 */
	public function certification_images_shortcode( $atts ) {
		// Enqueue styles
		wp_enqueue_style( 'certifications-plugin-style' );
		wp_enqueue_style( 'certifications-responsive-style' );
		wp_enqueue_style( 'certifications-shortcode-style' );

		// Default attributes
		$atts = shortcode_atts(
			array(
				'cache' => 'true',  // Whether to cache results
			),
			$atts,
			'certification_images'
		);

		// Convert string boolean to actual boolean
		$cache = filter_var($atts['cache'], FILTER_VALIDATE_BOOLEAN);
		
		// Allow cache to be disabled globally for testing
		if ( defined('CERTIFICATIONS_DISABLE_CACHE') && CERTIFICATIONS_DISABLE_CACHE ) {
			$cache = false;
		}

		// Start output buffering
		ob_start();

		// Get cached output if caching is enabled
		$cache_key = 'certification_images_' . md5(serialize($atts));
		$cached_output = $cache ? get_transient($cache_key) : false;

		if ($cached_output !== false) {
			echo $cached_output;
			return ob_get_clean();
		}

		// Query all certification posts ordered by menu_order
		$certifications = new WP_Query( array(
			'post_type'      => 'certification',
			'posts_per_page' => -1,
			'orderby'        => 'menu_order',
			'order'          => 'ASC',
		) );

		if ( $certifications->have_posts() ) {
			echo '<div class="certifications-images-container">';
			echo '<div class="certifications-images-row">';

			$counter = 0;

			while ( $certifications->have_posts() ) {
				$certifications->the_post();

				if ( has_post_thumbnail() ) {
					// Start a new row for every 4 items
					if ( $counter > 0 && $counter % 4 === 0 ) {
						echo '</div><div class="certifications-images-row">';
					}

					echo '<div class="certifications-image-item">';
					echo '<a href="' . esc_url( get_permalink() ) . '">';
					the_post_thumbnail( 'medium', array( 'class' => 'certifications-image-thumbnail' ) );
					echo '</a>';
					echo '</div>';

					$counter++;
				}
			}

			echo '</div>';
			echo '</div>';

			wp_reset_postdata();
		} else {
			echo '<p class="certifications-no-results">No certification images found.</p>';
		}

		// Get buffer contents
		$output = ob_get_clean();

		// Cache the output if caching is enabled
		if ($cache) {
			set_transient($cache_key, $output, HOUR_IN_SECONDS);
		}

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
			'orderby'        => $atts['orderby'],
			'offset'         => $atts['offset'],
		);

		// Add meta key for ordering if specified
		if ($atts['orderby'] === 'meta_value' && !empty($atts['meta_key'])) {
			$args['meta_key'] = $atts['meta_key'];
		} elseif ($atts['orderby'] === 'menu_order') {
			// Default to using the display order meta for menu_order
			$args['meta_key'] = '_certification_display_order';
			$args['orderby'] = 'meta_value_num';
		}

		// Add category filter if specified
		if ( ! empty( $atts['category'] ) ) {
			// Check if category is an ID or slug
			if (is_numeric($atts['category'])) {
				$args['cat'] = intval($atts['category']);
			} else {
				$args['category_name'] = $atts['category'];
			}
		}

		// Add tag filter if specified
		if (!empty($atts['tag'])) {
			// Check if tag is an ID or slug
			if (is_numeric($atts['tag'])) {
				$args['tag_id'] = intval($atts['tag']);
			} else {
				$args['tag'] = $atts['tag'];
			}
		}

		// Add specific posts to include
		if (!empty($atts['include'])) {
			$include_ids = array_map('intval', explode(',', $atts['include']));
			$args['post__in'] = $include_ids;
		}

		// Add specific posts to exclude
		if (!empty($atts['exclude'])) {
			$exclude_ids = array_map('intval', explode(',', $atts['exclude']));
			$args['post__not_in'] = $exclude_ids;
		}

		// Add pagination if needed
		if ($atts['pagination']) {
			$args['paged'] = get_query_var('paged') ? get_query_var('paged') : 1;
		}

		// Create and return query
		return new WP_Query( $args );
	}
}
