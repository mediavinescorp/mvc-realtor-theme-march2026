<?php get_header(); ?>

<div class="single-post">
    <div class="single-post__inner">

        <!-- Main Content -->
        <article class="single-post__content">

            <!-- Post Header -->
            <header class="single-post__header">
                <div class="single-post__meta">
                    <span class="single-post__author">By <?php the_author(); ?></span>
                    <span class="single-post__sep">•</span>
                    <span class="single-post__date"><?php echo get_the_date( 'F j, Y' ); ?></span>
                    <?php
                    $categories = get_the_category();
                    if ( $categories ) : ?>
                        <span class="single-post__sep">•</span>
                        <?php foreach ( $categories as $cat ) : ?>
                            <a href="<?php echo esc_url( get_category_link( $cat->term_id ) ); ?>"
                               class="single-post__cat">
                                <?php echo esc_html( $cat->name ); ?>
                            </a>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <h1 class="single-post__title"><?php the_title(); ?></h1>

                <?php if ( has_post_thumbnail() ) : ?>
                    <div class="single-post__featured-image">
                        <?php the_post_thumbnail( 'large', array( 'class' => 'single-post__image' ) ); ?>
                    </div>
                <?php endif; ?>
            </header>

            <!-- Post Body -->
            <div class="single-post__body">
                <?php the_content(); ?>
            </div>

            <!-- Post Footer -->
            <footer class="single-post__footer">
                <div class="single-post__tags">
                    <?php the_tags( '<span class="single-post__tag-label">Tags: </span>', ', ', '' ); ?>
                </div>
                <div class="single-post__nav">
                    <?php
                    the_post_navigation( array(
                        'prev_text' => '← %title',
                        'next_text' => '%title →',
                    ) );
                    ?>
                </div>
            </footer>

        </article>

        <!-- Sidebar -->
        <aside class="single-post__sidebar">

            <!-- Blayne Bio -->
            <?php
            $photo = get_field( 'blayne_photo', 'option' );
            $phone = get_field( 'phone_number', 'option' );
            ?>
            <div class="sidebar__widget sidebar__bio">
                <?php if ( $photo ) : ?>
                    <img src="<?php echo esc_url( $photo['url'] ); ?>"
                         alt="<?php echo esc_attr( $photo['alt'] ); ?>"
                         class="sidebar__bio-photo">
                <?php endif; ?>
                <h3 class="sidebar__bio-name">Blayne Pacelli</h3>
                <p class="sidebar__bio-title">Realtor — Rodeo Realty</p>
                <p class="sidebar__bio-text">Your dedicated real estate agent serving Greater Los Angeles. 20+ years of experience matching families to their perfect home.</p>
                <?php if ( $phone ) : ?>
                    <a href="tel:<?php echo esc_attr( $phone ); ?>"
                       class="sidebar__bio-btn">
                        Call <?php echo esc_html( $phone ); ?>
                    </a>
                <?php endif; ?>
            </div>

            <!-- Lead Form -->
            <?php $ghl_form = get_field( 'ghl_form_embed', 'option' ); ?>
            <?php if ( $ghl_form ) : ?>
            <div class="sidebar__widget sidebar__form">
                <h3 class="sidebar__widget-heading">Get a Free Consultation</h3>
                <?php echo $ghl_form; ?>
            </div>
            <?php endif; ?>

            <!-- City Links -->
            <div class="sidebar__widget sidebar__cities">
                <h3 class="sidebar__widget-heading">Areas We Serve</h3>
                <?php
                $cities = new WP_Query( array(
                    'post_type'      => 'city',
                    'posts_per_page' => -1,
                    'orderby'        => 'title',
                    'order'          => 'ASC',
                ) );
                if ( $cities->have_posts() ) : ?>
                    <ul class="sidebar__cities-list">
                        <?php while ( $cities->have_posts() ) : $cities->the_post(); ?>
                            <li>
                                <a href="<?php the_permalink(); ?>" class="sidebar__city-link">
                                    <?php the_title(); ?>
                                </a>
                            </li>
                        <?php endwhile; wp_reset_postdata(); ?>
                    </ul>
                <?php endif; ?>
            </div>

            <!-- Recent Posts -->
            <div class="sidebar__widget sidebar__recent">
                <h3 class="sidebar__widget-heading">Recent Articles</h3>
                <?php
                $recent = new WP_Query( array(
                    'post_type'      => 'post',
                    'posts_per_page' => 4,
                    'orderby'        => 'date',
                    'order'          => 'DESC',
                    'post__not_in'   => array( get_the_ID() ),
                ) );
                if ( $recent->have_posts() ) : ?>
                    <ul class="sidebar__recent-list">
                        <?php while ( $recent->have_posts() ) : $recent->the_post(); ?>
                            <li class="sidebar__recent-item">
                                <?php if ( has_post_thumbnail() ) : ?>
                                    <a href="<?php the_permalink(); ?>" class="sidebar__recent-image-wrap">
                                        <?php the_post_thumbnail( 'thumbnail', array( 'class' => 'sidebar__recent-image' ) ); ?>
                                    </a>
                                <?php endif; ?>
                                <div class="sidebar__recent-content">
                                    <a href="<?php the_permalink(); ?>" class="sidebar__recent-title">
                                        <?php the_title(); ?>
                                    </a>
                                    <span class="sidebar__recent-date"><?php echo get_the_date( 'M j, Y' ); ?></span>
                                </div>
                            </li>
                        <?php endwhile; wp_reset_postdata(); ?>
                    </ul>
                <?php endif; ?>
            </div>


<!-- Related Posts -->
            <div class="sidebar__widget sidebar__related">
                <h3 class="sidebar__widget-heading">Related Articles</h3>
                <?php
                $categories   = get_the_category();
                $category_ids = array();
                foreach ( $categories as $cat ) {
                    $category_ids[] = $cat->term_id;
                }
                $related = new WP_Query( array(
                    'post_type'      => 'post',
                    'posts_per_page' => 3,
                    'orderby'        => 'rand',
                    'post__not_in'   => array( get_the_ID() ),
                    'category__in'   => $category_ids,
                ) );
                if ( $related->have_posts() ) : ?>
                    <ul class="sidebar__recent-list">
                        <?php while ( $related->have_posts() ) : $related->the_post(); ?>
                            <li class="sidebar__recent-item">
                                <?php if ( has_post_thumbnail() ) : ?>
                                    <a href="<?php the_permalink(); ?>" class="sidebar__recent-image-wrap">
                                        <?php the_post_thumbnail( 'thumbnail', array( 'class' => 'sidebar__recent-image' ) ); ?>
                                    </a>
                                <?php endif; ?>
                                <div class="sidebar__recent-content">
                                    <a href="<?php the_permalink(); ?>" class="sidebar__recent-title">
                                        <?php the_title(); ?>
                                    </a>
                                    <span class="sidebar__recent-date"><?php echo get_the_date( 'M j, Y' ); ?></span>
                                </div>
                            </li>
                        <?php endwhile; wp_reset_postdata(); ?>
                    </ul>
                <?php else : ?>
                    <p class="sidebar__no-related">No related articles found.</p>
                <?php endif; ?>
            </div>

        </aside>

    </div>
</div>

<?php blayne_faq_section( [
    'heading' => 'Related Real Estate Questions',
    'limit'   => 4,
] ); ?>

<?php get_footer(); ?>