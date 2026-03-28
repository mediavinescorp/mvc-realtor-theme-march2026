<?php get_header(); ?>

<?php
// ── Filters — prefixed with faq_ to avoid conflicts with city/service CPT slugs
$filter_city    = isset( $_GET['faq_city'] )    ? sanitize_text_field( $_GET['faq_city'] )    : '';
$filter_service = isset( $_GET['faq_service'] ) ? sanitize_text_field( $_GET['faq_service'] ) : '';
$filter_topic   = isset( $_GET['faq_topic'] )   ? sanitize_text_field( $_GET['faq_topic'] )   : '';
$paged          = get_query_var( 'paged' ) ? get_query_var( 'paged' ) : 1;
$has_filters    = $filter_city || $filter_service || $filter_topic;
$clear_url      = get_post_type_archive_link( 'faq' );

// ── Tax query ─────────────────────────────────────────────────────────────────
$tax_query = [ 'relation' => 'AND' ];
if ( $filter_city )    $tax_query[] = [ 'taxonomy' => 'post_city',    'field' => 'slug', 'terms' => $filter_city ];
if ( $filter_service ) $tax_query[] = [ 'taxonomy' => 'post_service', 'field' => 'slug', 'terms' => $filter_service ];
if ( $filter_topic )   $tax_query[] = [ 'taxonomy' => 'post_topic',   'field' => 'slug', 'terms' => $filter_topic ];

// ── Query ─────────────────────────────────────────────────────────────────────
$faq_args = [
    'post_type'      => 'faq',
    'post_status'    => 'publish',
    'posts_per_page' => 18,
    'paged'          => $paged,
    'orderby'        => [ 'meta_value_num' => 'ASC', 'title' => 'ASC' ],
    'meta_key'       => 'sort_order',
];
if ( count( $tax_query ) > 1 ) $faq_args['tax_query'] = $tax_query;
$faq_query = new WP_Query( $faq_args );

// ── Terms for dropdowns ───────────────────────────────────────────────────────
$all_cities   = get_terms( [ 'taxonomy' => 'post_city',    'hide_empty' => true ] );
$all_services = get_terms( [ 'taxonomy' => 'post_service', 'hide_empty' => true ] );
$all_topics   = get_terms( [ 'taxonomy' => 'post_topic',   'hide_empty' => true ] );

// ── Filter URL helper ─────────────────────────────────────────────────────────
function acfpi_faq_filter_url( $key, $slug ) {
    $params = [];
    if ( isset( $_GET['faq_city'] ) )    $params['faq_city']    = $_GET['faq_city'];
    if ( isset( $_GET['faq_service'] ) ) $params['faq_service'] = $_GET['faq_service'];
    if ( isset( $_GET['faq_topic'] ) )   $params['faq_topic']   = $_GET['faq_topic'];
    if ( $slug ) $params[ $key ] = $slug;
    else         unset( $params[ $key ] );
    $base = get_post_type_archive_link( 'faq' );
    return $params ? $base . '?' . http_build_query( $params ) : $base;
}

// ── FAQPage Schema for visible FAQs ──────────────────────────────────────────
$schema_entities = [];
if ( $faq_query->have_posts() ) {
    foreach ( $faq_query->posts as $faq_post ) {
        $q = $faq_post->post_title;
        $a = get_field( 'short_answer', $faq_post->ID ) ?: get_field( 'long_answer', $faq_post->ID );
        if ( $q && $a ) {
            $schema_entities[] = [
                '@type'          => 'Question',
                'name'           => $q,
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text'  => wp_strip_all_tags( $a ),
                ],
            ];
        }
    }
}
if ( $schema_entities ) {
    $schema = [
        '@context'   => 'https://schema.org',
        '@type'      => 'FAQPage',
        'mainEntity' => $schema_entities,
    ];
    echo '<script type="application/ld+json">' . wp_json_encode( $schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) . '</script>';
}
?>

