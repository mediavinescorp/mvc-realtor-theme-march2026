<?php get_header(); ?>

<?php
$faq_question    = get_the_title();
$short_answer    = get_field( 'short_answer' );
$long_answer     = get_field( 'long_answer' );
$related_city    = get_field( 'related_city' ); // relationship field → returns array of post objects
$meta_desc       = get_field( 'meta_description' );
$phone           = get_field( 'phone_number', 'option' );
$photo           = get_field( 'blayne_photo', 'option' );
$ghl_form        = get_field( 'ghl_form_embed', 'option' );

// Taxonomies
$city_terms    = get_the_terms( get_the_ID(), 'post_city' );
$service_terms = get_the_terms( get_the_ID(), 'post_service' );
$topic_terms   = get_the_terms( get_the_ID(), 'post_topic' );

// Breadcrumb home → FAQs → question
$faq_archive_url = get_post_type_archive_link( 'faq' );
?>

<?php
// ── FAQPage Schema ────────────────────────────────────────────────────────────
if ( $short_answer || $long_answer ) :
    $answer_text = $short_answer
        ? wp_strip_all_tags( $short_answer )
        : wp_strip_all_tags( $long_answer );
    $schema = [
        '@context'   => 'https://schema.org',
        '@type'      => 'FAQPage',
        'mainEntity' => [
            [
                '@type'          => 'Question',
                'name'           => $faq_question,
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text'  => $answer_text,
                ],
            ]
        ],
    ];
    echo '<script type="application/ld+json">' . wp_json_encode( $schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) . '</script>';
endif;
?>

<!-- Breadcrumb -->
<nav class="faq-breadcrumb" aria-label="Breadcrumb">
    <div class="faq-breadcrumb__inner">
        <ol class="faq-breadcrumb__list">
            <li><a href="<?php echo esc_url( home_url( '/' ) ); ?>">Home</a></li>
            <li aria-hidden="true">›</li>
            <?php if ( $faq_archive_url ) : ?>
                <li><a href="<?php echo esc_url( $faq_archive_url ); ?>">FAQs</a></li>
                <li aria-hidden="true">›</li>
            <?php endif; ?>
            <li aria-current="page"><?php echo esc_html( $faq_question ); ?></li>
        </ol>
    </div>
</nav>

<!-- FAQ Hero -->
<section class="faq-hero">
    <div class="faq-hero__inner">

        <!-- Taxonomy tags -->
        <div class="faq-hero__tags">
            <?php if ( $city_terms && ! is_wp_error( $city_terms ) ) :
                foreach ( $city_terms as $t ) : ?>
                    <span class="blog-card__tag blog-card__tag--city"><?php echo esc_html( $t->name ); ?></span>
            <?php endforeach; endif; ?>
            <?php if ( $service_terms && ! is_wp_error( $service_terms ) ) :
                foreach ( $service_terms as $t ) : ?>
                    <span class="blog-card__tag blog-card__tag--service"><?php echo esc_html( $t->name ); ?></span>
            <?php endforeach; endif; ?>
            <?php if ( $topic_terms && ! is_wp_error( $topic_terms ) ) :
                foreach ( $topic_terms as $t ) : ?>
                    <span class="blog-card__tag blog-card__tag--topic"><?php echo esc_html( $t->name ); ?></span>
            <?php endforeach; endif; ?>
        </div>

        <h1 class="faq-hero__question"><?php echo esc_html( $faq_question ); ?></h1>
        <div class="faq-hero__meta">
            <span class="faq-hero__date">Updated <?php echo get_the_modified_date( 'F j, Y' ); ?></span>
        </div>
    </div>
</section>

