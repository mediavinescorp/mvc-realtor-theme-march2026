<?php
$posts = new WP_Query( array(
    'post_type'      => 'post',
    'posts_per_page' => 6,
    'orderby'        => 'date',
    'order'          => 'DESC',
) );
?>

<?php if ( $posts->have_posts() ) : ?>
<section class="blog-feed">
    <div class="blog-feed__inner">

        <div class="blog-feed__header">
            <h2 class="blog-feed__heading">Real Estate Blog</h2>
            <p class="blog-feed__subtext">Gain world class cutting edge real-estate articles on buying homes, selling homes, interest rates, and stay up to date with anything and everything real-estate related.</p>
        </div>

        <div class="blog-feed__grid">
            <?php while ( $posts->have_posts() ) : $posts->the_post(); ?>
                <article class="blog-feed__card">
                    <?php if ( has_post_thumbnail() ) : ?>
                        <a href="<?php the_permalink(); ?>" class="blog-feed__card-image-wrap">
                            <?php the_post_thumbnail( 'medium_large', array( 'class' => 'blog-feed__card-image' ) ); ?>
                        </a>
                    <?php endif; ?>
                    <div class="blog-feed__card-content">
                        <div class="blog-feed__card-meta">
                            <span class="blog-feed__card-author">By <?php the_author(); ?></span>
                            <span class="blog-feed__card-sep">•</span>
                            <span class="blog-feed__card-date"><?php echo get_the_date( 'F j, Y' ); ?></span>
                        </div>
                        <h3 class="blog-feed__card-title">
                            <a href="<?php the_permalink(); ?>">
                                <?php the_title(); ?>
                            </a>
                        </h3>
                        <p class="blog-feed__card-excerpt">
                            <?php echo wp_trim_words( get_the_excerpt(), 20, '...' ); ?>
                        </p>
                        <a href="<?php the_permalink(); ?>" class="blog-feed__card-link">
                            Read More →
                        </a>
                    </div>
                </article>
            <?php endwhile; wp_reset_postdata(); ?>
        </div>

        <div class="blog-feed__cta">
            <a href="<?php echo esc_url( home_url( '/news' ) ); ?>" class="blog-feed__btn">
                View All Articles
            </a>
        </div>

    </div>
</section>
<?php endif; ?>