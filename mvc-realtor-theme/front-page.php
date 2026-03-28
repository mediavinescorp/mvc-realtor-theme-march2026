<?php get_header(); ?>

<?php get_template_part( 'template-parts/hero' ); ?>
<?php get_template_part( 'template-parts/services-strip' ); ?>
<?php get_template_part( 'template-parts/testimonial-videos' ); ?>
<?php get_template_part( 'template-parts/stats-bar' ); ?>
<?php get_template_part( 'template-parts/about-bio' ); ?>
<?php get_template_part( 'template-parts/gallery-carousel' ); ?>
<?php get_template_part( 'template-parts/cities-grid' ); ?>

<?php blayne_faq_section( [
    'heading' => 'Common Real Estate Questions',
    'limit'   => 6,
] ); ?>

<!-- Google Reviews -->
<section class="reviews-section">
    <div class="reviews-section__inner">
        <h2 class="reviews-section__heading">What Our Clients Say</h2>
        <h3 class="reviews-section__sub">Real reviews from real clients on Google</h3>
        <?php echo do_shortcode('[trustindex no-registration=google]'); ?>
    </div>
</section>

<!-- Lead Form -->
<?php get_template_part( 'template-parts/lead-form' ); ?>

<?php get_template_part( 'template-parts/blog-feed' ); ?>

<?php get_footer(); ?>