<!-- Main Layout -->
<div class="faq-layout">
    <div class="faq-layout__inner">

        <!-- ── Main Content ── -->
        <main class="faq-main">

            <!-- Short Answer (AEO highlight box) -->
            <?php if ( $short_answer ) : ?>
            <div class="faq-short-answer">
                <div class="faq-short-answer__label">
                    <span class="faq-short-answer__icon">⚡</span>
                    Quick Answer
                </div>
                <p class="faq-short-answer__text"><?php echo esc_html( $short_answer ); ?></p>
            </div>
            <?php endif; ?>

            <!-- Long Answer -->
            <?php if ( $long_answer ) : ?>
            <div class="faq-long-answer">
                <?php if ( $short_answer ) : ?>
                    <h2 class="faq-long-answer__heading">Full Explanation</h2>
                <?php endif; ?>
                <div class="faq-long-answer__body wysiwyg-content">
                    <?php echo wp_kses_post( $long_answer ); ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- Related Cities -->
            <?php if ( $related_city && is_array( $related_city ) ) : ?>
            <div class="faq-related-cities">
                <h3 class="faq-related-cities__heading">Relevant Cities</h3>
                <div class="faq-related-cities__grid">
                    <?php foreach ( $related_city as $city_post ) : ?>
                        <a href="<?php echo esc_url( get_permalink( $city_post->ID ) ); ?>"
                           class="faq-related-cities__item">
                            <?php echo esc_html( get_the_title( $city_post->ID ) ); ?> →
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- Post navigation -->
            <div class="faq-nav">
                <?php
                the_post_navigation( [
                    'prev_text' => '<span class="faq-nav__label">Previous Question</span><span class="faq-nav__title">← %title</span>',
                    'next_text' => '<span class="faq-nav__label">Next Question</span><span class="faq-nav__title">%title →</span>',
                ] );
                ?>
            </div>

            <!-- Related FAQs -->
            <?php
            // pull FAQs sharing same city or service taxonomy terms
            $related_tax_query = [ 'relation' => 'OR' ];
            if ( $city_terms && ! is_wp_error( $city_terms ) ) {
                $related_tax_query[] = [
                    'taxonomy' => 'post_city',
                    'field'    => 'term_id',
                    'terms'    => wp_list_pluck( $city_terms, 'term_id' ),
                ];
            }
            if ( $service_terms && ! is_wp_error( $service_terms ) ) {
                $related_tax_query[] = [
                    'taxonomy' => 'post_service',
                    'field'    => 'term_id',
                    'terms'    => wp_list_pluck( $service_terms, 'term_id' ),
                ];
            }
            $related_faqs_args = [
                'post_type'      => 'faq',
                'posts_per_page' => 4,
                'post__not_in'   => [ get_the_ID() ],
                'orderby'        => 'rand',
            ];
            if ( count( $related_tax_query ) > 1 ) {
                $related_faqs_args['tax_query'] = $related_tax_query;
            }
            $related_faqs = new WP_Query( $related_faqs_args );
            ?>
            <?php if ( $related_faqs->have_posts() ) : ?>
            <div class="faq-related">
                <h3 class="faq-related__heading">Related Questions</h3>
                <ul class="faq-related__list">
                    <?php while ( $related_faqs->have_posts() ) : $related_faqs->the_post();
                        $rel_short = get_field( 'short_answer' );
                    ?>
                        <li class="faq-related__item">
                            <a href="<?php the_permalink(); ?>" class="faq-related__question">
                                <?php the_title(); ?>
                            </a>
                            <?php if ( $rel_short ) : ?>
                                <p class="faq-related__preview"><?php echo esc_html( wp_trim_words( $rel_short, 18 ) ); ?></p>
                            <?php endif; ?>
                        </li>
                    <?php endwhile; wp_reset_postdata(); ?>
                </ul>
            </div>
            <?php endif; ?>

        </main>

        <!-- ── Sidebar ── -->
        <aside class="faq-sidebar">

            <!-- Blayne Bio -->
            <div class="sidebar__widget sidebar__bio">
                <?php if ( $photo ) : ?>
                    <img src="<?php echo esc_url( $photo['url'] ); ?>"
                         alt="Blayne Pacelli Realtor"
                         class="sidebar__bio-photo">
                <?php endif; ?>
                <h3 class="sidebar__bio-name">Blayne Pacelli</h3>
                <p class="sidebar__bio-title">Realtor — Rodeo Realty</p>
                <p class="sidebar__bio-text">Your dedicated real estate agent serving Greater Los Angeles. Questions about buying or selling? I'm here to help.</p>
                <?php if ( $phone ) : ?>
                    <a href="tel:<?php echo esc_attr( $phone ); ?>" class="sidebar__bio-btn">
                        Call <?php echo esc_html( $phone ); ?>
                    </a>
                <?php endif; ?>
            </div>

            <!-- Lead Form -->
            <?php if ( $ghl_form ) : ?>
            <div class="sidebar__widget sidebar__form">
                <h3 class="sidebar__widget-heading">Get a Free Consultation</h3>
                <?php echo $ghl_form; ?>
            </div>
            <?php endif; ?>

            <!-- Browse by City -->
            <?php if ( $city_terms && ! is_wp_error( $city_terms ) ) : ?>
            <div class="sidebar__widget">
                <h3 class="sidebar__widget-heading">More <?php echo esc_html( $city_terms[0]->name ); ?> FAQs</h3>
                <?php
                $city_faqs = new WP_Query( [
                    'post_type'      => 'faq',
                    'posts_per_page' => 6,
                    'post__not_in'   => [ get_the_ID() ],
                    'tax_query'      => [ [
                        'taxonomy' => 'post_city',
                        'field'    => 'term_id',
                        'terms'    => $city_terms[0]->term_id,
                    ] ],
                ] );
                if ( $city_faqs->have_posts() ) : ?>
                    <ul class="sidebar__recent-list">
                        <?php while ( $city_faqs->have_posts() ) : $city_faqs->the_post(); ?>
                            <li class="sidebar__recent-item">
                                <div class="sidebar__recent-content">
                                    <a href="<?php the_permalink(); ?>" class="sidebar__recent-title">
                                        <?php the_title(); ?>
                                    </a>
                                </div>
                            </li>
                        <?php endwhile; wp_reset_postdata(); ?>
                    </ul>
                <?php endif; ?>
            </div>
            <?php endif; ?>

            <!-- Browse all FAQ topics -->
            <?php
            $all_faq_topics = get_terms( [ 'taxonomy' => 'post_topic', 'hide_empty' => true ] );
            if ( $all_faq_topics && ! is_wp_error( $all_faq_topics ) ) :
            ?>
            <div class="sidebar__widget">
                <h3 class="sidebar__widget-heading">Browse by Topic</h3>
                <ul class="sidebar__tag-list">
                    <?php foreach ( $all_faq_topics as $topic ) : ?>
                        <li>
                            <a href="<?php echo esc_url( get_term_link( $topic ) ); ?>"
                               class="sidebar__tag-link">
                                <?php echo esc_html( $topic->name ); ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php endif; ?>

        </aside>

    </div>
</div>

<!-- Google Reviews -->
<section class="reviews-section">
    <div class="reviews-section__inner">
        <h2 class="reviews-section__heading">What Our Clients Say</h2>
        <h3 class="reviews-section__sub">Real reviews from real clients on Google</h3>
        <?php echo do_shortcode('[trustindex no-registration=google]'); ?>
    </div>
</section>

<!-- Lead Form -->
<?php get_template_part( 'template-parts/lead-form' ); ?>

<?php get_footer(); ?>
