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

                            <div class="certifications-grid row">
                                <?php if ( have_posts() ) : ?>
                                    <?php while ( have_posts() ) : the_post(); ?>
                                        <div class="certification-item col small-12 large-3">
                                            <a href="<?php the_permalink(); ?>" class="certification-link">
                                                <div class="certification-card">
                                                    <div class="certification-card-body">
                                                        <?php if ( has_post_thumbnail() ) : ?>
                                                            <div class="certification-image">
                                                                <?php the_post_thumbnail( 'medium', array( 'class' => 'certification-thumbnail' ) ); ?>
                                                            </div>
                                                        <?php endif; ?>

                                                        <div class="certification-content">
                                                            <h3 class="certification-title"><?php the_title(); ?></h3>
                                                            <div class="certification-description">
                                                                <?php the_excerpt(); ?>
                                                            </div>
                                                            <div class="certification-button">
                                                                <span class="button secondary">Learn More</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                    <?php endwhile; ?>
                                <?php else : ?>
                                    <div class="col small-12">
                                        <p class="no-certifications">No certifications found.</p>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <?php the_posts_pagination(); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

<?php
get_footer();