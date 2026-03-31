<?php
/**
 * Template: single-landing_page.php
 * CPT:       landing_page
 * URL:       /keyword/location/
 * Blueprint: 10-section SEO + AEO + Conversion architecture
 */

get_header();

// ── Pull all ACF fields ───────────────────────────────────────────────────────

// Identity
$city_name      = get_field( 'lp_city_name' );
$service_name   = get_field( 'lp_service_name' );
$service_slug   = get_field( 'lp_service_slug' );

// 01 Hero
$hero_image     = get_field( 'lp_hero_image' );
$hero_video_url = get_field( 'lp_hero_video_url' );
$page_h1        = get_field( 'lp_page_h1' );
$subheadline    = get_field( 'lp_page_subheadline' );
$cta_label      = get_field( 'lp_hero_cta_label' ) ?: 'Get a Free Consultation';
$cta_url        = get_field( 'lp_hero_cta_url' ) ?: '#lp-contact';
$hero_url       = $hero_image ? $hero_image['url'] : '';
$hero_alt       = $page_h1 ?: $city_name . ' ' . $service_name . ' — Blayne Pacelli Realtor';

// 02 AEO
$aeo_intro      = get_field( 'lp_aeo_intro' );
$aeo_questions  = get_field( 'lp_aeo_questions' );

// 03 Market
$market_h2      = get_field( 'lp_market_section_h2' ) ?: $city_name . ' Real Estate Market — ' . date( 'Y' );
$median_price   = get_field( 'lp_median_price' );
$price_trend    = get_field( 'lp_price_trend' );
$days_on_market = get_field( 'lp_days_on_market' );
$homes_sold_30  = get_field( 'lp_homes_sold_30' );
$price_per_sqft = get_field( 'lp_price_per_sqft' );
$market_type    = get_field( 'lp_market_type' );
$market_summary = get_field( 'lp_market_summary' );

$trend_icon = '→';
if ( $price_trend === 'up' )   $trend_icon = '↑';
if ( $price_trend === 'down' ) $trend_icon = '↓';

$market_label = 'Market';
if ( $market_type === 'seller' )   $market_label = "Seller's Market";
if ( $market_type === 'buyer' )    $market_label = "Buyer's Market";
if ( $market_type === 'balanced' ) $market_label = 'Balanced Market';

// 04 Area
$city_desc      = get_field( 'lp_city_description' );
$who_lives      = get_field( 'lp_who_lives_here' );
$proximity      = get_field( 'lp_proximity_note' );


// 05 Services
$service_cards  = get_field( 'lp_service_cards' );

// 06 Reviews
$reviews_heading     = get_field( 'lp_reviews_heading' ) ?: 'What Our Clients Say';
$testimonials        = get_field( 'lp_testimonials' );
$show_google_reviews = get_field( 'lp_show_google_reviews' );

// 07 Bio
$city_bio_line  = get_field( 'lp_city_bio_line' );

// 08 Listings
$listings_h2    = get_field( 'lp_listings_h2' ) ?: 'Homes for Sale in ' . $city_name;
$listings       = get_field( 'lp_featured_listings' );
$view_all_url   = get_field( 'lp_view_all_listings_url' );

// 09 Lead
$lead_h2        = get_field( 'lp_lead_section_h2' ) ?: 'Ready to Buy or Sell in ' . $city_name . '?';
$lead_copy      = get_field( 'lp_lead_section_copy' );

// 10 Nearby
$nearby_heading = get_field( 'lp_nearby_heading' ) ?: 'Also Serving These ' . $city_name . ' Area Communities';
$nearby_cities  = get_field( 'lp_nearby_cities' );

// Site Settings
$phone          = get_field( 'phone_number', 'option' );
$blayne_photo   = get_field( 'blayne_photo', 'option' );
$blayne_photo2  = get_field( 'blayne_photo_2', 'option' );
$license        = get_field( 'license_number', 'option' );
$years_exp      = get_field( 'years_of_experience', 'option' );
$cities_count   = get_field( 'number_of_cities', 'option' );
$listing_rate   = get_field( 'listing_price_rate', 'option' );
$closing_rate   = get_field( 'closing_success_rate', 'option' );
$ghl_form       = get_field( 'ghl_form_embed', 'option' );
$zillow_url     = get_field( 'social_zillow', 'option' );
$realtor_url    = get_field( 'social_realtor', 'option' );
$google_biz_url = get_field( 'google_business_url', 'option' );
?>

