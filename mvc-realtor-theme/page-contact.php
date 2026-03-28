<?php
/*
 * Template Name: Contact Page
 */
get_header();

$phone      = get_field( 'phone_number', 'option' );
$email      = get_field( 'email_address', 'option' );
$address    = get_field( 'address', 'option' );
$city_state = get_field( 'city_state_zip', 'option' );
$ghl_form   = get_field( 'ghl_form_embed', 'option' );
$map_embed  = get_field( 'google_map_embed', 'option' );
$facebook   = get_field( 'social_facebook', 'option' );
$linkedin   = get_field( 'social_linkedin', 'option' );
$youtube    = get_field( 'social_youtube', 'option' );
$realtor    = get_field( 'social_realtor', 'option' );
$zillow     = get_field( 'social_zillow', 'option' );
$homes      = get_field( 'social_homes', 'option' );
?>

<!-- Hero -->
<!-- Hero -->
<?php
$hero_image = get_field( 'hero_image' );
$hero_url   = $hero_image ? $hero_image['url'] : '';
?>
<section class="page-hero" <?php if ( $hero_url ) : ?>style="background-image: url('<?php echo esc_url( $hero_url ); ?>'); background-size: cover; background-position: center;"<?php endif; ?>>
    <div class="page-hero__overlay"></div>
    <div class="page-hero__inner">        <h1 class="page-hero__title">Contact Us</h1>
        <p class="page-hero__tagline">The Best Luxury Real Estate in Southern California</p>
    </div>
    </div>
</section>

<!-- Contact Content -->
<section class="contact-page">
    <div class="contact-page__inner">

        <!-- Contact Info -->
        <div class="contact-page__info">

            <h2 class="contact-page__heading">Get in Touch</h2>

            <div class="contact-page__details">
                <?php if ( $phone ) : ?>
                <div class="contact-page__detail">
                    <span class="contact-page__detail-label">Phone</span>
                    <a href="tel:<?php echo esc_attr( $phone ); ?>" class="contact-page__detail-value">
                        <?php echo esc_html( $phone ); ?>
                    </a>
                </div>
                <?php endif; ?>

                <?php if ( $email ) : ?>
                <div class="contact-page__detail">
                    <span class="contact-page__detail-label">Email</span>
                    <a href="mailto:<?php echo esc_attr( $email ); ?>" class="contact-page__detail-value">
                        <?php echo esc_html( $email ); ?>
                    </a>
                </div>
                <?php endif; ?>

                <?php if ( $address ) : ?>
                <div class="contact-page__detail">
                    <span class="contact-page__detail-label">Address</span>
                    <span class="contact-page__detail-value">
                        <?php echo esc_html( $address ); ?><br>
                        <?php echo esc_html( $city_state ); ?>
                    </span>
                </div>
                <?php endif; ?>
            </div>

            <!-- Social + Online Profiles -->
            <div class="contact-page__profiles">
                <h3 class="contact-page__profiles-heading">Find Me Online</h3>
                <div class="contact-page__profile-links">
                    <?php if ( $realtor ) : ?>
                        <a href="<?php echo esc_url( $realtor ); ?>" target="_blank" rel="noopener" class="contact-page__profile-btn">Realtor.com</a>
                    <?php endif; ?>
                    <?php if ( $zillow ) : ?>
                        <a href="<?php echo esc_url( $zillow ); ?>" target="_blank" rel="noopener" class="contact-page__profile-btn">Zillow</a>
                    <?php endif; ?>
                    <?php if ( $homes ) : ?>
                        <a href="<?php echo esc_url( $homes ); ?>" target="_blank" rel="noopener" class="contact-page__profile-btn">Homes.com</a>
                    <?php endif; ?>
                    <?php if ( $facebook ) : ?>
                        <a href="<?php echo esc_url( $facebook ); ?>" target="_blank" rel="noopener" class="contact-page__profile-btn">Facebook</a>
                    <?php endif; ?>
                    <?php if ( $linkedin ) : ?>
                        <a href="<?php echo esc_url( $linkedin ); ?>" target="_blank" rel="noopener" class="contact-page__profile-btn">LinkedIn</a>
                    <?php endif; ?>
                    <?php if ( $youtube ) : ?>
                        <a href="<?php echo esc_url( $youtube ); ?>" target="_blank" rel="noopener" class="contact-page__profile-btn">YouTube</a>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Map Embed -->
            <?php if ( $map_embed ) : ?>
            <div class="contact-page__map">
                <?php echo $map_embed; ?>
            </div>
            <?php endif; ?>

        </div>

        <!-- GHL Form -->
        <div class="contact-page__form">
            <h2 class="contact-page__form-heading">Send a Message</h2>
            <?php if ( $ghl_form ) : ?>
                <?php echo $ghl_form; ?>
            <?php else : ?>
                <p>Please call us directly at <a href="tel:<?php echo esc_attr( $phone ); ?>"><?php echo esc_html( $phone ); ?></a></p>
            <?php endif; ?>
        </div>

    </div>
</section>

<?php get_footer(); ?>