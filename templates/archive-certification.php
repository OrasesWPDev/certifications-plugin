<?php
/**
 * The template for displaying certification archives
 *
 * @package Certifications_Plugin
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

// Get display options - can be filtered or set via theme customizer
$display_type = apply_filters('certifications_archive_display_type', 'grid');
$columns = apply_filters('certifications_archive_columns', 3);
$show_image = apply_filters('certifications_archive_show_image', true);
$image_size = apply_filters('certifications_archive_image_size', 'medium');
$excerpt_length = apply_filters('certifications_archive_excerpt_length', 25);
$button_text = apply_filters('certifications_archive_button_text', __('Learn More', 'certifications-plugin'));
$link_target = apply_filters('certifications_archive_link_target', '_self');
?>

    <main id="main" class="certification-archive">
        <div class="page-wrapper">
            <div class="container">
                <div class="row">
                    <div class="large-12 col">
                        <div class="col-inner">
                            <header class="page-header">
                                <h1 class="page-title"><?php post_type_archive_title(); ?></h1>
                            </header>

							<?php
							// Add container class based on display type
							$container_class = 'certifications-container';
							if ($display_type === 'grid') {
								$container_class .= ' certifications-grid row';
							} else {
								$container_class .= ' certifications-list';
							}
							?>

                            <div class="<?php echo esc_attr($container_class); ?>">
								<?php if ( have_posts() ) : ?>
									<?php while ( have_posts() ) : the_post();
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
													$description = wp_trim_words($description, $excerpt_length);
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
											$description = wp_trim_words($description, $excerpt_length, '...');
										}

										// Get image if needed
										if ($show_image) {
											if (has_post_thumbnail()) {
												$image = get_the_post_thumbnail($id, $image_size, array('class' => 'certification-thumbnail'));
											} elseif (function_exists('get_field')) {
												// Try to get field image from ACF if featured image is not set
												$acf_image = get_field('certification_logo', $id);
												if ($acf_image && is_array($acf_image)) {
													$image_src = $acf_image['sizes'][$image_size] ?? $acf_image['url'];
													$image = '<img src="' . esc_url($image_src) . '" alt="' . esc_attr($title) . '" class="certification-thumbnail" />';
												}
											}
										}

										// Calculate column classes based on display type and columns
										$column_class = 'certification-item';
										if ($display_type === 'grid') {
											$column_class .= ' col small-12 large-' . (12 / intval($columns));
										} else {
											$column_class .= ' certification-list-item';
										}
										?>
                                        <div class="<?php echo esc_attr($column_class); ?>">
                                            <div class="certification-card">
                                                <div class="certification-card-body">
													<?php if ($image && $show_image) : ?>
                                                        <div class="certification-image">
                                                            <a href="<?php echo esc_url($permalink); ?>" target="<?php echo esc_attr($link_target); ?>">
																<?php echo $image; ?>
                                                            </a>
                                                        </div>
													<?php endif; ?>

                                                    <div class="certification-content">
                                                        <h3 class="certification-title">
                                                            <a href="<?php echo esc_url($permalink); ?>" target="<?php echo esc_attr($link_target); ?>">
																<?php echo esc_html($title); ?>
                                                            </a>
                                                        </h3>

                                                        <div class="certification-description">
															<?php echo wp_kses_post($description); ?>
                                                        </div>

                                                        <div class="certification-button">
                                                            <a href="<?php echo esc_url($permalink); ?>" class="button secondary" target="<?php echo esc_attr($link_target); ?>">
																<?php echo esc_html($button_text); ?>
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
									<?php endwhile; ?>
								<?php else : ?>
                                    <div class="col small-12">
                                        <p class="no-certifications"><?php _e('No certifications found.', 'certifications-plugin'); ?></p>
                                    </div>
								<?php endif; ?>
                            </div>

							<?php
							// Add standard WordPress pagination
							the_posts_pagination(array(
								'prev_text' => '&laquo;',
								'next_text' => '&raquo;',
								'before_page_number' => '<span class="meta-nav screen-reader-text">' . __('Page', 'certifications-plugin') . ' </span>',
								'class' => 'certifications-pagination',
							));
							?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

<?php
get_footer();