<?php // ── 01 HERO ──────────────────────────────────────────────────────────── ?>
<section class="lp-hero"
    <?php if ( $hero_url && ! $hero_video_url ) : ?>
        style="background-image: url('<?php echo esc_url( $hero_url ); ?>');"
    <?php endif; ?>>

    <?php if ( $hero_video_url ) : ?>
        <video class="lp-hero__video" autoplay muted loop playsinline
               poster="<?php echo esc_url( $hero_url ); ?>">
            <source src="<?php echo esc_url( $hero_video_url ); ?>" type="video/mp4">
        </video>
    <?php endif; ?>

    <div class="lp-hero__overlay"></div>

    <div class="lp-hero__inner">

        <h1 class="lp-hero__h1">
            <?php echo esc_html( $page_h1 ?: $service_name . ' in ' . $city_name . ' — Blayne Pacelli, Realtor' ); ?>
        </h1>

        <?php if ( $subheadline ) : ?>
            <p class="lp-hero__sub"><?php echo esc_html( $subheadline ); ?></p>
        <?php endif; ?>

        <?php // Trust strip — 3 micro-stats from Site Settings ?>
        <div class="lp-hero__trust-strip">
            <?php if ( $years_exp ) : ?>
                <div class="lp-hero__trust-item">
                    <span class="lp-hero__trust-num"><?php echo esc_html( $years_exp ); ?></span>
                    <span class="lp-hero__trust-label">Years Experience</span>
                </div>
            <?php endif; ?>
            <?php if ( $cities_count ) : ?>
                <div class="lp-hero__trust-item">
                    <span class="lp-hero__trust-num"><?php echo esc_html( $cities_count ); ?>+</span>
                    <span class="lp-hero__trust-label">Cities Served</span>
                </div>
            <?php endif; ?>
            <?php if ( $closing_rate ) : ?>
                <div class="lp-hero__trust-item">
                    <span class="lp-hero__trust-num"><?php echo esc_html( $closing_rate ); ?></span>
                    <span class="lp-hero__trust-label">Closing Success Rate</span>
                </div>
            <?php endif; ?>
        </div>

        <div class="lp-hero__cta-wrap">
            <a href="<?php echo esc_url( $cta_url ); ?>" class="lp-hero__btn">
                <?php echo esc_html( $cta_label ); ?>
            </a>
            <?php if ( $phone ) : ?>
                <a href="tel:<?php echo esc_attr( $phone ); ?>" class="lp-hero__btn lp-hero__btn--outline">
                    Call <?php echo esc_html( $phone ); ?>
                </a>
            <?php endif; ?>
        </div>

    </div>
</section>


<?php // ── 02 AEO QUICK-ANSWER BLOCK ─────────────────────────────────────────── ?>
<?php if ( $aeo_questions ) : ?>
<section class="lp-aeo">
    <div class="lp-aeo__inner">

        <div class="lp-aeo__header">
            <h2 class="lp-aeo__heading">
                <?php echo esc_html( $aeo_intro ?: 'Quick Answers About ' . $service_name . ' in ' . $city_name ); ?>
            </h2>
        </div>

        <div class="lp-aeo__grid">
            <?php foreach ( $aeo_questions as $i => $qa ) :
                $panel_id = 'aeo-panel-' . $i;
                $is_first = $i === 0;
            ?>
            <div class="lp-aeo__item <?php echo $is_first ? 'lp-aeo__item--open' : ''; ?>">
                <button class="lp-aeo__trigger"
                        aria-expanded="<?php echo $is_first ? 'true' : 'false'; ?>"
                        aria-controls="<?php echo esc_attr( $panel_id ); ?>">
                    <h2 class="lp-aeo__question"><?php echo esc_html( $qa['lp_aeo_question'] ); ?></h2>
                    <span class="lp-aeo__icon" aria-hidden="true"></span>
                </button>
                <div class="lp-aeo__panel" id="<?php echo esc_attr( $panel_id ); ?>"
                     <?php echo $is_first ? '' : 'hidden'; ?>>
                    <?php if ( $qa['lp_aeo_short_answer'] ) : ?>
                        <p class="lp-aeo__short"><?php echo esc_html( $qa['lp_aeo_short_answer'] ); ?></p>
                    <?php endif; ?>
                    <?php if ( $qa['lp_aeo_long_answer'] ) : ?>
                        <div class="lp-aeo__long wysiwyg-content">
                            <?php echo wp_kses_post( $qa['lp_aeo_long_answer'] ); ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

    </div>
