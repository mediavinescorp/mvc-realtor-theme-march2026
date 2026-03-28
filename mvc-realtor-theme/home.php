<?php get_header(); ?>

<?php
// ── Filters ───────────────────────────────────────────────────────────────────
$filter_city    = isset( $_GET['city'] )    ? sanitize_text_field( $_GET['city'] )    : '';
$filter_service = isset( $_GET['service'] ) ? sanitize_text_field( $_GET['service'] ) : '';
$filter_topic   = isset( $_GET['topic'] )   ? sanitize_text_field( $_GET['topic'] )   : '';
$paged          = get_query_var( 'paged' ) ? get_query_var( 'paged' ) : 1;
$has_filters    = $filter_city || $filter_service || $filter_topic;
$clear_url      = get_permalink( get_option( 'page_for_posts' ) );

// ── Latest post (always the newest, ignoring filters) ────────────────────────
$latest_query = new WP_Query( [
    'post_type'      => 'post',
    'post_status'    => 'publish',
    'posts_per_page' => 1,
    'orderby'        => 'date',
    'order'          => 'DESC',
] );
$latest_post    = $latest_query->have_posts() ? $latest_query->posts[0] : null;
$latest_post_id = $latest_post ? $latest_post->ID : 0;

// ── Tax query ────────────────────────────────────────────────────────────────
$tax_query = [ 'relation' => 'AND' ];
if ( $filter_city )    $tax_query[] = [ 'taxonomy' => 'post_city',    'field' => 'slug', 'terms' => $filter_city ];
if ( $filter_service ) $tax_query[] = [ 'taxonomy' => 'post_service', 'field' => 'slug', 'terms' => $filter_service ];
if ( $filter_topic )   $tax_query[] = [ 'taxonomy' => 'post_topic',   'field' => 'slug', 'terms' => $filter_topic ];

// ── Grid query (exclude latest post) ─────────────────────────────────────────
$grid_args = [
    'post_type'      => 'post',
    'post_status'    => 'publish',
    'posts_per_page' => 9,
    'paged'          => $paged,
    'post__not_in'   => $latest_post_id ? [ $latest_post_id ] : [],
];
if ( count( $tax_query ) > 1 ) $grid_args['tax_query'] = $tax_query;
// if filters are active, include the latest post in grid results too
if ( $has_filters ) unset( $grid_args['post__not_in'] );

$blog_query = new WP_Query( $grid_args );

// ── Terms for dropdowns ───────────────────────────────────────────────────────
$all_cities   = get_terms( [ 'taxonomy' => 'post_city',    'hide_empty' => true ] );
$all_services = get_terms( [ 'taxonomy' => 'post_service', 'hide_empty' => true ] );
$all_topics   = get_terms( [ 'taxonomy' => 'post_topic',   'hide_empty' => true ] );

// ── Filter URL helper ─────────────────────────────────────────────────────────
function acfpi_filter_url( $key, $slug ) {
    $params = [];
    if ( isset( $_GET['city'] ) )    $params['city']    = $_GET['city'];
    if ( isset( $_GET['service'] ) ) $params['service'] = $_GET['service'];
    if ( isset( $_GET['topic'] ) )   $params['topic']   = $_GET['topic'];
    if ( $slug ) $params[ $key ] = $slug;
    else         unset( $params[ $key ] );
    $base = get_permalink( get_option( 'page_for_posts' ) );
    return $params ? $base . '?' . http_build_query( $params ) : $base;
}
?>

<!-- Blog Hero -->
<section class="blog-hero">
    <div class="blog-hero__inner">
        <h1 class="blog-hero__title">News &amp; Guides</h1>
        <p class="blog-hero__sub">Real estate insights, neighborhood guides, and market updates for Los Angeles County.</p>
    </div>
</section>

<!-- ── Featured Latest Post ─────────────────────────────────────────────────── -->
<?php if ( $latest_post && ! $has_filters ) :
    $lid     = $latest_post->ID;
    $l_title = get_the_title( $lid );
    $l_link  = get_permalink( $lid );
    $l_date  = get_the_date( 'M j, Y', $lid );
    $l_thumb = get_the_post_thumbnail_url( $lid, 'large' );
    $l_excerpt = wp_trim_words( get_the_excerpt( $lid ), 30 );
    $l_cities   = get_the_terms( $lid, 'post_city' );
    $l_services = get_the_terms( $lid, 'post_service' );
    $l_topics   = get_the_terms( $lid, 'post_topic' );
