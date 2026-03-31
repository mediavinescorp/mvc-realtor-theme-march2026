<?php
/**
 * Archive Template: Services Index
 * URL: /services/
 *
 * Sections:
 * 1. Hero — background image + overlay
 * 2. Services Grid — all 6 services linking to single pages
 * 3. People Also Search — 12 random cities from City CPT
 * 4. FAQ Section — general/buying FAQs
 * 5. Lead Form CTA
 */

get_header();

// ACF Options
$phone    = get_field( 'phone_number', 'option' );
$hero_url = 'https://j2nad1hj6o.wpdns.site/wp-content/uploads/2026/03/realtor-home-for-sales-sherman-oaks-blayne-pacelli-of-rodeo-realty.jpg';

?>

<!-- ============================================================
     SECTION 1 — HERO
     ============================================================ -->
<section class="svc-archive-hero"<?php if ( $hero_url ) : ?> style="background-image: url('<?php echo esc_url( $hero_url ); ?>')"<?php endif; ?>>
    <div class="svc-archive-hero__overlay"></div>
    <div class="svc-archive-hero__inner">
        <p class="svc-archive-hero__eyebrow">Los Angeles County Real Estate</p>
        <h1 class="svc-archive-hero__title">Expert Real Estate Services<br>Across Greater Los Angeles</h1>
        <h2 class="svc-archive-hero__sub">Whether you're buying, selling, or investing — Blayne Pacelli delivers results across 30+ cities in LA County</h2>
        <div class="svc-archive-hero__cta">
            <a href="<?php echo esc_url( home_url( '/contact' ) ); ?>" class="svc-archive-hero__btn">
                Get a Free Consultation
            </a>
            <?php if ( $phone ) : ?>
                <a href="tel:<?php echo esc_attr( $phone ); ?>" class="svc-archive-hero__btn svc-archive-hero__btn--outline">
                    Call <?php echo esc_html( $phone ); ?>
                </a>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- ============================================================
     SECTION 2 — SERVICES GRID
     ============================================================ -->
<section class="svc-archive-grid">
    <div class="svc-archive-grid__inner">
        <div class="svc-archive-grid__header">
            <h2 class="svc-archive-grid__title">How Blayne Can Help You</h2>
            <h3 class="svc-archive-grid__sub">Full-service real estate representation for buyers, sellers, and investors throughout Los Angeles County</h3>
        </div>
        <div class="svc-archive-grid__cards">
            <?php
            $services = new WP_Query( array(
                'post_type'      => 'service',
                'posts_per_page' => -1,
                'orderby'        => 'menu_order',
                'order'          => 'ASC',
            ) );

            if ( $services->have_posts() ) :
                while ( $services->have_posts() ) : $services->the_post();
                    $icon    = get_field( 'service_icon' );
                    $tagline = get_field( 'service_tagline' );
                    $intro   = get_field( 'service_intro' );
                    ?>
                    <a href="<?php the_permalink(); ?>" class="svc-archive-card">
                        <?php if ( $icon ) : ?>
                            <div class="svc-archive-card__icon">
                                <span class="dashicons <?php echo esc_attr( $icon ); ?>"></span>
                            </div>
                        <?php endif; ?>
                        <h3 class="svc-archive-card__title"><?php the_title(); ?></h3>
                        <?php if ( $tagline ) : ?>
                            <p class="svc-archive-card__tagline"><?php echo esc_html( $tagline ); ?></p>
                        <?php elseif ( $intro ) : ?>
                            <p class="svc-archive-card__tagline"><?php echo wp_trim_words( $intro, 15 ); ?></p>
                        <?php endif; ?>
                        <span class="svc-archive-card__cta">Learn More →</span>
                    </a>
                    <?php
                endwhile;
                wp_reset_postdata();
            endif;
            ?>
        </div>
    </div>
</section>

<!-- ============================================================
     SECTION 3 — PEOPLE ALSO SEARCH
     ============================================================ -->