</section>

<script>
(function(){
    document.querySelectorAll('.lp-aeo__trigger').forEach(function(btn){
        btn.addEventListener('click', function(){
            var panel  = document.getElementById(btn.getAttribute('aria-controls'));
            var item   = btn.closest('.lp-aeo__item');
            var isOpen = btn.getAttribute('aria-expanded') === 'true';
            document.querySelectorAll('.lp-aeo__trigger').forEach(function(b){
                b.setAttribute('aria-expanded','false');
                b.closest('.lp-aeo__item').classList.remove('lp-aeo__item--open');
                var p = document.getElementById(b.getAttribute('aria-controls'));
                if(p) p.hidden = true;
            });
            if(!isOpen){
                btn.setAttribute('aria-expanded','true');
                item.classList.add('lp-aeo__item--open');
                panel.hidden = false;
            }
        });
    });
})();
</script>
<?php endif; ?>


<?php // ── 03 LOCAL MARKET SNAPSHOT ──────────────────────────────────────────── ?>
<?php if ( $median_price || $days_on_market || $homes_sold_30 || $price_per_sqft ) : ?>
<section class="lp-market">
    <div class="lp-market__inner">

        <h2 class="lp-market__heading"><?php echo esc_html( $market_h2 ); ?></h2>

        <?php if ( $market_type ) : ?>
            <div class="lp-market__type-badge lp-market__type-badge--<?php echo esc_attr( $market_type ); ?>">
                <?php echo esc_html( $market_label ); ?>
            </div>
        <?php endif; ?>

        <div class="lp-market__stats-grid">

            <?php if ( $median_price ) : ?>
            <div class="lp-market__stat">
                <div class="lp-market__stat-num">
                    <?php echo esc_html( $median_price ); ?>
                    <span class="lp-market__trend lp-market__trend--<?php echo esc_attr( $price_trend ); ?>">
                        <?php echo $trend_icon; ?>
                    </span>
                </div>
                <div class="lp-market__stat-label">Median Home Price</div>
            </div>
            <?php endif; ?>

            <?php if ( $days_on_market ) : ?>
            <div class="lp-market__stat">
                <div class="lp-market__stat-num"><?php echo esc_html( $days_on_market ); ?></div>
                <div class="lp-market__stat-label">Avg Days on Market</div>
            </div>
            <?php endif; ?>

            <?php if ( $homes_sold_30 ) : ?>
            <div class="lp-market__stat">
                <div class="lp-market__stat-num"><?php echo esc_html( $homes_sold_30 ); ?></div>
                <div class="lp-market__stat-label">Homes Sold Last 30 Days</div>
            </div>
            <?php endif; ?>

            <?php if ( $price_per_sqft ) : ?>
            <div class="lp-market__stat">
                <div class="lp-market__stat-num"><?php echo esc_html( $price_per_sqft ); ?></div>
                <div class="lp-market__stat-label">Price Per Sq Ft</div>
            </div>
            <?php endif; ?>

        </div>

        <?php if ( $market_summary ) : ?>
            <p class="lp-market__summary"><?php echo esc_html( $market_summary ); ?></p>
        <?php endif; ?>

    </div>
</section>
<?php endif; ?>


<?php // ── 04 ABOUT THE AREA ─────────────────────────────────────────────────── ?>
<?php
$lp_city_terms       = get_the_terms( get_the_ID(), 'post_city' );
$city_slug_for_hoods = ( $lp_city_terms && ! is_wp_error( $lp_city_terms ) )
    ? $lp_city_terms[0]->slug
    : get_post_field( 'post_name', get_the_ID() );
$hood_query = blayne_get_neighborhoods( $city_slug_for_hoods );
?>

