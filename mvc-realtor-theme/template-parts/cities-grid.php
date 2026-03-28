<?php
$cities = new WP_Query( array(
    'post_type'      => 'city',
    'posts_per_page' => -1,
    'orderby'        => 'title',
    'order'          => 'ASC',
) );
?>

<section class="cities-grid">
    <div class="cities-grid__inner">

        <div class="cities-grid__header">
            <h2 class="cities-grid__heading">Finding the perfect city is the first step, explore each city within my reach!</h2>
            <p class="cities-grid__subtext">Finding the perfect home for sale is not just about the home. So we've begun the research for you. Visit our cities of real estate services to learn about every city you're considering from the population, local restaurants, schools, and more. We're not in the real estate business, we're in the matching business where we find the perfect lifestyle solution for you. If you don't find your city just call me and I'll either help you directly or help you find one of my partners to assist.</p>
        </div>

      <?php if ( $cities->have_posts() ) : ?>
        <div class="cities-grid__badges">
            <?php while ( $cities->have_posts() ) : $cities->the_post(); ?>
                <a href="<?php the_permalink(); ?>" class="cities-grid__badge">
                    <?php the_title(); ?>
                </a>
            <?php endwhile; wp_reset_postdata(); ?>
        </div>
        <?php endif; ?>

        <div class="cities-grid__cta">
            <p class="cities-grid__cta-text">Don't see your city? <a href="<?php echo esc_url( home_url( '/contact' ) ); ?>">Contact Blayne directly</a> and he'll help you find the right solution.</p>
        </div>

    </div>
</section>