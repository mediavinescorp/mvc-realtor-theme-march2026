<?php get_header(); ?>

<?php
$city_name    = get_the_title();
$hero_image   = get_field( 'hero_image' );
$city_intro   = get_field( 'city_intro' );
$where_is     = get_field( 'where_is_city' );
$reasons      = get_field( 'top_5_reasons' );
$reason_intro    = get_field( 'reason_intro' );
$reason_summary  = get_field( 'reason_summary' );
$neighborhoods = get_field( 'sub_neighborhoods' );
$public_schools  = get_field( 'public_schools' );
$private_schools = get_field( 'private_schools' );
$avg_income   = get_field( 'avg_household_income' );
$avg_home     = get_field( 'avg_home_cost' );
$income_ratio = get_field( 'income_ratio_note' );
$edu_stats    = get_field( 'education_stats' );
$restaurants  = get_field( 'restaurants' );
$form_cta     = get_field( 'form_cta_heading' );
$phone        = get_field( 'phone_number', 'option' );
$hero_url     = $hero_image ? $hero_image['url'] : '';
$reason_img_raw  = get_field( 'reason_image' );
$reason_img_url  = ( is_array( $reason_img_raw ) && ! empty( $reason_img_raw['url'] ) )
                   ? $reason_img_raw['url']
                   : $hero_url;
?>

<!-- Hero -->
<section class="city-hero" <?php if ( $hero_url ) : ?>style="background-image: url('<?php echo esc_url( $hero_url ); ?>');"<?php endif; ?>>
    <div class="city-hero__overlay"></div>
    <div class="city-hero__inner">
        <h1 class="city-hero__title"><?php echo esc_html( $city_name ); ?> Realtor</h1>
        <p class="city-hero__tagline">Is <?php echo esc_html( $city_name ); ?> the Perfect Place for You to Buy a Home?</p>
        <?php if ( $city_intro ) : ?>
            <p class="city-hero__intro"><?php echo esc_html( $city_intro ); ?></p>
        <?php endif; ?>
        <?php if ( $phone ) : ?>
            <div class="city-hero__cta">
                <a href="<?php echo esc_url( home_url( '/contact' ) ); ?>" class="city-hero__btn">Get a Free Consultation</a>
                <a href="tel:<?php echo esc_attr( $phone ); ?>" class="city-hero__btn city-hero__btn--outline">Call <?php echo esc_html( $phone ); ?></a>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Quick Links Anchor Nav -->
<nav class="city-quicklinks" aria-label="Page sections">
    <div class="city-quicklinks__inner">
        <ul class="city-quicklinks__list">
            <?php if ( $where_is ) : ?>
                <li><a href="#where-is-<?php echo sanitize_title( $city_name ); ?>" class="city-quicklinks__link">Where is <?php echo esc_html( $city_name ); ?></a></li>
            <?php endif; ?>
            <?php if ( $reasons ) : ?>
                <li><a href="#top-reasons" class="city-quicklinks__link">Top 5 Reasons to Move</a></li>
            <?php endif; ?>
            <?php if ( $neighborhoods ) : ?>
                <li><a href="#neighborhoods" class="city-quicklinks__link">Neighborhoods</a></li>
            <?php endif; ?>
            <?php if ( $public_schools || $private_schools ) : ?>
                <li><a href="#schools" class="city-quicklinks__link">Schools</a></li>
            <?php endif; ?>
            <?php if ( $restaurants ) : ?>
                <li><a href="#restaurants" class="city-quicklinks__link">Restaurants</a></li>
            <?php endif; ?>
            <li><a href="#contact-<?php echo sanitize_title( $city_name ); ?>" class="city-quicklinks__link">Free Consult</a></li>
        </ul>
    </div>
</nav>

<!-- Where is [City] -->
<?php if ( $where_is ) : ?>
<section class="city-section" id="where-is-<?php echo sanitize_title( $city_name ); ?>">
    <div class="city-section__inner">
        <h2 class="city-section__heading">Where is <?php echo esc_html( $city_name ); ?> CA?</h2>
        <div class="city-section__content">
            <?php echo wpautop( esc_html( $where_is ) ); ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Top 5 Reasons -->