<?php if ( $city_desc || $who_lives || $proximity || $hood_query ) : ?>
<section class="lp-area">
    <div class="lp-area__inner">

        <h2 class="lp-area__heading">About <?php echo esc_html( $city_name ); ?>, California</h2>

        <?php if ( $city_desc ) : ?>
            <div class="lp-area__desc wysiwyg-content">
                <?php echo wp_kses_post( $city_desc ); ?>
            </div>
        <?php endif; ?>

        <div class="lp-area__meta">
            <?php if ( $who_lives ) : ?>
                <div class="lp-area__who">
                    <h3 class="lp-area__meta-heading">Who Lives Here</h3>
                    <p><?php echo esc_html( $who_lives ); ?></p>
                </div>
            <?php endif; ?>
            <?php if ( $proximity ) : ?>
                <div class="lp-area__proximity">
                    <h3 class="lp-area__meta-heading">Location</h3>
                    <p><?php echo esc_html( $proximity ); ?></p>
                </div>
            <?php endif; ?>
        </div>

        <?php if ( $hood_query ) : ?>
        <div class="lp-area__neighborhoods">
            <h3 class="lp-area__neighborhoods-heading">
                Top Neighborhoods in <?php echo esc_html( $city_name ); ?>
            </h3>
            <div class="lp-area__neighborhoods-grid">
                <?php while ( $hood_query->have_posts() ) : $hood_query->the_post();
                    $nh_image     = get_field( 'neighborhood_image' );
                    $nh_desc      = get_field( 'neighborhood_description' );
                    $nh_highlight = get_field( 'neighborhood_highlight' );
                    $nh_link      = get_field( 'neighborhood_link' );
                    $nh_img_url   = $nh_image ? $nh_image['url'] : '';
                ?>
                <div class="lp-area__hood-card">
                    <?php if ( $nh_img_url ) : ?>
                        <div class="lp-area__hood-img-wrap">
                            <img src="<?php echo esc_url( $nh_img_url ); ?>"
                                 alt="<?php echo esc_attr( get_the_title() ); ?> neighborhood"
                                 class="lp-area__hood-img">
                        </div>
                    <?php endif; ?>
                    <div class="lp-area__hood-body">
                        <div class="lp-area__hood-name">
                            <?php if ( $nh_link ) : ?>
                                <a href="<?php echo esc_url( $nh_link ); ?>" class="lp-area__hood-link">
                                    <?php the_title(); ?>
                                </a>
                            <?php else : ?>
                                <?php the_title(); ?>
                            <?php endif; ?>
                        </div>
                        <?php if ( $nh_highlight ) : ?>
                            <div class="lp-area__hood-highlight"><?php echo esc_html( $nh_highlight ); ?></div>
                        <?php endif; ?>
                        <?php if ( $nh_desc ) : ?>
                            <p class="lp-area__hood-desc"><?php echo esc_html( $nh_desc ); ?></p>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endwhile; wp_reset_postdata(); ?>
            </div>
        </div>
        <?php endif; ?>

    </div>
</section>
<?php endif; ?>

<?php // ── 05 SERVICES + VALUE PROP ──────────────────────────────────────────── ?>
<?php if ( $service_cards ) : ?>
<section class="lp-services">
    <div class="lp-services__inner">

        <h2 class="lp-services__heading">How Blayne Helps You in <?php echo esc_html( $city_name ); ?></h2>

        <div class="lp-services__grid">
            <?php foreach ( $service_cards as $card ) : ?>
            <div class="lp-services__card">
               <?php if ( $card['lp_card_icon'] ) : ?>
    <div class="lp-services__card-icon">
        <span class="dashicons <?php echo esc_attr( $card['lp_card_icon'] ); ?>"></span>
    </div>
<?php endif; ?>
                <h3 class="lp-services__card-title"><?php echo esc_html( $card['lp_card_title'] ); ?></h3>
                <?php if ( $card['lp_card_desc'] ) : ?>
                    <p class="lp-services__card-desc"><?php echo esc_html( $card['lp_card_desc'] ); ?></p>
                <?php endif; ?>
                <?php if ( $card['lp_card_differentiator'] ) : ?>
                    <p class="lp-services__card-diff">✓ <?php echo esc_html( $card['lp_card_differentiator'] ); ?></p>
                <?php endif; ?>
                <?php if ( $card['lp_card_link'] ) : ?>
                    <a href="<?php echo esc_url( $card['lp_card_link'] ); ?>" class="lp-services__card-link">
                        Learn More →
                    </a>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>

    </div>
</section>
<?php endif; ?>


