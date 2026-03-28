<?php get_header(); ?>

<?php
$service_name    = get_the_title();
$hero_image      = get_field( 'hero_image' );
$service_intro   = get_field( 'service_intro' );
$service_type    = get_field( 'service_type' );
$related_cities  = get_field( 'related_city' );
$page_headline   = get_field( 'page_headline' );
$why_choose      = get_field( 'why_choose_blayne' );
$process_steps   = get_field( 'process_steps' );
$phone           = get_field( 'phone_number', 'option' );
$blayne_photo    = get_field( 'blayne_photo', 'option' );
$ghl_form        = get_field( 'ghl_form_embed', 'option' );
$license         = get_field( 'license_number', 'option' );
$hero_url        = $hero_image ? $hero_image['url'] : '';

$stats = array_filter( [
    get_field( 'years_of_experience', 'option' ) ? [ 'num' => get_field( 'years_of_experience', 'option' ), 'label' => 'Years Experience' ] : null,
    get_field( 'number_of_cities', 'option' )    ? [ 'num' => get_field( 'number_of_cities', 'option' ),    'label' => 'Cities Served'    ] : null,
    get_field( 'listings_per_month', 'option' )  ? [ 'num' => get_field( 'listings_per_month', 'option' ),  'label' => 'Listings Reviewed Monthly' ] : null,
] );?>

<!-- ── Hero ──────────────────────────────────────────────────────────────────── -->
<section class="service-hero"
    <?php if ( $hero_url ) : ?>
        style="background-image: url('<?php echo esc_url( $hero_url ); ?>');"
    <?php endif; ?>>
    <div class="service-hero__overlay"></div>
    <div class="service-hero__inner">
        <?php if ( $service_type ) : ?>
            <div class="service-hero__eyebrow"><?php echo esc_html( $service_type ); ?></div>
        <?php endif; ?>
        <h1 class="service-hero__title">
            <?php echo esc_html( $page_headline ?: $service_name ); ?>
        </h1>
        <?php if ( $service_intro ) : ?>
            <p class="service-hero__intro"><?php echo esc_html( $service_intro ); ?></p>
        <?php endif; ?>
        <div class="service-hero__cta">
            <a href="#service-contact" class="service-hero__btn">Get a Free Consultation</a>
            <?php if ( $phone ) : ?>
                <a href="tel:<?php echo esc_attr( $phone ); ?>"
                   class="service-hero__btn service-hero__btn--outline">
                    Call <?php echo esc_html( $phone ); ?>
                </a>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- ── Why Choose Blayne ─────────────────────────────────────────────────────── -->
<?php if ( $why_choose ) : ?>
<section class="service-why">
    <div class="service-why__inner">
        <div class="service-why__content">

            <!-- Text -->
            <div class="service-why__text-wrap">
                <h2 class="service-why__heading">
                    Why Choose Blayne for <?php echo esc_html( $service_name ); ?>?
                </h2>
                <div class="service-why__body wysiwyg-content">
                    <?php echo wp_kses_post( $why_choose ); ?>
                </div>
            </div>

            <!-- Bio Card -->
            <div class="service-why__bio-card">
                <?php if ( $blayne_photo ) : ?>
                    <img src="<?php echo esc_url( $blayne_photo['url'] ); ?>"
                         alt="Blayne Pacelli Realtor"
                         class="service-why__bio-photo">
                <?php endif; ?>
                <div class="service-why__bio-content">
                    <div class="service-why__bio-name">Blayne Pacelli</div>
                    <div class="service-why__bio-title">Realtor — Rodeo Realty</div>
                    <?php if ( $license ) : ?>
                       <div class="service-why__bio-license"><?php echo esc_html( $license ); ?></div>
                    <?php endif; ?>
                    <?php if ( $stats ) : ?>
                        <div class="service-why__bio-stats">
                            <?php foreach ( $stats as $stat ) : ?>
                                <div class="service-why__bio-stat">
                                    <span class="service-why__bio-stat-num"><?php echo esc_html( $stat['num'] ); ?></span>
                                    <span class="service-why__bio-stat-label"><?php echo esc_html( $stat['label'] ); ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                    <?php if ( $phone ) : ?>
                        <a href="tel:<?php echo esc_attr( $phone ); ?>" class="service-why__bio-btn">
                            Call Blayne Now
                        </a>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </div>
</section>
<?php endif; ?>