<!-- FAQ Hero -->
<section class="faq-archive-hero">
    <div class="faq-archive-hero__inner">
        <div class="faq-archive-hero__eyebrow">Knowledge Base</div>
        <h1 class="faq-archive-hero__title">Frequently Asked Questions</h1>
        <p class="faq-archive-hero__sub">Answers to the most common questions about buying, selling, and investing in Los Angeles real estate.</p>
    </div>
</section>

<!-- ── Filter Bar ─────────────────────────────────────────────────────────── -->
<div class="blog-filters faq-filters" id="faq-filters">
    <div class="blog-filters__inner">
        <div class="blog-filters__dropdowns">

            <!-- City -->
            <?php if ( $all_cities && ! is_wp_error( $all_cities ) ) : ?>
            <div class="blog-filter-dropdown">
                <button class="blog-filter-btn <?php echo $filter_city ? 'active' : ''; ?>"
                        id="faq-filter-city-btn"
                        aria-expanded="false"
                        aria-controls="faq-filter-city-menu">
                    <?php
                    if ( $filter_city ) {
                        $t = get_term_by( 'slug', $filter_city, 'post_city' );
                        echo esc_html( $t ? $t->name : 'City' );
                    } else { echo 'City'; }
                    ?>
                    <svg class="blog-filter-btn__arrow" width="12" height="12" viewBox="0 0 12 12" fill="none">
                        <path d="M2 4l4 4 4-4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>
                <div class="blog-filter-menu" id="faq-filter-city-menu" hidden>
                    <a href="<?php echo esc_url( acfpi_faq_filter_url( 'faq_city', '' ) ); ?>"
                       class="blog-filter-menu__item <?php echo ! $filter_city ? 'selected' : ''; ?>">All Cities</a>
                    <?php foreach ( $all_cities as $term ) : ?>
                        <a href="<?php echo esc_url( acfpi_faq_filter_url( 'faq_city', $term->slug ) ); ?>"
                           class="blog-filter-menu__item <?php echo $filter_city === $term->slug ? 'selected' : ''; ?>">
                            <?php echo esc_html( $term->name ); ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- Service -->
            <?php if ( $all_services && ! is_wp_error( $all_services ) ) : ?>
            <div class="blog-filter-dropdown">
                <button class="blog-filter-btn <?php echo $filter_service ? 'active' : ''; ?>"
                        id="faq-filter-service-btn"
                        aria-expanded="false"
                        aria-controls="faq-filter-service-menu">
                    <?php
                    if ( $filter_service ) {
                        $t = get_term_by( 'slug', $filter_service, 'post_service' );
                        echo esc_html( $t ? $t->name : 'Service' );
                    } else { echo 'Service'; }
                    ?>
                    <svg class="blog-filter-btn__arrow" width="12" height="12" viewBox="0 0 12 12" fill="none">
                        <path d="M2 4l4 4 4-4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>
                <div class="blog-filter-menu" id="faq-filter-service-menu" hidden>
                    <a href="<?php echo esc_url( acfpi_faq_filter_url( 'faq_service', '' ) ); ?>"
                       class="blog-filter-menu__item <?php echo ! $filter_service ? 'selected' : ''; ?>">All Services</a>
                    <?php foreach ( $all_services as $term ) : ?>
                        <a href="<?php echo esc_url( acfpi_faq_filter_url( 'faq_service', $term->slug ) ); ?>"
                           class="blog-filter-menu__item <?php echo $filter_service === $term->slug ? 'selected' : ''; ?>">
                            <?php echo esc_html( $term->name ); ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- Topic -->
            <?php if ( $all_topics && ! is_wp_error( $all_topics ) ) : ?>
            <div class="blog-filter-dropdown">
                <button class="blog-filter-btn <?php echo $filter_topic ? 'active' : ''; ?>"
                        id="faq-filter-topic-btn"
                        aria-expanded="false"
                        aria-controls="faq-filter-topic-menu">
                    <?php
                    if ( $filter_topic ) {
                        $t = get_term_by( 'slug', $filter_topic, 'post_topic' );
                        echo esc_html( $t ? $t->name : 'Topic' );
                    } else { echo 'Topic'; }
                    ?>
                    <svg class="blog-filter-btn__arrow" width="12" height="12" viewBox="0 0 12 12" fill="none">
                        <path d="M2 4l4 4 4-4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>
                <div class="blog-filter-menu" id="faq-filter-topic-menu" hidden>
                    <a href="<?php echo esc_url( acfpi_faq_filter_url( 'faq_topic', '' ) ); ?>"
                       class="blog-filter-menu__item <?php echo ! $filter_topic ? 'selected' : ''; ?>">All Topics</a>
                    <?php foreach ( $all_topics as $term ) : ?>
                        <a href="<?php echo esc_url( acfpi_faq_filter_url( 'faq_topic', $term->slug ) ); ?>"
                           class="blog-filter-menu__item <?php echo $filter_topic === $term->slug ? 'selected' : ''; ?>">
                            <?php echo esc_html( $term->name ); ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- Clear -->
            <?php if ( $has_filters ) : ?>
            <a href="<?php echo esc_url( $clear_url ); ?>" class="blog-filter-clear-btn">✕ Clear Filters</a>
            <?php endif; ?>

        </div>

        <!-- Active tags + count -->
        <?php if ( $has_filters ) : ?>
        <div class="blog-filter-active">
            <?php if ( $filter_city ) :
                $t = get_term_by( 'slug', $filter_city, 'post_city' ); ?>
                <span class="blog-filter-active__tag blog-filter-active__tag--city">
                    <?php echo esc_html( $t ? $t->name : $filter_city ); ?>
                    <a href="<?php echo esc_url( acfpi_faq_filter_url( 'faq_city', '' ) ); ?>">×</a>
                </span>
            <?php endif; ?>
            <?php if ( $filter_service ) :
                $t = get_term_by( 'slug', $filter_service, 'post_service' ); ?>
                <span class="blog-filter-active__tag blog-filter-active__tag--service">
                    <?php echo esc_html( $t ? $t->name : $filter_service ); ?>
                    <a href="<?php echo esc_url( acfpi_faq_filter_url( 'faq_service', '' ) ); ?>">×</a>
                </span>
            <?php endif; ?>
            <?php if ( $filter_topic ) :
                $t = get_term_by( 'slug', $filter_topic, 'post_topic' ); ?>
                <span class="blog-filter-active__tag blog-filter-active__tag--topic">
                    <?php echo esc_html( $t ? $t->name : $filter_topic ); ?>
                    <a href="<?php echo esc_url( acfpi_faq_filter_url( 'faq_topic', '' ) ); ?>">×</a>
                </span>
            <?php endif; ?>
            <span class="blog-results-count">
                <?php $total = $faq_query->found_posts;
                echo $total . ' ' . ( $total === 1 ? 'question' : 'questions' ); ?>
            </span>
        </div>
        <?php else : ?>
        <div class="blog-filter-active">
            <span class="blog-results-count">
                <?php $total = $faq_query->found_posts;
                echo $total . ' ' . ( $total === 1 ? 'question' : 'questions' ); ?>
            </span>
        </div>
        <?php endif; ?>

    </div>