<?php // ── 06 SOCIAL PROOF — REVIEWS ─────────────────────────────────────────── ?>
<section class="lp-reviews">
    <div class="lp-reviews__inner">

        <h2 class="lp-reviews__heading"><?php echo esc_html( $reviews_heading ); ?></h2>
       
        <?php if ( $testimonials ) : ?>
        <div class="lp-reviews__cards">
            <?php foreach ( $testimonials as $t ) :
                $stars = intval( $t['lp_testi_stars'] ?? 5 );
                $star_html = str_repeat( '★', $stars ) . str_repeat( '☆', 5 - $stars );
            ?>
            <div class="lp-reviews__card">
                <div class="lp-reviews__card-stars"><?php echo $star_html; ?></div>
                <?php if ( $t['lp_testi_result'] ) : ?>
                    <div class="lp-reviews__card-result"><?php echo esc_html( $t['lp_testi_result'] ); ?></div>
                <?php endif; ?>
                <?php if ( $t['lp_testi_quote'] ) : ?>
                    <blockquote class="lp-reviews__card-quote">
                        "<?php echo esc_html( $t['lp_testi_quote'] ); ?>"
                    </blockquote>
                <?php endif; ?>
                <div class="lp-reviews__card-meta">
                    <span class="lp-reviews__card-name"><?php echo esc_html( $t['lp_testi_name'] ); ?></span>
                    <?php if ( $t['lp_testi_city'] ) : ?>
                        <span class="lp-reviews__card-city"><?php echo esc_html( $t['lp_testi_city'] ); ?></span>
                    <?php endif; ?>
                    <?php if ( $t['lp_testi_type'] ) : ?>
                        <span class="lp-reviews__card-type"><?php echo esc_html( ucfirst( $t['lp_testi_type'] ) ); ?></span>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <?php if ( $show_google_reviews ) : ?>
        <div class="lp-reviews__google">
            <?php echo do_shortcode( '[trustindex no-registration=google]' ); ?>
        </div>
        <?php endif; ?>

        <?php // Profile links — trust anchors ?>
        <div class="lp-reviews__profiles">
            <?php if ( $google_biz_url ) : ?>
                <a href="<?php echo esc_url( $google_biz_url ); ?>" target="_blank" rel="noopener" class="lp-reviews__profile-link">Google ★</a>
            <?php endif; ?>
            <?php if ( $zillow_url ) : ?>
                <a href="<?php echo esc_url( $zillow_url ); ?>" target="_blank" rel="noopener" class="lp-reviews__profile-link">Zillow</a>
            <?php endif; ?>
            <?php if ( $realtor_url ) : ?>
                <a href="<?php echo esc_url( $realtor_url ); ?>" target="_blank" rel="noopener" class="lp-reviews__profile-link">Realtor.com</a>
            <?php endif; ?>
        </div>

    </div>
</section>


<?php // ── 07 ABOUT BLAYNE — E-E-A-T ─────────────────────────────────────────── ?>
<section class="lp-bio">
    <div class="lp-bio__inner">

        <?php if ( $blayne_photo ) : ?>
        <div class="lp-bio__photo-wrap">
            <img src="<?php echo esc_url( $blayne_photo['url'] ); ?>"
                 alt="Blayne Pacelli <?php echo esc_attr( $city_name ); ?> Realtor"
                 class="lp-bio__photo">
        </div>
        <?php endif; ?>

        <div class="lp-bio__content">
            <h2 class="lp-bio__heading">Your <?php echo esc_html( $city_name ); ?> Real Estate Expert</h2>
            <div class="lp-bio__name">Blayne Pacelli</div>
            <div class="lp-bio__title">Realtor — Rodeo Realty</div>
            <?php if ( $license ) : ?>
                <div class="lp-bio__license">License #<?php echo esc_html( $license ); ?></div>
            <?php endif; ?>

            <?php if ( $city_bio_line ) : ?>
                <p class="lp-bio__city-line"><?php echo esc_html( $city_bio_line ); ?></p>
            <?php else : ?>
                <p class="lp-bio__city-line">Blayne Pacelli is a top-rated Los Angeles realtor with Rodeo Realty, helping buyers and sellers across <?php echo esc_html( $city_name ); ?> and Greater Los Angeles County.</p>
            <?php endif; ?>

            <div class="lp-bio__stats">
                <?php if ( $years_exp ) : ?>
                <div class="lp-bio__stat">
                    <span class="lp-bio__stat-num"><?php echo esc_html( $years_exp ); ?>+</span>
                    <span class="lp-bio__stat-label">Years Experience</span>
                </div>
                <?php endif; ?>
                <?php if ( $listing_rate ) : ?>
                <div class="lp-bio__stat">
                    <span class="lp-bio__stat-num"><?php echo esc_html( $listing_rate ); ?></span>
                    <span class="lp-bio__stat-label">Listing Price Rate</span>
                </div>
                <?php endif; ?>
                <?php if ( $closing_rate ) : ?>
                <div class="lp-bio__stat">
                    <span class="lp-bio__stat-num"><?php echo esc_html( $closing_rate ); ?></span>
                    <span class="lp-bio__stat-label">Closing Success Rate</span>
                </div>
                <?php endif; ?>
            </div>

            <?php if ( $phone ) : ?>
                <a href="tel:<?php echo esc_attr( $phone ); ?>" class="lp-bio__btn">
                    Call Blayne Now
                </a>
            <?php endif; ?>
        </div>

    </div>