<!-- ── Process Steps ─────────────────────────────────────────────────────────── -->
<?php if ( $process_steps ) : ?>
<section class="service-process">
    <div class="service-process__inner">
        <h2 class="service-process__heading">The Process</h2>
        <p class="service-process__sub">Here's exactly what to expect when working with Blayne.</p>
        <div class="service-process__steps">
            <?php foreach ( $process_steps as $index => $step ) :
                $step_num      = $index + 1;
                $step_title    = $step['step_title']       ?? '';
                $step_desc     = $step['step_description'] ?? '';
                $step_icon     = $step['step_icon']        ?? '';
                $key_benefits  = $step['key_benefits']     ?? [];
                $cta_heading   = $step['cta_heading']      ?? '';
                $cta_subtext   = $step['cta_subtext']      ?? '';
            ?>
            <div class="service-process__step">

                <!-- Step number / icon -->
                <div class="service-process__step-num">
                    <?php if ( $step_icon ) : ?>
                        <span class="service-process__step-icon"><?php echo esc_html( $step_icon ); ?></span>
                    <?php else : ?>
                        <?php echo esc_html( $step_num ); ?>
                    <?php endif; ?>
                </div>

                <!-- Step content -->
                <div class="service-process__step-content">
                    <?php if ( $step_title ) : ?>
                        <h3 class="service-process__step-title"><?php echo esc_html( $step_title ); ?></h3>
                    <?php endif; ?>
                    <?php if ( $step_desc ) : ?>
                        <p class="service-process__step-desc"><?php echo esc_html( $step_desc ); ?></p>
                    <?php endif; ?>

                    <!-- Key Benefits -->
                    <?php if ( $key_benefits ) : ?>
                        <ul class="service-process__benefits">
                            <?php foreach ( $key_benefits as $benefit ) :
                                $benefit_title = $benefit['benefit_title']       ?? '';
                                $benefit_desc  = $benefit['benefit_description'] ?? '';
                            ?>
                                <?php if ( $benefit_title ) : ?>
                                    <li class="service-process__benefit">
                                        <span class="service-process__benefit-icon">✓</span>
                                        <div>
                                            <div class="service-process__benefit-title">
                                                <?php echo esc_html( $benefit_title ); ?>
                                            </div>
                                            <?php if ( $benefit_desc ) : ?>
                                                <div class="service-process__benefit-desc">
                                                    <?php echo esc_html( $benefit_desc ); ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </li>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>

                    <!-- Step CTA -->
                    <?php if ( $cta_heading || $cta_subtext ) : ?>
                        <div class="service-process__step-cta">
                            <?php if ( $cta_heading ) : ?>
                                <div class="service-process__step-cta-heading">
                                    <?php echo esc_html( $cta_heading ); ?>
                                </div>
                            <?php endif; ?>
                            <?php if ( $cta_subtext ) : ?>
                                <div class="service-process__step-cta-sub">
                                    <?php echo esc_html( $cta_subtext ); ?>
                                </div>
                            <?php endif; ?>
                            <a href="#service-contact" class="service-process__step-cta-btn">
                                Get Started →
                            </a>
                        </div>
                    <?php endif; ?>

                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ── Related Cities ────────────────────────────────────────────────────────── -->
<?php if ( $related_cities && is_array( $related_cities ) ) : ?>
<section class="service-cities">
    <div class="service-cities__inner">
        <h2 class="service-cities__heading">Areas We Serve</h2>
        <p class="service-cities__sub">
            Blayne provides <?php echo esc_html( $service_name ); ?> services throughout Greater Los Angeles.
        </p>
        <div class="service-cities__grid">
            <?php foreach ( $related_cities as $city_post ) :
                $city_id    = $city_post->ID;
                $city_title = get_the_title( $city_id );
                $city_link  = get_permalink( $city_id );
                $city_img   = get_field( 'hero_image', $city_id );
                $city_thumb = $city_img ? $city_img['url'] : '';
            ?>
            <a href="<?php echo esc_url( $city_link ); ?>" class="service-city-card">
                <div class="service-city-card__img-wrap <?php echo ! $city_thumb ? 'service-city-card__img-wrap--placeholder' : ''; ?>">
                    <?php if ( $city_thumb ) : ?>
                        <img src="<?php echo esc_url( $city_thumb ); ?>"
                             alt="<?php echo esc_attr( $city_title ); ?>"
                             class="service-city-card__img">
                    <?php endif; ?>
                </div>
                <div class="service-city-card__name"><?php echo esc_html( $city_title ); ?></div>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ── Related FAQs ───────────────────────────────────────────────────────────── -->
<?php
$service_slug = get_post_field( 'post_name', get_the_ID() );
blayne_faq_section( [
    'taxonomy'  => 'post_service',
    'term_slug' => $service_slug,
    'heading'   => $service_name . ' FAQs',
    'limit'     => 6,
] );
?>

<!-- ── Google Reviews ────────────────────────────────────────────────────────── -->
<section class="reviews-section">
    <div class="reviews-section__inner">
        <h2 class="reviews-section__heading">What Our Clients Say</h2>
        <h3 class="reviews-section__sub">Real reviews from real clients on Google</h3>
        <?php echo do_shortcode('[trustindex no-registration=google]'); ?>
    </div>
</section>

<!-- ── Lead Form ─────────────────────────────────────────────────────────────── -->
<section id="service-contact">
    <?php get_template_part( 'template-parts/lead-form' ); ?>
</section>

<?php get_footer(); ?>
