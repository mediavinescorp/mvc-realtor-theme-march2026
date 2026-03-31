<?php
$ghl_form  = get_field( 'ghl_form_embed', 'option' );
$phone     = get_field( 'phone_number', 'option' );
?>

<section class="lead-form">
    <div class="lead-form__inner">

        <div class="lead-form__header">
            <h2 class="lead-form__heading">Save time, call or message me now</h2>
            <p class="lead-form__subtext">Listings tell you about the home. I can tell you about the home, neighborhood, and everything in between. You don't want just a home — you need an entire guide to find the perfect buying or selling solution catered to your unique needs.</p>
        </div>

        <div class="lead-form__content">

            <!-- Contact Info -->
            <div class="lead-form__contact">
                <h3 class="lead-form__contact-heading">Get in Touch</h3>
                <?php if ( $phone ) : ?>
                    <a href="tel:<?php echo esc_attr( $phone ); ?>" class="lead-form__contact-item">
                        <span class="lead-form__contact-icon">📞</span>
                        <?php echo esc_html( $phone ); ?>
                    </a>
                <?php endif; ?>
                <div class="lead-form__profiles">
                    <p class="lead-form__profiles-heading">Find me online:</p>
                    <?php
                    $realtor = get_field( 'social_realtor', 'option' );
                    $zillow  = get_field( 'social_zillow', 'option' );
                    $homes   = get_field( 'social_homes', 'option' );
                    ?>
                    <?php if ( $realtor ) : ?>
                        <a href="<?php echo esc_url( $realtor ); ?>" target="_blank" rel="noopener" class="lead-form__profile-link">Realtor.com</a>
                    <?php endif; ?>
                    <?php if ( $zillow ) : ?>
                        <a href="<?php echo esc_url( $zillow ); ?>" target="_blank" rel="noopener" class="lead-form__profile-link">Zillow</a>
                    <?php endif; ?>
                    <?php if ( $homes ) : ?>
                        <a href="<?php echo esc_url( $homes ); ?>" target="_blank" rel="noopener" class="lead-form__profile-link">Homes.com</a>
                    <?php endif; ?>
                </div>
            </div>

            <!-- GHL Form -->
            <div class="lead-form__form">
                <?php if ( $ghl_form ) : ?>
                    <?php echo $ghl_form; ?>
                <?php else : ?>
                    <p class="lead-form__no-form">Form coming soon. Please call us directly at <a href="tel:<?php echo esc_attr( $phone ); ?>"><?php echo esc_html( $phone ); ?></a></p>
                <?php endif; ?>
            </div>

        </div>

    </div>
</section>