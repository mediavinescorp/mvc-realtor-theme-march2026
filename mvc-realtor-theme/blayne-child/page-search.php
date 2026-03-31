<?php
/**
 * Template Name: Search Homes
 *
 * Full-featured property search page for Blayne Pacelli.
 * Sections: Search Hero, Filter Bar, Featured Listings,
 *           Why Search with Blayne, Cities Grid, FAQ, Contact CTA
 */

get_header();

// ACF Options
$phone    = get_field( 'phone_number', 'option' );
$email    = get_field( 'email_address', 'option' );
$agent    = get_field( 'agent_name', 'option' );
$dre      = get_field( 'dre_number', 'option' );
$bio_photo = get_field( 'agent_photo', 'option' );

?>

<!-- ============================================================
     SECTION 1 — SEARCH HERO
     ============================================================ -->
<section class="search-hero">
    <div class="search-hero__inner">
        <p class="search-hero__eyebrow">Los Angeles County MLS Search</p>
        <h1 class="search-hero__title">Find Your Dream Home</h1>
        <p class="search-hero__sub">Search thousands of active listings across Greater Los Angeles</p>
        <div class="search-hero__bar">
            <?php echo do_shortcode('[idx-omnibar styles="1" extra="0" min_price="0" remove_price_validation="0" ]'); ?>
        </div>
        <!-- Quick Filter Tabs -->
        <div class="search-filters">
            <a href="https://blaynepacelli.idxbroker.com/idx/results/listings?pt=sfr&srt=newest" target="_blank" class="search-filter__btn search-filter__btn--active">🏠 Residential</a>
            <a href="https://blaynepacelli.idxbroker.com/idx/results/listings?pt=cnd&srt=newest" target="_blank" class="search-filter__btn">🏢 Condos</a>
            <a href="https://blaynepacelli.idxbroker.com/idx/results/listings?pt=mfr&srt=newest" target="_blank" class="search-filter__btn">🏘 Multi-Family</a>
            <a href="https://blaynepacelli.idxbroker.com/idx/results/listings?pt=lnd&srt=newest" target="_blank" class="search-filter__btn">🌿 Land</a>
            <a href="https://blaynepacelli.idxbroker.com/idx/results/listings?pt=com&srt=newest" target="_blank" class="search-filter__btn">🏬 Commercial</a>
            <a href="https://blaynepacelli.idxbroker.com/idx/results/listings?feature=openhouse&srt=newest" target="_blank" class="search-filter__btn">📅 Open Houses</a>
        </div>
    </div>
</section>

<!-- ============================================================
     SECTION 2 — FEATURED LISTINGS BY BLAYNE
     ============================================================ -->
<section class="search-featured">
    <div class="search-featured__inner">
        <div class="search-section__header">
            <h2 class="search-section__title">Homes Listed by Blayne</h2>
            <h3 class="search-section__sub">Browse active listings currently represented by Blayne Pacelli in Greater Los Angeles</h3>
        </div>
        <div class="search-featured__cta-wrap">
            <a href="https://blaynepacelli.idxbroker.com/idx/results/listings?srt=newest" target="_blank" class="search-btn search-btn--primary">
                View Blayne's Active Listings →
            </a>
            <a href="https://blaynepacelli.idxbroker.com/idx/results/listings?feature=openhouse&srt=newest" target="_blank" class="search-btn search-btn--outline">
                Open Houses This Weekend →
            </a>
        </div>
    </div>
</section>

<!-- ============================================================
     SECTION 3 — WHY SEARCH WITH BLAYNE
     ============================================================ -->
<section class="search-why">
    <div class="search-why__inner">
        <div class="search-section__header search-section__header--light">
            <h2 class="search-section__title">Why Work with Blayne?</h2>
            <h3 class="search-section__sub">More than just listings — a trusted guide through every step</h3>
        </div>
        <div class="search-why__grid">
            <div class="search-why__card">
                <span class="search-why__icon">🔑</span>
                <h3 class="search-why__card-title">Exclusive Access</h3>
                <p class="search-why__card-text">Get early access to listings before they hit the public market through Blayne's network of LA agents.</p>
            </div>
            <div class="search-why__card">
                <span class="search-why__icon">📍</span>
                <h3 class="search-why__card-title">Local Expertise</h3>
                <p class="search-why__card-text">30+ cities covered across Greater Los Angeles County. Blayne knows every neighborhood, school district, and street.</p>
            </div>
            <div class="search-why__card">
                <span class="search-why__icon">💰</span>
                <h3 class="search-why__card-title">Best Price Guaranteed</h3>
                <p class="search-why__card-text">Expert negotiation skills that consistently get buyers below asking price in competitive LA markets.</p>
            </div>
            <div class="search-why__card">
                <span class="search-why__icon">⚡</span>
                <h3 class="search-why__card-title">Fast Response</h3>
                <p class="search-why__card-text">In a fast-moving market, speed matters. Blayne responds same-day so you never miss an opportunity.</p>
            </div>
            <div class="search-why__card">
                <span class="search-why__icon">📋</span>
                <h3 class="search-why__card-title">Full-Service Support</h3>
                <p class="search-why__card-text">From search to close, Blayne handles inspections, financing referrals, escrow, and every detail in between.</p>
            </div>
            <div class="search-why__card">
                <span class="search-why__icon">⭐</span>
                <h3 class="search-why__card-title">5-Star Rated</h3>
                <p class="search-why__card-text">Hundreds of satisfied buyers and sellers across Los Angeles trust Blayne to deliver results every time.</p>
            </div>
        </div>
        <?php if ( $dre ) : ?>
        <p class="search-why__dre"><?php echo esc_html( $dre ); ?></p>
        <?php endif; ?>
    </div>
