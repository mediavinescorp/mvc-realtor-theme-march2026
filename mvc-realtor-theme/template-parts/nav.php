<?php
$logo     = get_field( 'site_logo', 'option' );
$phone    = get_field( 'phone_number', 'option' );
?>

<header class="site-header">
    <div class="site-header__inner">

        <!-- Logo -->
        <div class="site-header__logo">
            <a href="<?php echo esc_url( home_url( '/' ) ); ?>">
                <?php if ( $logo ) : ?>
                    <img src="<?php echo esc_url( $logo['url'] ); ?>"
                         alt="<?php echo esc_attr( $logo['alt'] ); ?>"
                         width="<?php echo esc_attr( $logo['width'] ); ?>"
                         height="<?php echo esc_attr( $logo['height'] ); ?>">
                <?php else : ?>
                    <?php bloginfo( 'name' ); ?>
                <?php endif; ?>
            </a>
        </div>

        <!-- Main Nav -->
        <nav class="site-nav" aria-label="Main Navigation">
            <ul class="site-nav__list">

                <!-- Research Neighborhoods Dropdown -->
                <li class="site-nav__item site-nav__item--dropdown">
                    <button class="site-nav__link site-nav__dropdown-toggle" 
                            aria-expanded="false" 
                            aria-haspopup="true">
                        Research Neighborhoods
                        <span class="site-nav__arrow" aria-hidden="true">&#9660;</span>
                    </button>
                   <ul class="site-nav__dropdown">
    <!-- View All Cities -->
    <li class="site-nav__dropdown-item site-nav__dropdown-item--all">
        <a href="<?php echo esc_url( get_post_type_archive_link( 'city' ) ); ?>"
           class="site-nav__dropdown-link site-nav__dropdown-link--all">
            🗺 View All Cities
        </a>
    </li>
    <?php
    $cities = new WP_Query( array(
                            'post_type'      => 'city',
                            'posts_per_page' => -1,
                            'orderby'        => 'title',
                            'order'          => 'ASC',
                        ) );
                        if ( $cities->have_posts() ) :
                            while ( $cities->have_posts() ) : $cities->the_post(); ?>
                                <li class="site-nav__dropdown-item">
                                    <a href="<?php the_permalink(); ?>" 
                                       class="site-nav__dropdown-link">
                                        <?php the_title(); ?>
                                    </a>
                                </li>
                            <?php endwhile;
                            wp_reset_postdata();
                        endif; ?>
                    </ul>
                </li>

                <!-- Standard Nav Items -->
                <li class="site-nav__item">
                    <a href="<?php echo esc_url( home_url( '/about-us' ) ); ?>" 
   class="site-nav__link <?php echo is_page( 'about-us' ) ? 'site-nav__link--active' : ''; ?>">Meet Blayne</a>
                </li>
                <li class="site-nav__item">
                 <a href="<?php echo esc_url( home_url( '/news' ) ); ?>" 
   class="site-nav__link <?php echo is_home() ? 'site-nav__link--active' : ''; ?>">News &amp; Guides</a>
                </li>
                <li class="site-nav__item">
                    <a href="<?php echo esc_url( home_url( '/contact' ) ); ?>" 
   class="site-nav__link <?php echo is_page( 'contact' ) ? 'site-nav__link--active' : ''; ?>">Contact</a>
                </li>

            </ul>
        </nav>

        <!-- Mobile CTA -->
        <?php if ( $phone ) : ?>
            <a href="tel:<?php echo esc_attr( $phone ); ?>" 
               class="site-header__cta">
                Call Blayne Now
            </a>
        <?php endif; ?>

        <!-- Mobile Hamburger -->
        <button class="site-header__hamburger" 
                aria-label="Toggle Navigation" 
                aria-expanded="false">
            <span></span>
            <span></span>
            <span></span>
        </button>

    </div>
</header>