<?php if ( $reasons ) : ?>
<section class="city-reasons" id="top-reasons">
    <div class="city-reasons__inner">
        <h2 class="city-reasons__heading">Top 5 Reasons to Move to <?php echo esc_html( $city_name ); ?></h2>

        <?php if ( $reason_intro ) : ?>
            <p class="city-reasons__intro"><?php echo esc_html( $reason_intro ); ?></p>
        <?php endif; ?>

        <div class="city-reasons__layout">

            <!-- Image -->
           <!-- Image -->
            <div class="city-reasons__image-wrap">
                <?php if ( $reason_img_url ) : ?>
                    <img src="<?php echo esc_url( $reason_img_url ); ?>"
                         alt="<?php echo esc_attr( $city_name ); ?> Real Estate"
                         class="city-reasons__image"
                         id="city-reasons-image">
                <?php endif; ?>
            </div>

            <!-- Accordion -->
            <div class="city-reasons__accordion">
                <?php foreach ( $reasons as $index => $reason ) :
                    $reason_id = 'reason-' . $index;
                    $is_first  = $index === 0;
                ?>
                    <div class="city-reasons__item <?php echo $is_first ? 'city-reasons__item--open' : ''; ?>"
                         data-index="<?php echo $index; ?>">
                        <button class="city-reasons__trigger"
                                aria-expanded="<?php echo $is_first ? 'true' : 'false'; ?>"
                                aria-controls="<?php echo $reason_id; ?>">
                            <span class="city-reasons__trigger-text">
                                <?php echo esc_html( $reason['reason_title'] ); ?>
                            </span>
                            <span class="city-reasons__trigger-icon" aria-hidden="true">
                                <?php echo $is_first ? '−' : '+'; ?>
                            </span>
                        </button>
                        <div class="city-reasons__content"
                             id="<?php echo $reason_id; ?>"
                             <?php echo $is_first ? '' : 'hidden'; ?>>
                            <p class="city-reasons__desc">
                                <?php echo esc_html( $reason['reason_description'] ); ?>
                            </p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

        </div>

        <?php if ( $reason_summary ) : ?>
            <div class="city-reasons__summary">
                <?php echo wpautop( esc_html( $reason_summary ) ); ?>
            </div>
        <?php endif; ?>

    </div>
</section>
<?php endif; ?>


<!-- Sub Neighborhoods -->
<?php
$city_slug_for_hoods = get_post_field( 'post_name', get_the_ID() );
$hood_query = blayne_get_neighborhoods( $city_slug_for_hoods );
?>
 
<?php if ( $hood_query ) : ?>
<section class="city-neighborhoods" id="neighborhoods">
    <div class="city-neighborhoods__inner">
        <h2 class="city-neighborhoods__heading">
            Learn About Neighborhoods Within <?php echo esc_html( $city_name ); ?> CA
        </h2>
        <div class="city-neighborhoods__grid">
            <?php while ( $hood_query->have_posts() ) : $hood_query->the_post();
                $nh_image     = get_field( 'neighborhood_image' );
                $nh_desc      = get_field( 'neighborhood_description' );
                $nh_highlight = get_field( 'neighborhood_highlight' );
                $nh_link      = get_field( 'neighborhood_link' );
                $nh_img_url   = $nh_image ? $nh_image['url'] : '';
            ?>
            <div class="city-neighborhoods__card">
                <?php if ( $nh_img_url ) : ?>
                    <div class="city-neighborhoods__card-img-wrap">
                        <img src="<?php echo esc_url( $nh_img_url ); ?>"
                             alt="<?php echo esc_attr( get_the_title() ); ?> <?php echo esc_attr( $city_name ); ?>"
                             class="city-neighborhoods__card-img">
                    </div>
                <?php endif; ?>
                <h3 class="city-neighborhoods__card-title">
                    <?php if ( $nh_link ) : ?>
                        <a href="<?php echo esc_url( $nh_link ); ?>" class="city-neighborhoods__card-link">
                            <?php the_title(); ?>
                        </a>
                    <?php else : ?>
                        <?php the_title(); ?>
                    <?php endif; ?>
                </h3>
                <?php if ( $nh_highlight ) : ?>
                    <p class="city-neighborhoods__card-highlight"><?php echo esc_html( $nh_highlight ); ?></p>
                <?php endif; ?>
                <?php if ( $nh_desc ) : ?>
                    <p class="city-neighborhoods__card-desc"><?php echo esc_html( $nh_desc ); ?></p>
                <?php endif; ?>
            </div>
            <?php endwhile; wp_reset_postdata(); ?>
        </div>
    </div>
</section>
<?php endif; ?>


<!-- Mid Page CTA -->
<section class="city-cta">
    <div class="city-cta__inner">
        <h2 class="city-cta__heading">Want to Buy or Sell a Property in <?php echo esc_html( $city_name ); ?>?</h2>
        <?php if ( $phone ) : ?>
            <a href="tel:<?php echo esc_attr( $phone ); ?>" class="city-cta__btn">
                Call Blayne Pacelli
            </a>
        <?php endif; ?>
    </div>
</section>


<!-- Income + Home Cost Stats -->
<?php if ( $avg_income || $avg_home ) : ?>
<section class="city-stats">
    <div class="city-stats__inner">
        <h2 class="city-stats__heading"><?php echo esc_html( $city_name ); ?> Average Household Income vs Average Home Cost</h2>
        <div class="city-stats__grid">
            <?php if ( $avg_income ) : ?>
            <div class="city-stats__item">
                <div class="city-stats__number">
                    <span class="stats-counter"
                          data-target="<?php echo esc_attr( preg_replace('/[^0-9]/', '', $avg_income ) ); ?>"
                          data-prefix="$"
                          data-format="true">0</span>
                </div>
                <div class="city-stats__label">Average Household Income</div>
            </div>
            <?php endif; ?>
           
	<?php if ( $avg_home ) : ?>
    <div class="city-stats__item">
        <div class="city-stats__number">
            <span class="city-stats__static"><?php echo esc_html( $avg_home ); ?></span>
        </div>
        <div class="city-stats__label">Average Home Cost</div>
    </div>