</div>

<!-- ── FAQ Grid ───────────────────────────────────────────────────────────── -->
<section class="faq-archive">
    <div class="faq-archive__inner">

        <?php if ( $faq_query->have_posts() ) : ?>
            <div class="faq-archive__grid">
                <?php while ( $faq_query->have_posts() ) : $faq_query->the_post();
                    $short    = get_field( 'short_answer' );
                    $f_cities   = get_the_terms( get_the_ID(), 'post_city' );
                    $f_services = get_the_terms( get_the_ID(), 'post_service' );
                    $f_topics   = get_the_terms( get_the_ID(), 'post_topic' );
                ?>
                <article class="faq-card">
                    <div class="faq-card__body">

                        <!-- Tags -->
                        <div class="faq-card__tags">
                            <?php if ( $f_cities && ! is_wp_error( $f_cities ) ) :
                                foreach ( $f_cities as $t ) : ?>
                                    <span class="blog-card__tag blog-card__tag--city"><?php echo esc_html( $t->name ); ?></span>
                            <?php endforeach; endif; ?>
                            <?php if ( $f_services && ! is_wp_error( $f_services ) ) :
                                foreach ( $f_services as $t ) : ?>
                                    <span class="blog-card__tag blog-card__tag--service"><?php echo esc_html( $t->name ); ?></span>
                            <?php endforeach; endif; ?>
                            <?php if ( $f_topics && ! is_wp_error( $f_topics ) ) :
                                foreach ( $f_topics as $t ) : ?>
                                    <span class="blog-card__tag blog-card__tag--topic"><?php echo esc_html( $t->name ); ?></span>
                            <?php endforeach; endif; ?>
                        </div>

                        <!-- Question -->
                        <h2 class="faq-card__question">
                            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                        </h2>

                        <!-- Short answer preview -->
                        <?php if ( $short ) : ?>
                            <p class="faq-card__preview"><?php echo esc_html( wp_trim_words( $short, 22 ) ); ?></p>
                        <?php endif; ?>

                    </div>

                    <div class="faq-card__footer">
                        <a href="<?php the_permalink(); ?>" class="faq-card__link">
                            Read Full Answer →
                        </a>
                    </div>
                </article>
                <?php endwhile; wp_reset_postdata(); ?>
            </div>

            <!-- Pagination -->
            <?php if ( $faq_query->max_num_pages > 1 ) :
                $base_url = get_post_type_archive_link( 'faq' );
                $params   = [];
                if ( $filter_city )    $params['faq_city']    = $filter_city;
                if ( $filter_service ) $params['faq_service'] = $filter_service;
                if ( $filter_topic )   $params['faq_topic']   = $filter_topic;
                $qs = $params ? '?' . http_build_query( $params ) : '';
            ?>
            <div class="blog-pagination">
                <?php echo paginate_links( [
                    'base'      => $base_url . 'page/%#%/' . $qs,
                    'format'    => '',
                    'current'   => $paged,
                    'total'     => $faq_query->max_num_pages,
                    'prev_text' => '← Prev',
                    'next_text' => 'Next →',
                ] ); ?>
            </div>
            <?php endif; ?>

        <?php else : ?>
            <div class="blog-no-results">
                <div class="blog-no-results__icon">🤔</div>
                <h3>No questions found</h3>
                <p>Try adjusting your filters or <a href="<?php echo esc_url( $clear_url ); ?>">view all questions</a>.</p>
            </div>
        <?php endif; ?>

    </div>
