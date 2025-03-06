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
        <?php while ( have_posts() ) : the_post(); ?>

            <?php
            // Get field values
            $intro = get_field('intro');
            $prepare_apply = get_field('prepare_&_apply');
            $get_certified = get_field('get_certified');
            $after_exam = get_field('after_the_exam');
            $documents = get_field('documents');
            ?>

            <div class="container-of-boxes">
                <!-- Sticky Menu Navigation -->
                <div class="row sticky-top" id="sticky-menu">
                    <div class="col-md-12">
                        <section class="credential-sections-links">
                            <div class="container">
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
                            </div>
                        </section>
                    </div>
                </div>

                <!-- Social Sharing Section -->
                <div class="row mt-5">
                    <div class="container">
                        <div class="blog-detail-metadata-share col-12">
                            <div class="social-sharing float-right">
                                <ul class="share-tools inline-list">
                                    <li class="list-inline-item twitter">
                                        <a href="https://twitter.com/intent/tweet?url=<?php echo urlencode(get_permalink()); ?>" target="_blank">
                                            <span class="icon-twitter"></span>
                                            <span class="social-media-text">Tweet</span>
                                        </a>
                                    </li>
                                    <li class="list-inline-item facebook">
                                        <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode(get_permalink()); ?>&amp;display=popup&amp;ref=plugin&amp;src=share_button" target="_blank">
                                            <span class="icon-facebook"></span>
                                            <span class="social-media-text">Share</span>
                                        </a>
                                    </li>
                                    <li class="list-inline-item linkedin">
                                        <a href="https://www.linkedin.com/shareArticle?mini=true&amp;url=<?php echo urlencode(get_permalink()); ?>&amp;title=<?php echo urlencode(get_the_title()); ?>" target="_blank">
                                            <span class="icon-linkedin"></span>
                                            <span class="social-media-text">Share</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Overview Section -->
                <div class="row overview pt-0" id="overview">
                    <div class="container pb-5">
                        <div class="checkmark-heading-green pt-3">
                            <h2><?php the_title(); ?> <i class="icon-checkmark"></i></h2>
                            <p><?php echo get_the_excerpt(); ?></p>
                        </div>
                    </div>
                </div>

                <!-- Intro Section -->
                <?php if ($intro) : ?>
                    <div class="row alternate-background-lightgrey" id="intro">
                        <div class="container pb-5 pt-5 section-content">
                            <section class="pt-5">
                                <h3 class="text-to-uppercase">INTRO</h3>
                                <span class="back-to-top">
                            <a href="#sticky-menu">Back to Top</a>
                        </span>
                                <div class="certification-field-content">
                                    <?php echo $intro; ?>
                                </div>
                            </section>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Prepare & Apply Section -->
                <?php if ($prepare_apply) : ?>
                    <div class="row alternate-background-white" id="prepare-apply">
                        <div class="container pb-5 pt-5 section-content">
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
                <?php endif; ?>

                <!-- Get Certified Section -->
                <?php if ($get_certified) : ?>
                    <div class="row alternate-background-lightgrey" id="get-certified">
                        <div class="container pb-5 pt-5 section-content">
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
                <?php endif; ?>

                <!-- After The Exam Section -->
                <?php if ($after_exam) : ?>
                    <div class="row alternate-background-white" id="after-the-exam">
                        <div class="container pb-5 pt-5 section-content">
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
                <?php endif; ?>

                <!-- Documents Section -->
                <?php if ($documents) : ?>
                    <div class="row alternate-background-lightgrey" id="documents">
                        <div class="container pb-5 pt-5 section-content">
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
                <?php endif; ?>

            </div><!-- .container-of-boxes -->

        <?php endwhile; ?>
    </main>

<?php
// Get the Flatsome theme footer
get_footer();