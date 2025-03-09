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

    <main id="main" class="certifications-archive">
        <div class="certifications-page-wrapper">
            <div class="certifications-container">
                <div class="certifications-row">
                    <div class="certifications-col certifications-large-12">
                        <div class="certifications-col-inner">
                            <header class="certifications-page-header">
                                <h1 class="certifications-page-title"><?php post_type_archive_title(); ?></h1>
                            </header>

							<?php
							// Add container class based on display type
							$container_class = 'certifications-container';
							if ($display_type === 'grid') {
								$container_class .= ' certifications-grid certifications-row';
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
												$image = get_the_post_thumbnail($id, $image_size, array('class' => 'certifications-thumbnail'));
											} elseif (function_exists('get_field')) {
												// Try to get field image from ACF if featured image is not set
												$acf_image = get_field('certification_logo', $id);
												if ($acf_image && is_array($acf_image)) {
													$image_src = $acf_image['sizes'][$image_size] ?? $acf_image['url'];
													$image = '<img src="' . esc_url($image_src) . '" alt="' . esc_attr($title) . '" class="certifications-thumbnail" />';
												}
											}
										}

										// Calculate column classes based on display type and columns
										$column_class = 'certifications-item';
										if ($display_type === 'grid') {
											$column_class .= ' certifications-col certifications-small-12 certifications-large-' . (12 / intval($columns));
										} else {
											$column_class .= ' certifications-list-item';
										}
										?>
                                        <div class="<?php echo esc_attr($column_class); ?>">
                                            <div class="certifications-card">
                                                <div class="certifications-card-body">
													<?php if ($image && $show_image) : ?>
                                                        <div class="certifications-image">
                                                            <a href="<?php echo esc_url($permalink); ?>" target="<?php echo esc_attr($link_target); ?>">
																<?php echo $image; ?>
                                                            </a>
                                                        </div>
													<?php endif; ?>

                                                    <div class="certifications-content">
                                                        <h3 class="certifications-title">
                                                            <a href="<?php echo esc_url($permalink); ?>" target="<?php echo esc_attr($link_target); ?>">
																<?php echo esc_html($title); ?>
                                                            </a>
                                                        </h3>

                                                        <div class="certifications-description">
															<?php echo wp_kses_post($description); ?>
                                                        </div>

                                                        <div class="certifications-button">
                                                            <a href="<?php echo esc_url($permalink); ?>" class="certifications-btn certifications-secondary" target="<?php echo esc_attr($link_target); ?>">
																<?php echo esc_html($button_text); ?>
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
									<?php endwhile; ?>
								<?php else : ?>
                                    <div class="certifications-col certifications-small-12">
                                        <p class="certifications-no-results"><?php _e('No certifications found.', 'certifications-plugin'); ?></p>
                                    </div>
								<?php endif; ?>
                            </div>

							<?php
							// Add standard WordPress pagination with custom class names
							$pagination_args = array(
								'prev_text' => '&laquo;',
								'next_text' => '&raquo;',
								'before_page_number' => '<span class="certifications-meta-nav certifications-screen-reader-text">' . __('Page', 'certifications-plugin') . ' </span>',
								'class' => 'certifications-pagination',
								'echo' => false
							);

							$pagination = paginate_links($pagination_args);

							if (!empty($pagination)) {
								// Replace default classes with our custom ones
								$pagination = str_replace('page-numbers', 'certifications-page-numbers', $pagination);
								$pagination = str_replace('prev', 'certifications-prev', $pagination);
								$pagination = str_replace('next', 'certifications-next', $pagination);
								$pagination = str_replace('current', 'certifications-current', $pagination);

								echo '<div class="certifications-pagination">' . $pagination . '</div>';
							}
							?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

<?php
get_footer();