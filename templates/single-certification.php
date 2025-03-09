<?php
/**
 * The template for displaying single certification posts
 *
 * @package Certifications_Plugin
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Get the Flatsome theme header
get_header();

// Start the main content
?>
    <main id="main" class="certifications-single">
        <div class="certifications-section-wrapper certifications-credentials-header">
            <div class="certifications-container">
                <div class="certifications-row">
                    <div class="certifications-col-12">
						<?php echo do_shortcode('[block id="certifications-header"]'); ?>
                    </div>
                </div>
            </div>
        </div>
		<?php while ( have_posts() ) : the_post(); ?>

			<?php
			// Get field values
			$intro = get_field('intro');
			$prepare_apply = get_field('prepare_&_apply');
			$get_certified = get_field('get_certified');
			$after_exam = get_field('after_the_exam');
			$documents = get_field('documents');
			// Get button URLs
			$apply_button_url = get_field('apply_button_url') ?: '#';
			$renew_button_url = get_field('renew_button_url') ?: '#';
			?>

            <div class="certifications-container-of-boxes">
                <!-- Sticky Menu Navigation -->
                <div class="certifications-sticky-top" id="certifications-sticky-menu">
                    <div class="certifications-container">
                        <div class="certifications-row">
                            <div class="certifications-col-md-12">
                                <section class="certifications-credential-sections-links">
                                    <nav>
                                        <ul class="certifications-list-inline certifications-flex-container certifications-sticky-menu">
                                            <li class="certifications-list-inline-item">
                                                <a href="#certifications-overview">Overview</a>
                                            </li>
											<?php if ($intro) : ?>
                                                <li class="certifications-list-inline-item">
                                                    <a href="#certifications-intro">INTRO</a>
                                                </li>
											<?php endif; ?>
											<?php if ($prepare_apply) : ?>
                                                <li class="certifications-list-inline-item">
                                                    <a href="#certifications-prepare-apply">PREPARE &amp; APPLY</a>
                                                </li>
											<?php endif; ?>
											<?php if ($get_certified) : ?>
                                                <li class="certifications-list-inline-item">
                                                    <a href="#certifications-get-certified">GET CERTIFIED</a>
                                                </li>
											<?php endif; ?>
											<?php if ($after_exam) : ?>
                                                <li class="certifications-list-inline-item">
                                                    <a href="#certifications-after-the-exam">AFTER THE EXAM</a>
                                                </li>
											<?php endif; ?>
											<?php if ($documents) : ?>
                                                <li class="certifications-list-inline-item">
                                                    <a href="#certifications-documents">DOCUMENTS</a>
                                                </li>
											<?php endif; ?>
                                        </ul>
                                    </nav>
                                </section>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Overview Section -->
                <div id="certifications-overview" class="certifications-section-wrapper">
                    <div class="certifications-container certifications-pb-3">
                        <div class="certifications-row certifications-pt-0">
                            <div class="certifications-col-12">
                                <div class="certifications-pt-2">
                                    <p><?php echo get_the_excerpt(); ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Intro Section -->
				<?php if ($intro) : ?>
                    <div id="certifications-intro" class="certifications-section-wrapper certifications-alternate-background-lightgrey">
                        <div class="certifications-container certifications-pb-5 certifications-pt-5">
                            <div class="certifications-row">
                                <div class="certifications-col-12">
                                    <div class="certifications-section-content">
                                        <section class="certifications-pt-5">
                                            <h3 class="certifications-text-to-uppercase">INTRO</h3>
                                            <span class="certifications-back-to-top">
                                                <a href="#certifications-sticky-menu">Back to Top</a>
                                             </span>

                                            <div class="certifications-row">
                                                <div class="certifications-col-md-9">
                                                    <div class="certifications-field-content">
														<?php echo $intro; ?>

                                                        <!-- Action Buttons -->
                                                        <div class="certifications-action-buttons">
                                                            <a href="<?php echo esc_url($apply_button_url); ?>" class="certifications-btn certifications-primary">APPLY NOW</a>
                                                            <a href="<?php echo esc_url($renew_button_url); ?>" class="certifications-btn certifications-primary">RENEW</a>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="certifications-col-md-3">
													<?php if (has_post_thumbnail()) : ?>
                                                        <div class="certifications-featured-image">
															<?php
															$id = get_the_ID();
															echo get_the_post_thumbnail($id, 'medium', array(
																'class' => 'certifications-thumbnail',
																'loading' => 'lazy'
															));
															?>
                                                        </div>
													<?php endif; ?>
                                                </div>
                                            </div>
                                        </section>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
				<?php endif; ?>

                <!-- Prepare & Apply Section -->
				<?php if ($prepare_apply) : ?>
                    <div id="certifications-prepare-apply" class="certifications-section-wrapper certifications-alternate-background-white">
                        <div class="certifications-container certifications-pb-5 certifications-pt-5">
                            <div class="certifications-row">
                                <div class="certifications-col-12">
                                    <div class="certifications-section-content">
                                        <section class="certifications-pt-5">
                                            <h3 class="certifications-text-to-uppercase">PREPARE &amp; APPLY</h3>
                                            <span class="certifications-back-to-top">
                                                <a href="#certifications-sticky-menu">Back to Top</a>
                                            </span>
                                            <div class="certifications-field-content">
												<?php echo $prepare_apply; ?>
                                            </div>
                                        </section>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
				<?php endif; ?>

                <!-- Get Certified Section -->
				<?php if ($get_certified) : ?>
                    <div id="certifications-get-certified" class="certifications-section-wrapper certifications-alternate-background-lightgrey">
                        <div class="certifications-container certifications-pb-5 certifications-pt-5">
                            <div class="certifications-row">
                                <div class="certifications-col-12">
                                    <div class="certifications-section-content">
                                        <section class="certifications-pt-5">
                                            <h3 class="certifications-text-to-uppercase">GET CERTIFIED</h3>
                                            <span class="certifications-back-to-top">
                                                <a href="#certifications-sticky-menu">Back to Top</a>
                                            </span>
                                            <div class="certifications-field-content">
												<?php echo $get_certified; ?>
                                            </div>
                                        </section>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
				<?php endif; ?>

                <!-- After The Exam Section -->
				<?php if ($after_exam) : ?>
                    <div id="certifications-after-the-exam" class="certifications-section-wrapper certifications-alternate-background-white">
                        <div class="certifications-container certifications-pb-5 certifications-pt-5">
                            <div class="certifications-row">
                                <div class="certifications-col-12">
                                    <div class="certifications-section-content">
                                        <section class="certifications-pt-5">
                                            <h3 class="certifications-text-to-uppercase">AFTER THE EXAM</h3>
                                            <span class="certifications-back-to-top">
                                                <a href="#certifications-sticky-menu">Back to Top</a>
                                            </span>
                                            <div class="certifications-field-content">
												<?php echo $after_exam; ?>
                                            </div>
                                        </section>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
				<?php endif; ?>

                <!-- Documents Section -->
				<?php if ($documents) : ?>
                    <div id="certifications-documents" class="certifications-section-wrapper certifications-alternate-background-lightgrey">
                        <div class="certifications-container certifications-pb-5 certifications-pt-5">
                            <div class="certifications-row">
                                <div class="certifications-col-12">
                                    <div class="certifications-section-content">
                                        <section class="certifications-pt-5">
                                            <h3 class="certifications-text-to-uppercase">DOCUMENTS</h3>
                                            <span class="certifications-back-to-top">
                                                <a href="#certifications-sticky-menu">Back to Top</a>
                                            </span>
                                            <div class="certifications-field-content">
												<?php echo $documents; ?>
                                            </div>
                                        </section>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
				<?php endif; ?>

            </div><!-- .certifications-container-of-boxes -->

		<?php endwhile; ?>
    </main>

<?php
// Get the Flatsome theme footer
get_footer();