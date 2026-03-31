<?php
$photo        = get_field( 'blayne_photo', 'option' );
$years        = get_field( 'years_experience', 'option' );
$listing_rate = get_field( 'listing_price_rate', 'option' );
$closing_rate = get_field( 'closing_success_rate', 'option' );
?>

<section class="about-bio">
    <div class="about-bio__inner">

        <!-- Photo -->
        <div class="about-bio__photo-wrap">
            <?php if ( $photo ) : ?>
                <img src="<?php echo esc_url( $photo['url'] ); ?>"
                     alt="<?php echo esc_attr( $photo['alt'] ); ?>"
                     class="about-bio__photo">
            <?php endif; ?>
        </div>

        <!-- Content -->
        <div class="about-bio__content">
            <h2 class="about-bio__heading">
                <?php echo $years ? esc_html( $years ) . ' Years' : '20 Years'; ?> Real Estate Experience
            </h2>
            <p class="about-bio__text">
                Selling and buying homes in the Greater Los Angeles area can be overwhelming. My mission is not just to find the perfect home or sell your home quickly (which I will), but rather to ensure that the neighborhood, amenities, restaurants, schools, and anything else that matters to you, is part of the package deal.
            </p>
            <p class="about-bio__text">
                I work night and day to become your dream home matchmaker. Let's work together to make your real estate transactions smooth and successful. Whether you're buying, selling, or just exploring, I'm committed to helping you meet your goals.
            </p>

            <!-- Stats -->
            <div class="about-bio__stats">
                <?php if ( $listing_rate ) : ?>
                <div class="about-bio__stat">
                    <span class="about-bio__stat-number"><?php echo esc_html( $listing_rate ); ?></span>
                    <span class="about-bio__stat-label">Listing Price Rate</span>
                </div>
                <?php endif; ?>
                <?php if ( $closing_rate ) : ?>
                <div class="about-bio__stat">
                    <span class="about-bio__stat-number"><?php echo esc_html( $closing_rate ); ?></span>
                    <span class="about-bio__stat-label">Closing Success Rate</span>
                </div>
                <?php endif; ?>
            </div>

            <a href="<?php echo esc_url( home_url( '/about-us' ) ); ?>"
               class="about-bio__btn">
                More About Blayne
            </a>
        </div>

    </div>
</section>