?>
<section class="blog-featured">
    <div class="blog-featured__inner">
        <div class="blog-featured__label">Latest Article</div>
        <article class="blog-featured__card">
            <?php if ( $l_thumb ) : ?>
            <a href="<?php echo esc_url( $l_link ); ?>" class="blog-featured__img-wrap">
                <img src="<?php echo esc_url( $l_thumb ); ?>"
                     alt="<?php echo esc_attr( $l_title ); ?>"
                     class="blog-featured__img">
                <div class="blog-featured__img-overlay"></div>
            </a>
            <?php endif; ?>
            <div class="blog-featured__content">
                <!-- Tags -->
                <div class="blog-card__tags">
                    <?php if ( $l_cities && ! is_wp_error( $l_cities ) ) :
                        foreach ( $l_cities as $t ) : ?>
                            <span class="blog-card__tag blog-card__tag--city"><?php echo esc_html( $t->name ); ?></span>
                    <?php endforeach; endif; ?>
                    <?php if ( $l_services && ! is_wp_error( $l_services ) ) :
                        foreach ( $l_services as $t ) : ?>
                            <span class="blog-card__tag blog-card__tag--service"><?php echo esc_html( $t->name ); ?></span>
                    <?php endforeach; endif; ?>
                    <?php if ( $l_topics && ! is_wp_error( $l_topics ) ) :
                        foreach ( $l_topics as $t ) : ?>
                            <span class="blog-card__tag blog-card__tag--topic"><?php echo esc_html( $t->name ); ?></span>
                    <?php endforeach; endif; ?>
                </div>
                <h2 class="blog-featured__title">
                    <a href="<?php echo esc_url( $l_link ); ?>"><?php echo esc_html( $l_title ); ?></a>
                </h2>
                <p class="blog-featured__excerpt"><?php echo esc_html( $l_excerpt ); ?></p>
                <div class="blog-featured__meta">
                    <span class="blog-featured__date"><?php echo esc_html( $l_date ); ?></span>
                    <a href="<?php echo esc_url( $l_link ); ?>" class="blog-featured__btn">Read Article →</a>
                </div>
            </div>
        </article>
    </div>
</section>
<?php endif; ?>