</section>

<!-- ============================================================
     SECTION 4 — BROWSE BY CITY
     ============================================================ -->
<section class="search-cities">
    <div class="search-cities__inner">
        <div class="search-section__header">
            <h2 class="search-section__title">Browse by City</h2>
            <h3 class="search-section__sub">Explore active listings in Blayne's most popular markets</h3>
        </div>
        <div class="search-cities__grid">
            <?php
            $cities = new WP_Query( array(
                'post_type'      => 'city',
                'posts_per_page' => 12,
                'orderby'        => 'rand',
            ) );
            if ( $cities->have_posts() ) :
                while ( $cities->have_posts() ) : $cities->the_post();
                    $city_name = get_the_title();
                    $city_url  = get_permalink();
                    ?>
                    <a href="<?php echo esc_url( $city_url ); ?>" class="search-city__card">
                        <span class="search-city__name"><?php echo esc_html( $city_name ); ?></span>
                        <span class="search-city__arrow">→</span>
                    </a>
                    <?php
                endwhile;
                wp_reset_postdata();
            endif;
            ?>
        </div>
        <div class="search-cities__cta">
            <a href="<?php echo esc_url( get_post_type_archive_link( 'city' ) ); ?>" class="search-btn search-btn--outline">
                View All 30 Cities →
            </a>
        </div>
    </div>
</section>

<!-- ============================================================
     SECTION 5 — BUYING FAQ
     ============================================================ -->
<section class="search-faq">
    <div class="search-faq__inner">
        <div class="search-section__header">
            <h2 class="search-section__title">Buying a Home in LA — FAQs</h2>
            <h3 class="search-section__sub">Common questions from buyers searching in Los Angeles County</h3>
        </div>
        <div class="search-faq__list">
            <?php
            // Pull FAQs tagged with buying service
            $faqs = new WP_Query( array(
                'post_type'      => 'faq',
                'posts_per_page' => 6,
                'tax_query'      => array(
                    array(
                        'taxonomy' => 'post_service',
                        'field'    => 'slug',
                        'terms'    => 'buying-a-home',
                    ),
                ),
            ) );

            // Fallback — if no buying FAQs, get any FAQs
            if ( ! $faqs->have_posts() ) {
                $faqs = new WP_Query( array(
                    'post_type'      => 'faq',
                    'posts_per_page' => 6,
                ) );
            }

            if ( $faqs->have_posts() ) :
                while ( $faqs->have_posts() ) : $faqs->the_post(); ?>
                    <div class="search-faq__item">
                        <button class="search-faq__question" aria-expanded="false">
                            <?php the_title(); ?>
                            <span class="search-faq__icon" aria-hidden="true">+</span>
                        </button>
                        <div class="search-faq__answer" hidden>
                            <?php the_content(); ?>
                        </div>
                    </div>
                <?php endwhile;
                wp_reset_postdata();
            endif;
            ?>
        </div>
    </div>
</section>

<!-- ============================================================
     SECTION 6 — CONTACT / LEAD FORM
     ============================================================ -->
<section class="search-contact">
    <div class="search-contact__inner">
        <?php get_template_part( 'template-parts/lead-form' ); ?>
    </div>
</section>

<script>
// FAQ Accordion
document.querySelectorAll('.search-faq__question').forEach(function(btn) {
    btn.addEventListener('click', function() {
        var answer  = this.nextElementSibling;
        var icon    = this.querySelector('.search-faq__icon');
        var expanded = this.getAttribute('aria-expanded') === 'true';
        // Close all
        document.querySelectorAll('.search-faq__question').forEach(function(b) {
            b.setAttribute('aria-expanded', 'false');
            b.querySelector('.search-faq__icon').textContent = '+';
            b.nextElementSibling.hidden = true;
        });
        // Open clicked if it was closed
        if ( ! expanded ) {
            this.setAttribute('aria-expanded', 'true');
            icon.textContent = '−';
            answer.hidden = false;
        }
    });
});

// Filter buttons active state
document.querySelectorAll('.search-filter__btn').forEach(function(btn) {
    btn.addEventListener('click', function() {
        document.querySelectorAll('.search-filter__btn').forEach(function(b) {
            b.classList.remove('search-filter__btn--active');
        });
        this.classList.add('search-filter__btn--active');
    });
});
</script>

<?php get_footer(); ?>