<section class="svc-archive-cities">
    <div class="svc-archive-cities__inner">
        <div class="svc-archive-cities__header">
            <h2 class="svc-archive-cities__title">People Also Search</h2>
            <h3 class="svc-archive-cities__sub">Explore real estate markets across Greater Los Angeles County</h3>
        </div>
        <div class="svc-archive-cities__grid">
            <?php
            $cities = new WP_Query( array(
                'post_type'      => 'city',
                'posts_per_page' => 12,
                'orderby'        => 'rand',
            ) );

            if ( $cities->have_posts() ) :
                while ( $cities->have_posts() ) : $cities->the_post();
                    $city_img   = get_field( 'hero_image' );
                    $img_url    = $city_img ? $city_img['url'] : '';
                    $city_slug  = get_post_field( 'post_name', get_the_ID() );
                    $city_name  = get_the_title();
                    $city_url   = get_permalink();

                    // Get post_city term for this city
                    $city_terms = get_terms( array(
                        'taxonomy'   => 'post_city',
                        'name'       => $city_name,
                        'hide_empty' => false,
                    ) );
                    $city_term_slug = ( ! empty( $city_terms ) && ! is_wp_error( $city_terms ) ) ? $city_terms[0]->slug : $city_slug;

                    // Pull up to 3 landing pages tagged with this city
                    $city_services = new WP_Query( array(
                        'post_type'      => 'landing_page',
                        'posts_per_page' => 3,
                        'orderby'        => 'rand',
                        'tax_query'      => array(
                            array(
                                'taxonomy' => 'post_city',
                                'field'    => 'slug',
                                'terms'    => $city_term_slug,
                            ),
                        ),
                    ) );
                    ?>
                    <div class="svc-archive-city-card">
                        <a href="<?php echo esc_url( $city_url ); ?>" class="svc-archive-city-card__img-link">
                            <?php if ( $img_url ) : ?>
                                <div class="svc-archive-city-card__img-wrap">
                                    <img src="<?php echo esc_url( $img_url ); ?>"
                                         alt="<?php echo esc_attr( $city_name ); ?>"
                                         class="svc-archive-city-card__img"
                                         loading="lazy">
                                </div>
                            <?php else : ?>
                                <div class="svc-archive-city-card__img-wrap svc-archive-city-card__img-wrap--placeholder"></div>
                            <?php endif; ?>
                            <div class="svc-archive-city-card__header">
                                <span class="svc-archive-city-card__name"><?php echo esc_html( $city_name ); ?></span>
                                <span class="svc-archive-city-card__arrow">→</span>
                            </div>
                        </a>
                        <?php if ( $city_services->have_posts() ) : ?>
                            <ul class="svc-archive-city-card__services">
                                <?php while ( $city_services->have_posts() ) : $city_services->the_post(); ?>
                                    <li class="svc-archive-city-card__service-item">
                                        <a href="<?php the_permalink(); ?>" class="svc-archive-city-card__service-link">
                                            <?php the_title(); ?>
                                        </a>
                                    </li>
                                <?php endwhile; ?>
                            </ul>
                            <?php wp_reset_postdata(); ?>
                        <?php endif; ?>
                    </div>
                    <?php
                endwhile;
                wp_reset_postdata();
            endif;
            ?>
        </div>
        <div class="svc-archive-cities__cta">
            <a href="<?php echo esc_url( get_post_type_archive_link( 'city' ) ); ?>" class="svc-archive-btn svc-archive-btn--outline">
                View All 30 Cities →
            </a>
        </div>
    </div>
</section>

<!-- ============================================================
     SECTION 4 — FAQ
     ============================================================ -->
<section class="svc-archive-faq">
    <div class="svc-archive-faq__inner">
        <div class="svc-archive-faq__header">
            <h2 class="svc-archive-faq__title">Frequently Asked Questions</h2>
            <h3 class="svc-archive-faq__sub">Common questions about buying and selling real estate in Los Angeles</h3>
        </div>
        <div class="svc-archive-faq__list">
            <?php
            $faqs = new WP_Query( array(
                'post_type'      => 'faq',
                'posts_per_page' => 6,
                'orderby'        => 'rand',
            ) );

            if ( $faqs->have_posts() ) :
                while ( $faqs->have_posts() ) : $faqs->the_post(); ?>
                    <div class="svc-archive-faq__item">
                        <button class="svc-archive-faq__question" aria-expanded="false">
                            <?php the_title(); ?>
                            <span class="svc-archive-faq__icon" aria-hidden="true">+</span>
                        </button>
                        <div class="svc-archive-faq__answer" hidden>
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
     SECTION 5 — LEAD FORM
     ============================================================ -->
<?php get_template_part( 'template-parts/lead-form' ); ?>

<script>
// FAQ Accordion
document.querySelectorAll('.svc-archive-faq__question').forEach(function(btn) {
    btn.addEventListener('click', function() {
        var answer   = this.nextElementSibling;
        var icon     = this.querySelector('.svc-archive-faq__icon');
        var expanded = this.getAttribute('aria-expanded') === 'true';
        document.querySelectorAll('.svc-archive-faq__question').forEach(function(b) {
            b.setAttribute('aria-expanded', 'false');
            b.querySelector('.svc-archive-faq__icon').textContent = '+';
            b.nextElementSibling.hidden = true;
        });
        if ( ! expanded ) {
            this.setAttribute('aria-expanded', 'true');
            icon.textContent = '−';
            answer.hidden = false;
        }
    });
});
</script>

<?php get_footer(); ?>