<!-- ── Filter Bar ────────────────────────────────────────────────────────────── -->
<div class="blog-filters" id="blog-filters">
    <div class="blog-filters__inner">

        <div class="blog-filters__dropdowns">

            <!-- City dropdown -->
            <?php if ( $all_cities && ! is_wp_error( $all_cities ) ) : ?>
            <div class="blog-filter-dropdown" id="filter-city-wrap">
                <button class="blog-filter-btn <?php echo $filter_city ? 'active' : ''; ?>"
                        id="filter-city-btn"
                        aria-expanded="false"
                        aria-controls="filter-city-menu">
                    <?php
                    if ( $filter_city ) {
                        $t = get_term_by( 'slug', $filter_city, 'post_city' );
                        echo esc_html( $t ? $t->name : 'City' );
                    } else {
                        echo 'City';
                    }
                    ?>
                    <svg class="blog-filter-btn__arrow" width="12" height="12" viewBox="0 0 12 12" fill="none">
                        <path d="M2 4l4 4 4-4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>
                <div class="blog-filter-menu" id="filter-city-menu" role="listbox" hidden>
                    <a href="<?php echo esc_url( acfpi_filter_url( 'city', '' ) ); ?>"
                       class="blog-filter-menu__item <?php echo ! $filter_city ? 'selected' : ''; ?>">
                        All Cities
                    </a>
                    <?php foreach ( $all_cities as $term ) : ?>
                        <a href="<?php echo esc_url( acfpi_filter_url( 'city', $term->slug ) ); ?>"
                           class="blog-filter-menu__item <?php echo $filter_city === $term->slug ? 'selected' : ''; ?>">
                            <?php echo esc_html( $term->name ); ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- Service dropdown -->
            <?php if ( $all_services && ! is_wp_error( $all_services ) ) : ?>
            <div class="blog-filter-dropdown" id="filter-service-wrap">
                <button class="blog-filter-btn <?php echo $filter_service ? 'active' : ''; ?>"
                        id="filter-service-btn"
                        aria-expanded="false"
                        aria-controls="filter-service-menu">
                    <?php
                    if ( $filter_service ) {
                        $t = get_term_by( 'slug', $filter_service, 'post_service' );
                        echo esc_html( $t ? $t->name : 'Service' );
                    } else {
                        echo 'Service';
                    }
                    ?>
                    <svg class="blog-filter-btn__arrow" width="12" height="12" viewBox="0 0 12 12" fill="none">
                        <path d="M2 4l4 4 4-4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>
                <div class="blog-filter-menu" id="filter-service-menu" role="listbox" hidden>
                    <a href="<?php echo esc_url( acfpi_filter_url( 'service', '' ) ); ?>"
                       class="blog-filter-menu__item <?php echo ! $filter_service ? 'selected' : ''; ?>">
                        All Services
                    </a>
                    <?php foreach ( $all_services as $term ) : ?>
                        <a href="<?php echo esc_url( acfpi_filter_url( 'service', $term->slug ) ); ?>"
                           class="blog-filter-menu__item <?php echo $filter_service === $term->slug ? 'selected' : ''; ?>">
                            <?php echo esc_html( $term->name ); ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- Topic dropdown -->
            <?php if ( $all_topics && ! is_wp_error( $all_topics ) ) : ?>
            <div class="blog-filter-dropdown" id="filter-topic-wrap">
                <button class="blog-filter-btn <?php echo $filter_topic ? 'active' : ''; ?>"
                        id="filter-topic-btn"
                        aria-expanded="false"
                        aria-controls="filter-topic-menu">
                    <?php
                    if ( $filter_topic ) {
                        $t = get_term_by( 'slug', $filter_topic, 'post_topic' );
                        echo esc_html( $t ? $t->name : 'Topic' );
                    } else {
                        echo 'Topic';
                    }
                    ?>
                    <svg class="blog-filter-btn__arrow" width="12" height="12" viewBox="0 0 12 12" fill="none">
                        <path d="M2 4l4 4 4-4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>
                <div class="blog-filter-menu" id="filter-topic-menu" role="listbox" hidden>
                    <a href="<?php echo esc_url( acfpi_filter_url( 'topic', '' ) ); ?>"
                       class="blog-filter-menu__item <?php echo ! $filter_topic ? 'selected' : ''; ?>">
                        All Topics
                    </a>
                    <?php foreach ( $all_topics as $term ) : ?>
                        <a href="<?php echo esc_url( acfpi_filter_url( 'topic', $term->slug ) ); ?>"
                           class="blog-filter-menu__item <?php echo $filter_topic === $term->slug ? 'selected' : ''; ?>">
                            <?php echo esc_html( $term->name ); ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- Clear filters -->
            <?php if ( $has_filters ) : ?>
            <a href="<?php echo esc_url( $clear_url ); ?>" class="blog-filter-clear-btn">
                ✕ Clear Filters
            </a>
            <?php endif; ?>

        </div>

        <!-- Active filter tags -->
        <?php if ( $has_filters ) : ?>
        <div class="blog-filter-active">
            <?php if ( $filter_city ) :
                $t = get_term_by( 'slug', $filter_city, 'post_city' ); ?>
                <span class="blog-filter-active__tag blog-filter-active__tag--city">
                    <?php echo esc_html( $t ? $t->name : $filter_city ); ?>
                    <a href="<?php echo esc_url( acfpi_filter_url( 'city', '' ) ); ?>" aria-label="Remove city filter">×</a>
                </span>
            <?php endif; ?>
            <?php if ( $filter_service ) :
                $t = get_term_by( 'slug', $filter_service, 'post_service' ); ?>
                <span class="blog-filter-active__tag blog-filter-active__tag--service">
                    <?php echo esc_html( $t ? $t->name : $filter_service ); ?>
                    <a href="<?php echo esc_url( acfpi_filter_url( 'service', '' ) ); ?>" aria-label="Remove service filter">×</a>
                </span>
            <?php endif; ?>
            <?php if ( $filter_topic ) :
                $t = get_term_by( 'slug', $filter_topic, 'post_topic' ); ?>
                <span class="blog-filter-active__tag blog-filter-active__tag--topic">
                    <?php echo esc_html( $t ? $t->name : $filter_topic ); ?>
                    <a href="<?php echo esc_url( acfpi_filter_url( 'topic', '' ) ); ?>" aria-label="Remove topic filter">×</a>
                </span>
            <?php endif; ?>
            <span class="blog-results-count">
                <?php $total = $blog_query->found_posts;
                echo $total . ' ' . ( $total === 1 ? 'result' : 'results' ); ?>
            </span>
        </div>
        <?php endif; ?>

    </div>
</div>