</section>


<?php // ── 08 FEATURED LISTINGS ──────────────────────────────────────────────── ?>
<?php if ( $listings ) : ?>
<section class="lp-listings">
    <div class="lp-listings__inner">

        <h2 class="lp-listings__heading"><?php echo esc_html( $listings_h2 ); ?></h2>

        <div class="lp-listings__grid">
            <?php foreach ( $listings as $listing ) :
                $photo = $listing['lp_listing_photo'];
            ?>
            <div class="lp-listings__card">
                <?php if ( $photo ) : ?>
                    <div class="lp-listings__card-img-wrap">
                        <img src="<?php echo esc_url( $photo['url'] ); ?>"
                             alt="<?php echo esc_attr( $listing['lp_listing_address'] ); ?>"
                             class="lp-listings__card-img">
                    </div>
                <?php endif; ?>
                <div class="lp-listings__card-body">
                    <?php if ( $listing['lp_listing_price'] ) : ?>
                        <div class="lp-listings__card-price"><?php echo esc_html( $listing['lp_listing_price'] ); ?></div>
                    <?php endif; ?>
                    <?php if ( $listing['lp_listing_address'] ) : ?>
                        <div class="lp-listings__card-address"><?php echo esc_html( $listing['lp_listing_address'] ); ?></div>
                    <?php endif; ?>
                    <div class="lp-listings__card-specs">
                        <?php if ( $listing['lp_listing_beds'] ) : ?>
                            <span><?php echo esc_html( $listing['lp_listing_beds'] ); ?> bd</span>
                        <?php endif; ?>
                        <?php if ( $listing['lp_listing_baths'] ) : ?>
                            <span><?php echo esc_html( $listing['lp_listing_baths'] ); ?> ba</span>
                        <?php endif; ?>
                        <?php if ( $listing['lp_listing_sqft'] ) : ?>
                            <span><?php echo esc_html( $listing['lp_listing_sqft'] ); ?> sqft</span>
                        <?php endif; ?>
                    </div>
                    <?php if ( $listing['lp_listing_description'] ) : ?>
                        <p class="lp-listings__card-desc"><?php echo esc_html( $listing['lp_listing_description'] ); ?></p>
                    <?php endif; ?>
                    <?php if ( $listing['lp_listing_url'] ) : ?>
                        <a href="<?php echo esc_url( $listing['lp_listing_url'] ); ?>"
                           target="_blank" rel="noopener"
                           class="lp-listings__card-link">View Listing →</a>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <?php if ( $view_all_url ) : ?>
        <div class="lp-listings__view-all">
            <a href="<?php echo esc_url( $view_all_url ); ?>" target="_blank" rel="noopener" class="lp-listings__view-all-btn">
                See All <?php echo esc_html( $city_name ); ?> Homes for Sale →
            </a>
        </div>
        <?php endif; ?>

    </div>
</section>
<?php endif; ?>


<?php
// ── FAQ SECTION ───────────────────────────────────────────────────────────────
$service_terms   = get_the_terms( get_the_ID(), 'post_service' );
$lp_service_slug = ( $service_terms && ! is_wp_error( $service_terms ) ) ? $service_terms[0]->slug : '';

blayne_faq_section( [
    'city_slug'    => get_post_field( 'post_name', get_the_ID() ),
    'service_slug' => $lp_service_slug,
    'heading'      => get_field( 'lp_faq_heading' ) ?: 'People Also Ask',
    'limit'        => 8,
] );
?>

