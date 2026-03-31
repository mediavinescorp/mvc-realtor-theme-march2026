<?php
$phone = get_field( 'phone_number', 'option' );
?>

<section class="services-strip">
    <div class="services-strip__inner">

        <!-- Card 1: Buy -->
        <div class="services-strip__card">
            <div class="services-strip__icon">🏠</div>
            <h3 class="services-strip__title">Don't waste time getting lost on listing sites. I'll do it for you!</h3>
            <p class="services-strip__desc">Committed to match your dream and needs to the perfect home, environment, community, and amenities.</p>
            <a href="<?php echo esc_url( home_url( '/contact' ) ); ?>" class="services-strip__btn">
                Let's Find Your Perfect Home
            </a>
        </div>

        <!-- Card 2: Sell -->
        <div class="services-strip__card">
            <div class="services-strip__icon">💰</div>
            <h3 class="services-strip__title">Know the true value of your home and get the best bids!</h3>
            <p class="services-strip__desc">With exceptional integrity, negotiating skills and marketing strategies, we promise the best price for your property.</p>
            <a href="<?php echo esc_url( home_url( '/contact' ) ); ?>" class="services-strip__btn">
                Let's Sell Your Home Together
            </a>
        </div>

        <!-- Card 3: Call -->
        <div class="services-strip__card services-strip__card--accent">
            <div class="services-strip__icon">⭐</div>
            <h3 class="services-strip__title">Experience the Gold Standard in Realtor Services</h3>
            <p class="services-strip__desc">Every transaction we undertake is delivered with care, precision and results — no exceptions.</p>
            <?php if ( $phone ) : ?>
                <a href="tel:<?php echo esc_attr( $phone ); ?>" class="services-strip__btn">
                    Call Blayne Pacelli Now
                </a>
            <?php endif; ?>
        </div>

    </div>
</section>