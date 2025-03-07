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
    <main id="main" class="certification-single">
    <div class="section-wrapper credentials-header">
        <div class="container">
            <div class="row">
                <div class="col-12">
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

            <div class="container-of-boxes">
                <!-- Sticky Menu Navigation -->
                <div class="sticky-top" id="sticky-menu">
                    <div class="container">
                        <div class="row">
                            <div class="col-md-12">
                                <section class="credential-sections-links">
                                    <nav>
                                        <ul class="list-inline flex-container sticky-menu">
                                            <li class="list-inline-item">
                                                <a href="#overview">Overview</a>
                                            </li>
                                            <?php if ($intro) : ?>
                                                <li class="list-inline-item">
                                                    <a href="#intro">INTRO</a>
                                                </li>
                                            <?php endif; ?>
                                            <?php if ($prepare_apply) : ?>
                                                <li class="list-inline-item">
                                                    <a href="#prepare-apply">PREPARE &amp; APPLY</a>
                                                </li>
                                            <?php endif; ?>
                                            <?php if ($get_certified) : ?>
                                                <li class="list-inline-item">
                                                    <a href="#get-certified">GET CERTIFIED</a>
                                                </li>
                                            <?php endif; ?>
                                            <?php if ($after_exam) : ?>
                                                <li class="list-inline-item">
                                                    <a href="#after-the-exam">AFTER THE EXAM</a>
                                                </li>
                                            <?php endif; ?>
                                            <?php if ($documents) : ?>
                                                <li class="list-inline-item">
                                                    <a href="#documents">DOCUMENTS</a>
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
                <div id="overview" class="section-wrapper">
                    <div class="container pb-3">
                        <div class="row pt-0">
                            <div class="col-12">
                                <div class="pt-2">
                                    <p><?php echo get_the_excerpt(); ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Intro Section -->
                <?php if ($intro) : ?>
                    <div id="intro" class="section-wrapper alternate-background-lightgrey">
                        <div class="container pb-5 pt-5">
                            <div class="row">
                                <div class="col-12">
                                    <div class="section-content">
                                        <section class="pt-5">
                                            <h3 class="text-to-uppercase">INTRO</h3>
                                            <span class="back-to-top">
                                                <a href="#sticky-menu">Back to Top</a>
                                             </span>

                                            <div class="row">
                                                <div class="col-md-9">
                                                    <div class="certification-field-content">
                                                        <?php echo $intro; ?>

                                                        <!-- Action Buttons -->
                                                        <div class="certification-action-buttons">
                                                            <a href="<?php echo esc_url($apply_button_url); ?>" class="button primary">APPLY NOW</a>
                                                            <a href="<?php echo esc_url($renew_button_url); ?>" class="button primary">RENEW</a>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-3">
                                                    <?php if (has_post_thumbnail()) : ?>
                                                        <div class="certification-featured-image">
                                                            <?php
                                                            $id = get_the_ID();
                                                            echo get_the_post_thumbnail($id, 'medium', array(
                                                                'class' => 'certification-thumbnail',
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
                    <div id="prepare-apply" class="section-wrapper alternate-background-white">
                        <div class="container pb-5 pt-5">
                            <div class="row">
                                <div class="col-12">
                                    <div class="section-content">
                                        <section class="pt-5">
                                            <h3 class="text-to-uppercase">PREPARE &amp; APPLY</h3>
                                            <span class="back-to-top">
                                        <a href="#sticky-menu">Back to Top</a>
                                    </span>
                                            <div class="certification-field-content">
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
                    <div id="get-certified" class="section-wrapper alternate-background-lightgrey">
                        <div class="container pb-5 pt-5">
                            <div class="row">
                                <div class="col-12">
                                    <div class="section-content">
                                        <section class="pt-5">
                                            <h3 class="text-to-uppercase">GET CERTIFIED</h3>
                                            <span class="back-to-top">
                                        <a href="#sticky-menu">Back to Top</a>
                                    </span>
                                            <div class="certification-field-content">
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
                    <div id="after-the-exam" class="section-wrapper alternate-background-white">
                        <div class="container pb-5 pt-5">
                            <div class="row">
                                <div class="col-12">
                                    <div class="section-content">
                                        <section class="pt-5">
                                            <h3 class="text-to-uppercase">AFTER THE EXAM</h3>
                                            <span class="back-to-top">
                                        <a href="#sticky-menu">Back to Top</a>
                                    </span>
                                            <div class="certification-field-content">
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
                    <div id="documents" class="section-wrapper alternate-background-lightgrey">
                        <div class="container pb-5 pt-5">
                            <div class="row">
                                <div class="col-12">
                                    <div class="section-content">
                                        <section class="pt-5">
                                            <h3 class="text-to-uppercase">DOCUMENTS</h3>
                                            <span class="back-to-top">
                                        <a href="#sticky-menu">Back to Top</a>
                                    </span>
                                            <div class="certification-field-content">
                                                <?php echo $documents; ?>
                                            </div>
                                        </section>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

            </div><!-- .container-of-boxes -->

        <?php endwhile; ?>
    </main>

<?php
// Get the Flatsome theme footer
get_footer();