</section>

<!-- Google Reviews -->
<section class="reviews-section">
    <div class="reviews-section__inner">
        <h2 class="reviews-section__heading">What Our Clients Say</h2>
        <p class="reviews-section__sub">Real reviews from real clients on Google</p>
        <?php echo do_shortcode('[trustindex no-registration=google]'); ?>
    </div>
</section>

<!-- Lead Form -->
<?php get_template_part( 'template-parts/lead-form' ); ?>

<!-- Dropdown JS (reuse same pattern as blog) -->
<script>
(function(){
    var dropdowns = document.querySelectorAll('.faq-filters .blog-filter-dropdown');
    dropdowns.forEach(function(wrap){
        var btn  = wrap.querySelector('.blog-filter-btn');
        var menu = wrap.querySelector('.blog-filter-menu');
        if (!btn || !menu) return;
        btn.addEventListener('click', function(e){
            e.stopPropagation();
            var isOpen = !menu.hidden;
            document.querySelectorAll('.faq-filters .blog-filter-menu').forEach(function(m){ m.hidden = true; });
            document.querySelectorAll('.faq-filters .blog-filter-btn').forEach(function(b){ b.setAttribute('aria-expanded','false'); b.classList.remove('open'); });
            if (!isOpen) {
                menu.hidden = false;
                btn.setAttribute('aria-expanded','true');
                btn.classList.add('open');
            }
        });
    });
    document.addEventListener('click', function(){
        document.querySelectorAll('.faq-filters .blog-filter-menu').forEach(function(m){ m.hidden = true; });
        document.querySelectorAll('.faq-filters .blog-filter-btn').forEach(function(b){ b.setAttribute('aria-expanded','false'); b.classList.remove('open'); });
    });
})();
</script>

<?php get_footer(); ?>