<?php
$logo          = get_field( 'site_logo', 'option' );
$phone         = get_field( 'phone_number', 'option' );
$email         = get_field( 'email_address', 'option' );
$address       = get_field( 'address', 'option' );
$city_state    = get_field( 'city_state_zip', 'option' );
$facebook      = get_field( 'social_facebook', 'option' );
$linkedin      = get_field( 'social_linkedin', 'option' );
$youtube       = get_field( 'social_youtube', 'option' );
$realtor       = get_field( 'social_realtor', 'option' );
$zillow        = get_field( 'social_zillow', 'option' );
$homes         = get_field( 'social_homes', 'option' );
?>

<div class="site-footer__search-bar">
    <div class="site-footer__search-inner">
        <h3 class="site-footer__search-heading">Search Homes in Los Angeles County</h3>
      <?php echo do_shortcode('[idx-omnibar styles="1" extra="0" min_price="0" remove_price_validation="0" ]'); ?>
    </div>
</div>

<footer class="site-footer">
    <div class="site-footer__inner">

        <!-- Column 1: Logo + About -->
        <div class="site-footer__col site-footer__col--about">
            <?php if ( $logo ) : ?>
                <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="site-footer__logo">
                    <img src="<?php echo esc_url( $logo['url'] ); ?>"
                         alt="<?php echo esc_attr( $logo['alt'] ); ?>">
                </a>
            <?php endif; ?>
            <p class="site-footer__tagline">
                The #1 Realtor for home buyers and home sellers throughout Greater Los Angeles County.
            </p>
            <!-- Social Links -->
            <div class="site-footer__social">
                <?php if ( $facebook ) : ?>
                    <a href="<?php echo esc_url( $facebook ); ?>" target="_blank" rel="noopener" class="site-footer__social-link">Facebook</a>
                <?php endif; ?>
                <?php if ( $linkedin ) : ?>
                    <a href="<?php echo esc_url( $linkedin ); ?>" target="_blank" rel="noopener" class="site-footer__social-link">LinkedIn</a>
                <?php endif; ?>
                <?php if ( $youtube ) : ?>
                    <a href="<?php echo esc_url( $youtube ); ?>" target="_blank" rel="noopener" class="site-footer__social-link">YouTube</a>
                <?php endif; ?>
            </div>
        </div>

        <!-- Column 2: Quick Links -->
        <div class="site-footer__col site-footer__col--links">
            <h4 class="site-footer__heading">Quick Links</h4>
            <ul class="site-footer__list">
                <li><a href="<?php echo esc_url( home_url( '/' ) ); ?>">Home</a></li>
                <li><a href="<?php echo esc_url( home_url( '/about-us' ) ); ?>">Meet Blayne</a></li>
                <li><a href="<?php echo esc_url( home_url( '/news' ) ); ?>">News &amp; Guides</a></li>
                <li><a href="<?php echo esc_url( home_url( '/contact' ) ); ?>">Contact</a></li>
                <li><a href="<?php echo esc_url( home_url( '/privacy-policy' ) ); ?>">Privacy Policy</a></li>
                <li><a href="<?php echo esc_url( home_url( '/terms-of-service' ) ); ?>">Terms of Service</a></li>
            </ul>
            <!-- Online Profiles -->
            <h4 class="site-footer__heading">Find Me Online</h4>
            <ul class="site-footer__list">
                <?php if ( $realtor ) : ?>
                    <li><a href="<?php echo esc_url( $realtor ); ?>" target="_blank" rel="noopener">Realtor.com</a></li>
                <?php endif; ?>
                <?php if ( $zillow ) : ?>
                    <li><a href="<?php echo esc_url( $zillow ); ?>" target="_blank" rel="noopener">Zillow</a></li>
                <?php endif; ?>
                <?php if ( $homes ) : ?>
                    <li><a href="<?php echo esc_url( $homes ); ?>" target="_blank" rel="noopener">Homes.com</a></li>
                <?php endif; ?>
            </ul>
        </div>

        <!-- Column 3: Cities -->
        <div class="site-footer__col site-footer__col--cities">
            <h4 class="site-footer__heading">Areas We Serve</h4>
            <ul class="site-footer__cities">
                <?php
              $cities = new WP_Query( array(
    'post_type'      => 'city',
    'posts_per_page' => 10,
    'orderby'        => 'rand',
) );
                if ( $cities->have_posts() ) :
                    while ( $cities->have_posts() ) : $cities->the_post(); ?>
                        <li><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></li>
                    <?php endwhile;
                    wp_reset_postdata();
                endif; ?>
           </ul>
            <a href="<?php echo esc_url( home_url( '/cities' ) ); ?>" class="site-footer__view-all">
                View All Cities →
            </a>
        </div>

        <!-- Column 4: Contact -->
        <div class="site-footer__col site-footer__col--contact">
            <h4 class="site-footer__heading">Contact Us</h4>
            <?php if ( $address ) : ?>
                <p class="site-footer__address">
                    <?php echo esc_html( $address ); ?><br>
                    <?php echo esc_html( $city_state ); ?>
                </p>
            <?php endif; ?>
            <?php if ( $phone ) : ?>
                <a href="tel:<?php echo esc_attr( $phone ); ?>" class="site-footer__phone">
                    <?php echo esc_html( $phone ); ?>
                </a>
            <?php endif; ?>
            <?php if ( $email ) : ?>
                <a href="mailto:<?php echo esc_attr( $email ); ?>" class="site-footer__email">
                    <?php echo esc_html( $email ); ?>
                </a>
            <?php endif; ?>
        </div>

    </div>

    <!-- Footer Bottom Bar -->
    <div class="site-footer__bottom">
        <div class="site-footer__bottom-inner">
            <span class="site-footer__copyright">
                &copy; <?php echo date( 'Y' ); ?> All Rights Reserved | Blayne Pacelli Realtor
            </span>
            <span class="site-footer__credit">
                Digital Marketing by <a href="https://www.mediavines.com/" target="_blank" rel="noopener">Media Vines Corp</a>
            </span>
        </div>
    </div>

</footer>