<?php endif; ?>
        </div>
        <?php if ( $income_ratio ) : ?>
            <p class="city-stats__note"><?php echo esc_html( $income_ratio ); ?></p>
        <?php endif; ?>
    </div>
</section>
<?php endif; ?>


<!-- Education Stats -->
<?php if ( $edu_stats ) : ?>
<section class="city-section city-section--gray">
    <div class="city-section__inner">
        <h2 class="city-section__heading">Educational Levels of Homeowners in <?php echo esc_html( $city_name ); ?></h2>
        <div class="city-section__content">
            <?php echo wpautop( esc_html( $edu_stats ) ); ?>
        </div>
    </div>
</section>
<?php endif; ?>


<!-- Schools -->
<?php if ( $public_schools || $private_schools ) : ?>
<section class="city-schools" id="schools">
    <div class="city-schools__inner">
        <h2 class="city-schools__heading">Schools to Consider in <?php echo esc_html( $city_name ); ?></h2>
        <div class="city-schools__grid">
            <?php if ( $public_schools ) : ?>
            <div class="city-schools__col">
                <h3 class="city-schools__subheading">Public Schools</h3>
                <ol class="city-schools__list">
                    <?php foreach ( $public_schools as $school ) : ?>
                        <li class="city-schools__item">
                            <?php if ( $school['school_url'] ) : ?>
                                <a href="<?php echo esc_url( $school['school_url'] ); ?>" target="_blank" rel="noopener" class="city-schools__link">
                                    <?php echo esc_html( $school['school_name'] ); ?>
                                </a>
                            <?php else : ?>
                                <?php echo esc_html( $school['school_name'] ); ?>
                            <?php endif; ?>
                        </li>
                    <?php endforeach; ?>
                </ol>
            </div>
            <?php endif; ?>

            <?php if ( $private_schools ) : ?>
            <div class="city-schools__col">
                <h3 class="city-schools__subheading">Private Schools</h3>
                <ol class="city-schools__list">
                    <?php foreach ( $private_schools as $school ) : ?>
                        <li class="city-schools__item">
                            <?php if ( $school['private_school_url'] ) : ?>
                                <a href="<?php echo esc_url( $school['private_school_url'] ); ?>" target="_blank" rel="noopener" class="city-schools__link">
                                    <?php echo esc_html( $school['private_school_name'] ); ?>
                                </a>
                            <?php else : ?>
                                <?php echo esc_html( $school['private_school_name'] ); ?>
                            <?php endif; ?>
                        </li>
                    <?php endforeach; ?>
                </ol>
            </div>
            <?php endif; ?>
        </div>
    </div>
</section>
<?php endif; ?>



<!-- Restaurants -->
<?php if ( $restaurants ) : ?>
<section class="city-restaurants" id="restaurants">
    <div class="city-restaurants__inner">
        <h2 class="city-restaurants__heading">Top Restaurants in <?php echo esc_html( $city_name ); ?> CA</h2>
        <p class="city-restaurants__subtext">Based on Yelp and TripAdvisor ratings.</p>
        <ul class="city-restaurants__list">
            <?php foreach ( $restaurants as $restaurant ) : ?>
                <li class="city-restaurants__item">
                    <?php if ( $restaurant['restaurant_url'] ) : ?>
                        <a href="<?php echo esc_url( $restaurant['restaurant_url'] ); ?>" target="_blank" rel="noopener" class="city-restaurants__link">
                            <?php echo esc_html( $restaurant['restaurant_name'] ); ?>
                        </a>
                    <?php else : ?>
                        <?php echo esc_html( $restaurant['restaurant_name'] ); ?>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</section>
<?php endif; ?>

<?php blayne_faq_section( [
    'taxonomy'  => 'post_city',
    'term_slug' => get_post_field( 'post_name', get_the_ID() ),
    'heading'   => $city_name . ' Real Estate FAQs',
] ); ?>



<!-- Google Reviews -->
<section class="reviews-section">
    <div class="reviews-section__inner">
        <h2 class="reviews-section__heading">What Our Clients Say</h2>
        <h3 class="reviews-section__sub">Real reviews from real clients on Google</h3>
        <?php echo do_shortcode('[trustindex no-registration=google]'); ?>
    </div>
</section>


<!-- Lead Form -->
<section id="contact-<?php echo sanitize_title( $city_name ); ?>">
    <?php get_template_part( 'template-parts/lead-form' ); ?>
</section>

<?php get_footer(); ?>