<!-- ── Posts Grid ─────────────────────────────────────────────────────────────── -->
<section class="blog-archive">
    <div class="blog-archive__inner">
        <?php if ( $blog_query->have_posts() ) : ?>
            <div class="blog-archive__grid">
                <?php while ( $blog_query->have_posts() ) : $blog_query->the_post();
                    $card_cities   = get_the_terms( get_the_ID(), 'post_city' );
                    $card_services = get_the_terms( get_the_ID(), 'post_service' );
                    $card_topics   = get_the_terms( get_the_ID(), 'post_topic' );
                ?>
                <article class="blog-card">
                    <?php if ( has_post_thumbnail() ) : ?>
                    <a href="<?php the_permalink(); ?>" class="blog-card__img-wrap">
                        <?php the_post_thumbnail( 'medium_large', [ 'class' => 'blog-card__img' ] ); ?>
                    </a>
                    <?php endif; ?>
                    <div class="blog-card__body">
                        <div class="blog-card__tags">
                            <?php if ( $card_cities && ! is_wp_error( $card_cities ) ) :
                                foreach ( $card_cities as $t ) : ?>
                                    <span class="blog-card__tag blog-card__tag--city"><?php echo esc_html( $t->name ); ?></span>
                            <?php endforeach; endif; ?>
                            <?php if ( $card_services && ! is_wp_error( $card_services ) ) :
                                foreach ( $card_services as $t ) : ?>
                                    <span class="blog-card__tag blog-card__tag--service"><?php echo esc_html( $t->name ); ?></span>
                            <?php endforeach; endif; ?>
                            <?php if ( $card_topics && ! is_wp_error( $card_topics ) ) :
                                foreach ( $card_topics as $t ) : ?>
                                    <span class="blog-card__tag blog-card__tag--topic"><?php echo esc_html( $t->name ); ?></span>
                            <?php endforeach; endif; ?>
                        </div>
                        <h2 class="blog-card__title">
                            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                        </h2>
                        <p class="blog-card__excerpt"><?php echo wp_trim_words( get_the_excerpt(), 18 ); ?></p>
                        <div class="blog-card__meta">
                            <span class="blog-card__date"><?php echo get_the_date( 'M j, Y' ); ?></span>
                            <a href="<?php the_permalink(); ?>" class="blog-card__read-more">Read More →</a>
                        </div>
                    </div>
                </article>
                <?php endwhile; wp_reset_postdata(); ?>
            </div>

            <!-- Pagination -->
            <?php if ( $blog_query->max_num_pages > 1 ) :
                $base_url    = get_permalink( get_option( 'page_for_posts' ) );
                $params      = [];
                if ( $filter_city )    $params['city']    = $filter_city;
                if ( $filter_service ) $params['service'] = $filter_service;
                if ( $filter_topic )   $params['topic']   = $filter_topic;
                $qs = $params ? '?' . http_build_query( $params ) : '';
            ?>
            <div class="blog-pagination">
                <?php echo paginate_links( [
                    'base'      => $base_url . 'page/%#%/' . $qs,
                    'format'    => '',
                    'current'   => $paged,
                    'total'     => $blog_query->max_num_pages,
                    'prev_text' => '← Prev',
                    'next_text' => 'Next →',
                ] ); ?>
            </div>
            <?php endif; ?>

        <?php else : ?>
            <div class="blog-no-results">
                <div class="blog-no-results__icon">🔍</div>
                <h3>No articles found</h3>
                <p>Try adjusting your filters or <a href="<?php echo esc_url( $clear_url ); ?>">clear all filters</a>.</p>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Dropdown JS -->
<script>
(function(){
    var dropdowns = document.querySelectorAll('.blog-filter-dropdown');
    dropdowns.forEach(function(wrap){
        var btn  = wrap.querySelector('.blog-filter-btn');
        var menu = wrap.querySelector('.blog-filter-menu');
        if (!btn || !menu) return;

        btn.addEventListener('click', function(e){
            e.stopPropagation();
            var isOpen = !menu.hidden;
            // close all
            document.querySelectorAll('.blog-filter-menu').forEach(function(m){ m.hidden = true; });
            document.querySelectorAll('.blog-filter-btn').forEach(function(b){ b.setAttribute('aria-expanded','false'); b.classList.remove('open'); });
            // toggle this one
            if (!isOpen) {
                menu.hidden = false;
                btn.setAttribute('aria-expanded','true');
                btn.classList.add('open');
            }
        });
    });

    // close on outside click
    document.addEventListener('click', function(){
        document.querySelectorAll('.blog-filter-menu').forEach(function(m){ m.hidden = true; });
        document.querySelectorAll('.blog-filter-btn').forEach(function(b){ b.setAttribute('aria-expanded','false'); b.classList.remove('open'); });
    });
})();
</script>

<?php get_footer(); ?>