<?php // ── 09 LEAD CAPTURE ───────────────────────────────────────────────────── ?>
<section class="lp-lead" id="lp-contact">
    <div class="lp-lead__inner">

        <div class="lp-lead__header">
            <h2 class="lp-lead__heading"><?php echo esc_html( $lead_h2 ); ?></h2>
            <?php if ( $lead_copy ) : ?>
                <p class="lp-lead__copy"><?php echo esc_html( $lead_copy ); ?></p>
            <?php else : ?>
                <p class="lp-lead__copy">
                    I specialize in <?php echo esc_html( $service_name ); ?> in <?php echo esc_html( $city_name ); ?>.
                    Let's talk about your goals — free consultation, no obligation.
                </p>
            <?php endif; ?>
            <p class="lp-lead__trust">No obligation. Local advice. Fast response.</p>
        </div>

        <div class="lp-lead__content">

            <div class="lp-lead__contact">
                <h3 class="lp-lead__contact-heading">Get in Touch</h3>
                <?php if ( $phone ) : ?>
                    <a href="tel:<?php echo esc_attr( $phone ); ?>" class="lp-lead__phone">
                        📞 <?php echo esc_html( $phone ); ?>
                    </a>
                <?php endif; ?>
                <div class="lp-lead__profiles">
                    <?php if ( $realtor_url ) : ?>
                        <a href="<?php echo esc_url( $realtor_url ); ?>" target="_blank" rel="noopener" class="lp-lead__profile-link">Realtor.com</a>
                    <?php endif; ?>
                    <?php if ( $zillow_url ) : ?>
                        <a href="<?php echo esc_url( $zillow_url ); ?>" target="_blank" rel="noopener" class="lp-lead__profile-link">Zillow</a>
                    <?php endif; ?>
                </div>
            </div>

            <div class="lp-lead__form">
                <?php if ( $ghl_form ) : ?>
                    <?php echo $ghl_form; ?>
                <?php else : ?>
                    <p class="lp-lead__no-form">
                        Please call directly at <a href="tel:<?php echo esc_attr( $phone ); ?>"><?php echo esc_html( $phone ); ?></a>
                    </p>
                <?php endif; ?>
            </div>

        </div>

    </div>
</section>


<?php // ── 10 NEARBY AREAS ───────────────────────────────────────────────────── ?>
<?php if ( $nearby_cities ) : ?>
<section class="lp-nearby">
    <div class="lp-nearby__inner">

        <h2 class="lp-nearby__heading"><?php echo esc_html( $nearby_heading ); ?></h2>
        <h3 class="lp-nearby__sub">Blayne serves all of Greater Los Angeles — find your city below.</h3>

        <div class="lp-nearby__grid">
            <?php foreach ( $nearby_cities as $nc ) :
                $thumb = $nc['lp_nearby_city_thumb'];
            ?>
            <a href="<?php echo esc_url( $nc['lp_nearby_city_url'] ); ?>" class="lp-nearby__card">
                <?php if ( $thumb ) : ?>
                    <div class="lp-nearby__card-img-wrap">
                        <img src="<?php echo esc_url( $thumb['url'] ); ?>"
                             alt="<?php echo esc_attr( $nc['lp_nearby_city_name'] ); ?> <?php echo esc_attr( $service_name ); ?>"
                             class="lp-nearby__card-img">
                    </div>
                <?php endif; ?>
                <div class="lp-nearby__card-body">
                    <div class="lp-nearby__card-name"><?php echo esc_html( $nc['lp_nearby_city_name'] ); ?></div>
                    <?php if ( $nc['lp_nearby_city_distance'] ) : ?>
                        <div class="lp-nearby__card-distance"><?php echo esc_html( $nc['lp_nearby_city_distance'] ); ?></div>
                    <?php endif; ?>
                    <?php if ( $nc['lp_nearby_city_descriptor'] ) : ?>
                        <div class="lp-nearby__card-desc"><?php echo esc_html( $nc['lp_nearby_city_descriptor'] ); ?></div>
                    <?php endif; ?>
                </div>
            </a>
            <?php endforeach; ?>
        </div>

    <div class="lp-nearby__view-all">
            <a href="<?php echo esc_url( home_url( '/cities/' ) ); ?>" class="lp-nearby__view-all-btn">
                View All Cities We Serve →
            </a>
        </div>

    </div>
</section>
<?php endif; ?>

<?php get_footer(); ?>
