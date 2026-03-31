<?php

// Enable ACF Options Page
if ( function_exists( 'acf_add_options_page' ) ) {
    acf_add_options_page( array(
        'page_title' => 'Site Settings',
        'menu_title' => 'Site Settings',
        'menu_slug'  => 'site-settings',
        'capability' => 'edit_posts',
        'redirect'   => false,
    ) );
}

// ============================================================
// ENQUEUE STYLES
// ============================================================

add_action( 'wp_enqueue_scripts', 'blayne_child_enqueue_styles' );
function blayne_child_enqueue_styles() {

    wp_enqueue_style(
        'parent-style',
        get_template_directory_uri() . '/style.css'
    );

    wp_enqueue_style(
        'blayne-fonts',
        'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700&display=swap',
        array(),
        null
    );

    wp_enqueue_style(
        'blayne-global',
        get_stylesheet_directory_uri() . '/css/global.css',
        array( 'parent-style' ),
        filemtime( get_stylesheet_directory() . '/css/global.css' )
    );

    wp_enqueue_style(
        'blayne-typography',
        get_stylesheet_directory_uri() . '/css/typography.css',
        array( 'blayne-global' ),
        filemtime( get_stylesheet_directory() . '/css/typography.css' )
    );

wp_enqueue_style( 'dashicons' );

// components.css — header, nav, footer
    wp_enqueue_style(
        'blayne-components',
        get_stylesheet_directory_uri() . '/css/components.css',
        array( 'blayne-global' ),
        filemtime( get_stylesheet_directory() . '/css/components.css' )
    );

// Splide.js CSS — carousel
    wp_enqueue_style(
        'splide-css',
        'https://cdn.jsdelivr.net/npm/@splidejs/splide@4.1.4/dist/css/splide.min.css',
        array(),
        null
    );

}

// ============================================================
// ENQUEUE SCRIPTS
// ============================================================

add_action( 'wp_enqueue_scripts', 'blayne_child_enqueue_scripts' );
function blayne_child_enqueue_scripts() {

    // Splide.js — carousel library (must register before nav.js)
    wp_enqueue_script(
        'splide-js',
        'https://cdn.jsdelivr.net/npm/@splidejs/splide@4.1.4/dist/js/splide.min.js',
        array(),
        null,
        true
    );

    wp_enqueue_script(
        'blayne-nav',
        get_stylesheet_directory_uri() . '/js/nav.js',
        array( 'splide-js' ),
        filemtime( get_stylesheet_directory() . '/js/nav.js' ),
        true
    );

}
// ============================================================
// AUTO TABLE OF CONTENTS
// ============================================================

add_filter( 'the_content', 'blayne_auto_toc' );
function blayne_auto_toc( $content ) {

    if ( ! is_single() ) {
        return $content;
    }

    preg_match_all( '/<h([23])[^>]*>(.*?)<\/h[23]>/is', $content, $matches, PREG_SET_ORDER );

    if ( count( $matches ) < 3 ) {
        return $content;
    }

    $toc_items   = array();
    $new_content = $content;

    foreach ( $matches as $match ) {
        $level       = $match[1];
        $text        = wp_strip_all_tags( $match[2] );
        $id          = sanitize_title( $text );
        $old_heading = $match[0];
        $new_heading = '<h' . $level . ' id="' . esc_attr( $id ) . '">' . $match[2] . '</h' . $level . '>';
        $new_content = str_replace( $old_heading, $new_heading, $new_content );
        $toc_items[] = array(
            'level' => $level,
            'text'  => $text,
            'id'    => $id,
        );
    }

    $toc  = '<div class="toc" id="toc">';
    $toc .= '<div class="toc__header">';
    $toc .= '<span class="toc__title">Table of Contents</span>';
    $toc .= '<button class="toc__toggle" aria-expanded="true" onclick="this.setAttribute(\'aria-expanded\',this.getAttribute(\'aria-expanded\')==\'true\'?\'false\':\'true\');document.getElementById(\'toc-list\').classList.toggle(\'toc__list--hidden\')">−</button>';
    $toc .= '</div>';
  $toc .= '<ul class="toc__list" id="toc-list">';

    foreach ( $toc_items as $item ) {
        if ( $item['level'] === '3' ) {
            $toc .= '<li class="toc__item toc__item--sub">';
            $toc .= '<a href="#' . esc_attr( $item['id'] ) . '" class="toc__link toc__link--sub">' . esc_html( $item['text'] ) . '</a>';
            $toc .= '</li>';
        } else {
            $toc .= '<li class="toc__item">';
            $toc .= '<a href="#' . esc_attr( $item['id'] ) . '" class="toc__link">' . esc_html( $item['text'] ) . '</a>';
            $toc .= '</li>';
        }
    }

    $toc .= '</ul>';
    $toc .= '</div>';

    $pos = strpos( $new_content, '</p>' );
    if ( $pos !== false ) {
        $new_content = substr_replace( $new_content, '</p>' . $toc, $pos, strlen( '</p>' ) );
    } else {
        $new_content = $toc . $new_content;
    }

    return $new_content;
}

// ── Blog Post Taxonomies ─────────────────────────────────────────────────────
add_action( 'init', function () {

    // Post City
    register_taxonomy( 'post_city', [ 'post', 'faq', 'city', 'service', 'landing_page', 'neighborhood' ], [
        'labels' => [
            'name'              => 'Post Cities',
            'singular_name'     => 'Post City',
            'search_items'      => 'Search Cities',
            'all_items'         => 'All Cities',
            'edit_item'         => 'Edit City',
            'add_new_item'      => 'Add New City',
            'menu_name'         => 'Post Cities',
        ],
        'hierarchical'      => true,
        'show_ui'           => true,
        'show_in_rest'      => true,
        'show_admin_column' => true,
        'rewrite'           => [ 'slug' => 'post-city' ],
    ] );

    // Post Service
    register_taxonomy( 'post_service', [ 'post', 'faq', 'service', 'landing_page' ], [
        'labels' => [
            'name'              => 'Post Services',
            'singular_name'     => 'Post Service',
            'search_items'      => 'Search Services',
            'all_items'         => 'All Services',
            'edit_item'         => 'Edit Service',
            'add_new_item'      => 'Add New Service',
            'menu_name'         => 'Post Services',
        ],
        'hierarchical'      => true,
        'show_ui'           => true,
        'show_in_rest'      => true,
        'show_admin_column' => true,
        'rewrite'           => [ 'slug' => 'post-service' ],
    ] );

    // Post Topic
    register_taxonomy( 'post_topic', [ 'post', 'faq' ], [
        'labels' => [
            'name'              => 'Post Topics',
            'singular_name'     => 'Post Topic',
            'search_items'      => 'Search Topics',
            'all_items'         => 'All Topics',
            'edit_item'         => 'Edit Topic',
            'add_new_item'      => 'Add New Topic',
            'menu_name'         => 'Post Topics',
        ],
        'hierarchical'      => false,
        'show_ui'           => true,
        'show_in_rest'      => true,
        'show_admin_column' => true,
        'rewrite'           => [ 'slug' => 'post-topic' ],
    ] );

} );


// ── Flush rewrite rules when FAQ archive is enabled ──────────────────────────
add_action( 'init', function() {
    // only needed once after enabling has_archive — remove after first load
}, 999 );


// ── Allow taxonomy query vars on FAQ archive ──────────────────────────────
add_filter( 'query_vars', function( $vars ) {
    $vars[] = 'faq_city';
    $vars[] = 'faq_service';
    $vars[] = 'faq_topic';
    return $vars;
} );


// ── Render FAQ section with optional taxonomy filter ─────────────────────────
function blayne_faq_section( $args = [] ) {
    $defaults = [
        'city_slug'    => '',
        'service_slug' => '',
        'limit'        => 8,
        'heading'      => 'Frequently Asked Questions',
        // legacy support
        'taxonomy'     => '',
        'term_slug'    => '',
    ];
    $args = wp_parse_args( $args, $defaults );

    // ── Legacy single-taxonomy calls still work ───────────────────────────────
    if ( $args['taxonomy'] && $args['term_slug'] && ! $args['city_slug'] && ! $args['service_slug'] ) {
        $tax_map = [
            'post_city'    => 'city_slug',
            'post_service' => 'service_slug',
        ];
        if ( isset( $tax_map[ $args['taxonomy'] ] ) ) {
            $args[ $tax_map[ $args['taxonomy'] ] ] = $args['term_slug'];
        }
    }

    $city_slug    = $args['city_slug'];
    $service_slug = $args['service_slug'];

    // ── Build query attempts in priority order ────────────────────────────────
    $attempts = [];

    // 1. Both city + service (most specific)
    if ( $city_slug && $service_slug ) {
        $attempts[] = [
            'relation' => 'AND',
            [ 'taxonomy' => 'post_city',    'field' => 'slug', 'terms' => $city_slug ],
            [ 'taxonomy' => 'post_service', 'field' => 'slug', 'terms' => $service_slug ],
        ];
    }

    // 2. Service only fallback
    if ( $service_slug ) {
        $attempts[] = [
            [ 'taxonomy' => 'post_service', 'field' => 'slug', 'terms' => $service_slug ],
        ];
    }

    // 3. City only fallback
    if ( $city_slug ) {
        $attempts[] = [
            [ 'taxonomy' => 'post_city', 'field' => 'slug', 'terms' => $city_slug ],
        ];
    }

    // 4. No filter — return anything
    $attempts[] = null;

    // ── Run attempts until we get at least 3 results ──────────────────────────
    $faq_q = null;
    foreach ( $attempts as $tax_query ) {
        $query_args = [
            'post_type'      => 'faq',
            'post_status'    => 'publish',
            'posts_per_page' => $args['limit'],
            'orderby'        => [ 'meta_value_num' => 'ASC', 'title' => 'ASC' ],
            'meta_key'       => 'sort_order',
        ];
        if ( $tax_query ) {
            $query_args['tax_query'] = $tax_query;
        }
        $test_q = new WP_Query( $query_args );
        if ( $test_q->post_count >= 3 ) {
            $faq_q = $test_q;
            break;
        }
    }

    if ( ! $faq_q || ! $faq_q->have_posts() ) return;

    // ── FAQPage Schema ────────────────────────────────────────────────────────
    $schema_entities = [];
    foreach ( $faq_q->posts as $faq_post ) {
        $q = $faq_post->post_title;
        $a = get_field( 'short_answer', $faq_post->ID ) ?: get_field( 'long_answer', $faq_post->ID );
        if ( $q && $a ) {
            $schema_entities[] = [
                '@type'          => 'Question',
                'name'           => $q,
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text'  => wp_strip_all_tags( is_array( $a ) ? implode( ' ', $a ) : $a ),
                ],
            ];
        }
    }
    if ( $schema_entities ) {
        $faq_schema = [
            '@context'   => 'https://schema.org',
            '@type'      => 'FAQPage',
            'mainEntity' => $schema_entities,
        ];
        echo '<script type="application/ld+json">'
            . wp_json_encode( $faq_schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES )
            . '</script>' . "\n";
    }

    $archive_url = get_post_type_archive_link( 'faq' );
    $section_id  = 'faq-section-' . uniqid();
    ?>

    <section class="page-faqs">
        <div class="page-faqs__inner">

            <div class="page-faqs__header">
                <h2 class="page-faqs__heading"><?php echo esc_html( $args['heading'] ); ?></h2>
                <a href="<?php echo esc_url( $archive_url ); ?>" class="page-faqs__all">
                    View All FAQs →
                </a>
            </div>

            <div class="page-faqs__accordion" id="<?php echo esc_attr( $section_id ); ?>">
                <?php $i = 0; while ( $faq_q->have_posts() ) : $faq_q->the_post();
                    $short   = get_field( 'short_answer' );
                    $item_id = 'faq-item-' . get_the_ID();
                    $is_first = $i === 0;
                ?>
                <div class="page-faq-item <?php echo $is_first ? 'page-faq-item--open' : ''; ?>">
                    <button class="page-faq-trigger"
                            aria-expanded="<?php echo $is_first ? 'true' : 'false'; ?>"
                            aria-controls="<?php echo $item_id; ?>">
                        <span class="page-faq-trigger__text"><?php the_title(); ?></span>
                        <span class="page-faq-trigger__icon" aria-hidden="true"></span>
                    </button>
                    <div class="page-faq-panel"
                         id="<?php echo $item_id; ?>"
                         <?php echo $is_first ? '' : 'hidden'; ?>>
                        <?php if ( $short ) : ?>
                            <p class="page-faq-panel__answer"><?php echo esc_html( $short ); ?></p>
                        <?php endif; ?>
                        <a href="<?php the_permalink(); ?>" class="page-faq-panel__link">
                            Learn More →
                        </a>
                    </div>
                </div>
                <?php $i++; endwhile; wp_reset_postdata(); ?>
            </div>

        </div>
    </section>

    <script>
    (function(){
        var acc = document.getElementById('<?php echo esc_js( $section_id ); ?>');
        if (!acc) return;
        acc.querySelectorAll('.page-faq-trigger').forEach(function(btn){
            btn.addEventListener('click', function(){
                var panel  = document.getElementById(btn.getAttribute('aria-controls'));
                var item   = btn.closest('.page-faq-item');
                var isOpen = btn.getAttribute('aria-expanded') === 'true';
                acc.querySelectorAll('.page-faq-trigger').forEach(function(b){
                    b.setAttribute('aria-expanded','false');
                    b.closest('.page-faq-item').classList.remove('page-faq-item--open');
                    var p = document.getElementById(b.getAttribute('aria-controls'));
                    if(p) p.hidden = true;
                });
                if(!isOpen){
                    btn.setAttribute('aria-expanded','true');
                    item.classList.add('page-faq-item--open');
                    panel.hidden = false;
                }
            });
        });
    })();
    </script>
    <?php
}


// ── Schema Markup ─────────────────────────────────────────────────────────────
add_action( 'wp_head', function () {

    // ── Pull Site Settings ──────────────────────────────────────────────────
    $phone       = get_field( 'phone_number',      'option' );
    $email       = get_field( 'email_address',     'option' );
    $address     = get_field( 'office_address',    'option' );
    $license     = get_field( 'license_number',    'option' );
    $photo       = get_field( 'blayne_photo',      'option' );
    $logo        = get_field( 'site_logo',         'option' );
    $place_id    = get_field( 'google_place_id',   'option' );
    $years_exp   = get_field( 'years_of_experience', 'option' );
    $cities_num  = get_field( 'number_of_cities',  'option' );
    $site_url    = home_url('/');
    $site_name   = get_bloginfo('name');

    $photo_url   = $photo ? $photo['url'] : '';
    $logo_url    = $logo  ? $logo['url']  : '';

    // ── 1. Person + RealEstateAgent (every page) ────────────────────────────
    $person_schema = [
        '@context' => 'https://schema.org',
        '@type'    => [ 'Person', 'RealEstateAgent' ],
        '@id'      => $site_url . '#blayne-pacelli',
        'name'     => 'Blayne Pacelli',
        'jobTitle' => 'Realtor',
        'worksFor' => [
            '@type' => 'Organization',
            'name'  => 'Rodeo Realty',
        ],
        'url'         => $site_url,
        'telephone'   => $phone,
        'email'       => $email,
        'image'       => $photo_url,
        'description' => 'Blayne Pacelli is a top-rated Los Angeles realtor with Rodeo Realty, specializing in buying, selling, and luxury homes across Greater Los Angeles County.',
        'areaServed'  => [
            '@type' => 'AdministrativeArea',
            'name'  => 'Los Angeles County, California',
        ],
        'knowsAbout' => [
            'Buying a Home',
            'Selling a Home',
            'Luxury Real Estate',
            'Investment Properties',
            'First-Time Home Buyers',
            'Los Angeles Real Estate',
        ],
        'hasCredential' => $license ? [
            '@type' => 'EducationalOccupationalCredential',
            'name'  => 'California Real Estate License',
            'credentialCategory' => 'Real Estate License',
            'identifier' => $license,
        ] : null,
        'sameAs' => array_filter( [
            get_field( 'facebook_url',  'option' ),
            get_field( 'instagram_url', 'option' ),
            get_field( 'linkedin_url',  'option' ),
            get_field( 'youtube_url',   'option' ),
        ] ),
    ];

    // remove null values
    $person_schema = array_filter( $person_schema );

    echo '<script type="application/ld+json">'
        . wp_json_encode( $person_schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT )
        . '</script>' . "\n";

    // ── 2. LocalBusiness (every page) ───────────────────────────────────────
    $business_schema = [
        '@context'        => 'https://schema.org',
        '@type'           => [ 'LocalBusiness', 'RealEstateAgent' ],
        '@id'             => $site_url . '#business',
        'name'            => 'Blayne Pacelli — Rodeo Realty',
        'url'             => $site_url,
        'telephone'       => $phone,
        'email'           => $email,
        'image'           => $logo_url ?: $photo_url,
        'priceRange'      => '$$$',
        'currenciesAccepted' => 'USD',
        'paymentAccepted' => 'Cash, Check, Wire Transfer',
        'description'     => 'Top-rated Los Angeles real estate agent serving buyers and sellers across 30+ cities in Greater Los Angeles County.',
        'areaServed'      => [
            '@type' => 'AdministrativeArea',
            'name'  => 'Los Angeles County, California',
        ],
        'founder' => [
            '@id' => $site_url . '#blayne-pacelli',
        ],
        'sameAs' => array_filter( [
            get_field( 'facebook_url',  'option' ),
            get_field( 'instagram_url', 'option' ),
            get_field( 'linkedin_url',  'option' ),
            get_field( 'youtube_url',   'option' ),
            $place_id ? 'https://maps.google.com/?cid=' . $place_id : null,
        ] ),
    ];

    if ( $address ) {
        $business_schema['address'] = [
            '@type'           => 'PostalAddress',
            'streetAddress'   => $address,
            'addressLocality' => 'Studio City',
            'addressRegion'   => 'CA',
            'postalCode'      => '91604',
            'addressCountry'  => 'US',
        ];
    }

    $business_schema = array_filter( $business_schema );

    echo '<script type="application/ld+json">'
        . wp_json_encode( $business_schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT )
        . '</script>' . "\n";

    // ── 3. Article schema (single blog posts only) ───────────────────────────
    if ( is_single() && get_post_type() === 'post' ) {
        $article_schema = [
            '@context'         => 'https://schema.org',
            '@type'            => 'Article',
            'headline'         => get_the_title(),
            'description'      => get_the_excerpt(),
            'url'              => get_permalink(),
            'datePublished'    => get_the_date( 'c' ),
            'dateModified'     => get_the_modified_date( 'c' ),
            'author'           => [
                '@id' => $site_url . '#blayne-pacelli',
            ],
            'publisher'        => [
                '@id' => $site_url . '#business',
            ],
            'image'            => get_the_post_thumbnail_url( get_the_ID(), 'large' ) ?: $photo_url,
            'mainEntityOfPage' => [
                '@type' => 'WebPage',
                '@id'   => get_permalink(),
            ],
        ];

        // add taxonomy terms as keywords
        $city_terms    = get_the_terms( get_the_ID(), 'post_city' );
        $service_terms = get_the_terms( get_the_ID(), 'post_service' );
        $topic_terms   = get_the_terms( get_the_ID(), 'post_topic' );
        $keywords      = [];
        if ( $city_terms    && ! is_wp_error( $city_terms ) )    $keywords = array_merge( $keywords, wp_list_pluck( $city_terms,    'name' ) );
        if ( $service_terms && ! is_wp_error( $service_terms ) ) $keywords = array_merge( $keywords, wp_list_pluck( $service_terms, 'name' ) );
        if ( $topic_terms   && ! is_wp_error( $topic_terms ) )   $keywords = array_merge( $keywords, wp_list_pluck( $topic_terms,   'name' ) );
        if ( $keywords ) $article_schema['keywords'] = implode( ', ', $keywords );

        echo '<script type="application/ld+json">'
            . wp_json_encode( $article_schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT )
            . '</script>' . "\n";
    }

    // ── 4. BreadcrumbList (city pages) ──────────────────────────────────────
    if ( is_singular( 'city' ) ) {
        $breadcrumb_schema = [
            '@context'        => 'https://schema.org',
            '@type'           => 'BreadcrumbList',
            'itemListElement' => [
                [
                    '@type'    => 'ListItem',
                    'position' => 1,
                    'name'     => 'Home',
                    'item'     => $site_url,
                ],
                [
                    '@type'    => 'ListItem',
                    'position' => 2,
                    'name'     => 'Research Neighborhoods',
                    'item'     => get_post_type_archive_link( 'city' ),
                ],
                [
                    '@type'    => 'ListItem',
                    'position' => 3,
                    'name'     => get_the_title(),
                    'item'     => get_permalink(),
                ],
            ],
        ];

        echo '<script type="application/ld+json">'
            . wp_json_encode( $breadcrumb_schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT )
            . '</script>' . "\n";
    }

    // ── 5. BreadcrumbList (FAQ single) ──────────────────────────────────────
    if ( is_singular( 'faq' ) ) {
        $breadcrumb_schema = [
            '@context'        => 'https://schema.org',
            '@type'           => 'BreadcrumbList',
            'itemListElement' => [
                [
                    '@type'    => 'ListItem',
                    'position' => 1,
                    'name'     => 'Home',
                    'item'     => $site_url,
                ],
                [
                    '@type'    => 'ListItem',
                    'position' => 2,
                    'name'     => 'FAQs',
                    'item'     => get_post_type_archive_link( 'faq' ),
                ],
                [
                    '@type'    => 'ListItem',
                    'position' => 3,
                    'name'     => get_the_title(),
                    'item'     => get_permalink(),
                ],
            ],
        ];

        echo '<script type="application/ld+json">'
            . wp_json_encode( $breadcrumb_schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT )
            . '</script>' . "\n";
    }

    // ── 6. BreadcrumbList (service pages) ───────────────────────────────────
    if ( is_singular( 'service' ) ) {
        $breadcrumb_schema = [
            '@context'        => 'https://schema.org',
            '@type'           => 'BreadcrumbList',
            'itemListElement' => [
                [
                    '@type'    => 'ListItem',
                    'position' => 1,
                    'name'     => 'Home',
                    'item'     => $site_url,
                ],
                [
                    '@type'    => 'ListItem',
                    'position' => 2,
                    'name'     => 'Services',
                    'item'     => $site_url . 'services/',
                ],
                [
                    '@type'    => 'ListItem',
                    'position' => 3,
                    'name'     => get_the_title(),
                    'item'     => get_permalink(),
                ],
            ],
        ];

        echo '<script type="application/ld+json">'
            . wp_json_encode( $breadcrumb_schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT )
            . '</script>' . "\n";
    }

    // ── 7. City-specific RealEstateAgent schema (city pages) ────────────────
    if ( is_singular( 'city' ) ) {
        $city_name   = get_the_title();
        $city_schema = [
            '@context'    => 'https://schema.org',
            '@type'       => 'RealEstateAgent',
            'name'        => 'Blayne Pacelli — ' . $city_name . ' Realtor',
            'url'         => get_permalink(),
            'telephone'   => $phone,
            'image'       => $photo_url,
            'description' => 'Blayne Pacelli is the #1 realtor in ' . $city_name . ', CA, helping buyers and sellers navigate the local real estate market with expert guidance.',
            'areaServed'  => [
                '@type' => 'City',
                'name'  => $city_name . ', California',
            ],
            'employee' => [
                '@id' => $site_url . '#blayne-pacelli',
            ],
        ];

        if ( $address ) {
            $city_schema['address'] = [
                '@type'           => 'PostalAddress',
                'streetAddress'   => $address,
                'addressLocality' => 'Studio City',
                'addressRegion'   => 'CA',
                'postalCode'      => '91604',
                'addressCountry'  => 'US',
            ];
        }

        echo '<script type="application/ld+json">'
            . wp_json_encode( $city_schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT )
            . '</script>' . "\n";
    }




    // ── 8. Landing Page schema ───────────────────────────────────────────────
    if ( is_singular( 'landing_page' ) ) {

        $city_name    = get_field( 'lp_city_name' );
        $service_name = get_field( 'lp_service_name' );
        $service_slug = get_field( 'lp_service_slug' );
        $page_url     = get_permalink();

        // ── 8a. City-specific LocalBusiness ─────────────────────────────────
        $lp_local = [
            '@context'    => 'https://schema.org',
            '@type'       => [ 'LocalBusiness', 'RealEstateAgent' ],
            'name'        => 'Blayne Pacelli — ' . $city_name . ' ' . $service_name,
            'url'         => $page_url,
            'telephone'   => $phone,
            'email'       => $email,
            'image'       => $photo_url,
            'description' => 'Blayne Pacelli is a top-rated Los Angeles realtor helping clients with ' . $service_name . ' in ' . $city_name . ', CA.',
            'areaServed'  => [
                '@type' => 'City',
                'name'  => $city_name . ', California',
            ],
            'employee' => [
                '@id' => $site_url . '#blayne-pacelli',
            ],
        ];

        if ( $address ) {
            $lp_local['address'] = [
                '@type'           => 'PostalAddress',
                'streetAddress'   => $address,
                'addressLocality' => 'Studio City',
                'addressRegion'   => 'CA',
                'postalCode'      => '91604',
                'addressCountry'  => 'US',
            ];
        }

        echo '<script type="application/ld+json">'
            . wp_json_encode( $lp_local, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT )
            . '</script>' . "\n";

        // ── 8b. BreadcrumbList ───────────────────────────────────────────────
        $breadcrumb_schema = [
            '@context'        => 'https://schema.org',
            '@type'           => 'BreadcrumbList',
            'itemListElement' => [
                [
                    '@type'    => 'ListItem',
                    'position' => 1,
                    'name'     => 'Home',
                    'item'     => $site_url,
                ],
                [
                    '@type'    => 'ListItem',
                    'position' => 2,
                    'name'     => $service_name,
                    'item'     => $site_url . 'services/' . $service_slug . '/',
                ],
                [
                    '@type'    => 'ListItem',
                    'position' => 3,
                    'name'     => $city_name,
                    'item'     => $page_url,
                ],
            ],
        ];

        echo '<script type="application/ld+json">'
            . wp_json_encode( $breadcrumb_schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT )
            . '</script>' . "\n";

        // ── 8c. RealEstateListing — one block per featured listing ───────────
        $listings = get_field( 'lp_featured_listings' );
        if ( $listings ) {
            foreach ( $listings as $listing ) {
                if ( empty( $listing['lp_listing_address'] ) ) continue;
                $listing_schema = [
                    '@context'      => 'https://schema.org',
                    '@type'         => 'RealEstateListing',
                    'name'          => $listing['lp_listing_address'],
                    'url'           => $listing['lp_listing_url'] ?? $page_url,
                    'description'   => $listing['lp_listing_description'] ?? '',
                    'numberOfRooms' => $listing['lp_listing_beds'] ?? '',
                    'floorSize'     => $listing['lp_listing_sqft'] ? [
                        '@type' => 'QuantitativeValue',
                        'value' => preg_replace( '/[^0-9]/', '', $listing['lp_listing_sqft'] ),
                        'unitCode' => 'FTK',
                    ] : null,
                    'offers' => [
                        '@type'         => 'Offer',
                        'price'         => preg_replace( '/[^0-9]/', '', $listing['lp_listing_price'] ?? '' ),
                        'priceCurrency' => 'USD',
                    ],
                    'address' => [
                        '@type'           => 'PostalAddress',
                        'streetAddress'   => $listing['lp_listing_address'],
                        'addressLocality' => $city_name,
                        'addressRegion'   => 'CA',
                        'addressCountry'  => 'US',
                    ],
                ];

                // remove null values
                $listing_schema = array_filter( $listing_schema );

                echo '<script type="application/ld+json">'
                    . wp_json_encode( $listing_schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT )
                    . '</script>' . "\n";
            }
        }

        // ── 8d. areaServed expansion on global LocalBusiness ────────────────
        // Note: the global LocalBusiness schema already outputs on every page.
        // The city-specific block above handles per-page entity specificity.
        // No duplicate needed here.

    }





}, 5 );


// ── Meta Descriptions & Open Graph Tags ──────────────────────────────────────
add_action( 'wp_head', function () {

    $site_name  = get_bloginfo( 'name' );
    $site_url   = home_url( '/' );
    $logo       = get_field( 'site_logo', 'option' );
    $logo_url   = $logo ? $logo['url'] : '';
    $phone      = get_field( 'phone_number', 'option' );

    // ── Determine meta description ───────────────────────────────────────────
    $meta_desc  = '';
    $og_title   = '';
    $og_image   = $logo_url;
    $og_type    = 'website';

    if ( is_singular( 'city' ) ) {
        $meta_desc = get_field( 'city_intro' ) ?: 'Blayne Pacelli is the #1 realtor in ' . get_the_title() . ', CA. Expert guidance for buyers and sellers. Call ' . $phone . ' for a free consultation.';
        $og_title  = get_the_title() . ' Realtor — Blayne Pacelli | Rodeo Realty';
        $hero      = get_field( 'hero_image' );
        $og_image  = $hero ? $hero['url'] : $logo_url;
        $og_type   = 'article';

    } elseif ( is_singular( 'faq' ) ) {
        $meta_desc = get_field( 'meta_description' ) ?: get_field( 'short_answer' ) ?: get_the_excerpt();
        $og_title  = get_the_title() . ' — Los Angeles Real Estate FAQ';
        $og_type   = 'article';




    } elseif ( is_singular( 'landing_page' ) ) {
        $lp_city     = get_field( 'lp_city_name' );
        $lp_service  = get_field( 'lp_service_name' );
        $lp_meta     = get_field( 'lp_meta_description' );
        $lp_title    = get_field( 'lp_meta_title' );
        $lp_sub      = get_field( 'lp_page_subheadline' );
        $hero        = get_field( 'lp_hero_image' );

        $meta_desc = $lp_meta
            ?: $lp_sub
            ?: 'Blayne Pacelli helps clients with ' . $lp_service . ' in ' . $lp_city . ', CA. Expert local guidance from a top-rated Rodeo Realty agent. Free consultation.';

        $og_title  = $lp_title
            ?: $lp_service . ' in ' . $lp_city . ' | Blayne Pacelli — Realtor';

        $og_image  = $hero ? $hero['url'] : $logo_url;
        $og_type   = 'article';





    } elseif ( is_singular( 'service' ) ) {
        $meta_desc = get_field( 'meta_description' ) ?: get_field( 'service_intro' ) ?: 'Blayne Pacelli provides expert ' . get_the_title() . ' services across Greater Los Angeles. Call ' . $phone . ' for a free consultation.';
        $og_title  = get_the_title() . ' in Los Angeles — Blayne Pacelli | Rodeo Realty';
        $hero      = get_field( 'hero_image' );
        $og_image  = $hero ? $hero['url'] : $logo_url;
        $og_type   = 'article';

    } elseif ( is_singular( 'post' ) ) {
        $meta_desc = get_the_excerpt();
        $og_title  = get_the_title() . ' — ' . $site_name;
        $og_image  = get_the_post_thumbnail_url( get_the_ID(), 'large' ) ?: $logo_url;
        $og_type   = 'article';

    } elseif ( is_home() ) {
        $meta_desc = 'Los Angeles real estate news, neighborhood guides, and home buying tips from Blayne Pacelli of Rodeo Realty.';
        $og_title  = 'News & Guides — ' . $site_name;

    } elseif ( is_post_type_archive( 'faq' ) ) {
        $meta_desc = 'Answers to the most common questions about buying, selling, and investing in Los Angeles real estate — from Blayne Pacelli of Rodeo Realty.';
        $og_title  = 'Real Estate FAQs — ' . $site_name;

    } elseif ( is_post_type_archive( 'city' ) ) {
        $meta_desc = 'Browse all 30 cities served by Blayne Pacelli across Greater Los Angeles County. Find your perfect neighborhood today.';
        $og_title  = 'Research Neighborhoods — ' . $site_name;

    } elseif ( is_page() ) {
        $meta_desc = get_field( 'meta_description' ) ?: get_the_excerpt() ?: 'Blayne Pacelli — Top rated Los Angeles realtor with Rodeo Realty. Serving buyers and sellers across 30+ cities in Greater Los Angeles.';
        $og_title  = get_the_title() . ' — ' . $site_name;

    } elseif ( is_front_page() ) {
        $meta_desc = 'Blayne Pacelli is the #1 realtor in Greater Los Angeles. Expert guidance for buyers and sellers across 30+ cities. Call ' . $phone . ' for a free consultation.';
        $og_title  = $site_name . ' — #1 Los Angeles Realtor | Rodeo Realty';
    }

    // fallback
    if ( ! $meta_desc ) {
        $meta_desc = 'Blayne Pacelli — Top rated Los Angeles realtor with Rodeo Realty. Serving buyers and sellers across 30+ cities in Greater Los Angeles.';
    }
    if ( ! $og_title ) {
        $og_title = $site_name;
    }

    $og_url = ( is_singular() || is_archive() || is_home() ) ? get_permalink() : $site_url;

    // ── Output meta tags ─────────────────────────────────────────────────────
    $meta_desc = esc_attr( wp_trim_words( $meta_desc, 30, '...' ) );
    ?>
    <!-- Meta Description -->
    <meta name="description" content="<?php echo $meta_desc; ?>">

    <!-- Open Graph -->
    <meta property="og:type"        content="<?php echo esc_attr( $og_type ); ?>">
    <meta property="og:title"       content="<?php echo esc_attr( $og_title ); ?>">
    <meta property="og:description" content="<?php echo $meta_desc; ?>">
    <meta property="og:url"         content="<?php echo esc_url( $og_url ); ?>">
    <meta property="og:site_name"   content="<?php echo esc_attr( $site_name ); ?>">
    <?php if ( $og_image ) : ?>
    <meta property="og:image"       content="<?php echo esc_url( $og_image ); ?>">
    <meta property="og:image:width"  content="1200">
    <meta property="og:image:height" content="630">
    <?php endif; ?>

    <!-- Twitter Card -->
    <meta name="twitter:card"        content="summary_large_image">
    <meta name="twitter:title"       content="<?php echo esc_attr( $og_title ); ?>">
    <meta name="twitter:description" content="<?php echo $meta_desc; ?>">
    <?php if ( $og_image ) : ?>
    <meta name="twitter:image"       content="<?php echo esc_url( $og_image ); ?>">
    <?php endif; ?>

    <!-- Canonical -->
    <link rel="canonical" href="<?php echo esc_url( $og_url ); ?>">
    <?php
}, 1 );


// ── Include custom CPTs in WordPress sitemap ──────────────────────────────────
add_filter( 'wp_sitemaps_post_types', function( $post_types ) {
    if ( ! isset( $post_types['city'] ) ) {
        $post_types['city'] = get_post_type_object( 'city' );
    }
    if ( ! isset( $post_types['faq'] ) ) {
        $post_types['faq'] = get_post_type_object( 'faq' );
    }
    if ( ! isset( $post_types['service'] ) ) {
        $post_types['service'] = get_post_type_object( 'service' );
    }
    return $post_types;
} );

add_filter( 'wp_sitemaps_post_types', function( $post_types ) {
    if ( ! isset( $post_types['city'] ) ) {
        $post_types['city'] = get_post_type_object( 'city' );
    }
    if ( ! isset( $post_types['faq'] ) ) {
        $post_types['faq'] = get_post_type_object( 'faq' );
    }
    if ( ! isset( $post_types['service'] ) ) {
        $post_types['service'] = get_post_type_object( 'service' );
    }
    if ( ! isset( $post_types['neighborhood'] ) ) {
        $post_types['neighborhood'] = get_post_type_object( 'neighborhood' );
    }
    return $post_types;
} );

// ── Include custom taxonomies in sitemap ──────────────────────────────────────
add_filter( 'wp_sitemaps_taxonomies', function( $taxonomies ) {
    $taxonomies['post_city']    = get_taxonomy( 'post_city' );
    $taxonomies['post_service'] = get_taxonomy( 'post_service' );
    $taxonomies['post_topic']   = get_taxonomy( 'post_topic' );
    return $taxonomies;
} );


// ── Serve llms.txt ────────────────────────────────────────────────────────────
add_action( 'init', function() {
    add_rewrite_rule( '^llms\.txt$', 'index.php?llms_txt=1', 'top' );
} );

add_filter( 'query_vars', function( $vars ) {
    $vars[] = 'llms_txt';
    return $vars;
} );

add_action( 'template_redirect', function() {
    if ( get_query_var( 'llms_txt' ) ) {
        $file = ABSPATH . 'llms.txt';
        if ( file_exists( $file ) ) {
            header( 'Content-Type: text/plain; charset=utf-8' );
            readfile( $file );
            exit;
        }
    }
} );



// ── Download ACF fields────────────────────────────────────────────────────────

add_action('admin_menu', function() {
    add_management_page('ACF Field List', 'ACF Field List', 'manage_options', 'acf-field-list', 'acf_field_list_page');
});

function acf_field_list_page() {
    if (!function_exists('acf_get_field_groups')) return;
    
    echo '<div class="wrap"><h1>ACF Field List</h1>';
    echo '<textarea style="width:100%;height:400px;font-family:monospace;font-size:12px;">';
    echo "Group\tField Label\tSlug\tType\n";
    echo str_repeat("-", 80) . "\n";
    
    foreach (acf_get_field_groups() as $group) {
        $fields = acf_get_fields($group['key']);
        if (!$fields) continue;
        foreach ($fields as $field) {
            echo $group['title'] . "\t" . $field['label'] . "\t" . $field['name'] . "\t" . $field['type'] . "\n";
            // Handle sub-fields (repeaters/groups)
            if (!empty($field['sub_fields'])) {
                foreach ($field['sub_fields'] as $sub) {
                    echo $group['title'] . "\t  → " . $sub['label'] . "\t" . $field['name'] . "_" . $sub['name'] . "\t" . $sub['type'] . "\n";
                }
            }
        }
    }
    
    echo '</textarea></div>';
}



// ============================================================
// LANDING PAGE CPT — keyword + location pages
// ============================================================

add_action( 'init', function () {
    register_post_type( 'landing_page', [
        'labels' => [
            'name'               => 'Landing Pages',
            'singular_name'      => 'Landing Page',
            'add_new'            => 'Add New',
            'add_new_item'       => 'Add New Landing Page',
            'edit_item'          => 'Edit Landing Page',
            'new_item'           => 'New Landing Page',
            'view_item'          => 'View Landing Page',
            'search_items'       => 'Search Landing Pages',
            'not_found'          => 'No landing pages found',
            'not_found_in_trash' => 'No landing pages found in trash',
            'menu_name'          => 'Landing Pages',
        ],
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'show_in_rest'       => true,
        'show_in_nav_menus'  => false,
        'query_var'          => true,
        'rewrite'            => false, 
        'capability_type'    => 'post',
        'has_archive'        => false,
        'hierarchical'       => false,
        'menu_position'      => 6,
        'menu_icon'          => 'dashicons-location',
        'supports'           => [ 'title', 'editor', 'thumbnail', 'excerpt', 'custom-fields' ],
    ] );
} );


// ============================================================
// NEIGHBORHOOD CPT
// Data-only — no public URL, no single template
// Queried dynamically by post_city taxonomy on city + landing pages
// ============================================================
 
// ── 1. ADD TO functions.php — CPT Registration ───────────────────────────────
// Paste after the landing_page CPT registration block
 
add_action( 'init', function () {
    register_post_type( 'neighborhood', [
        'labels' => [
            'name'               => 'Neighborhoods',
            'singular_name'      => 'Neighborhood',
            'add_new'            => 'Add New',
            'add_new_item'       => 'Add New Neighborhood',
            'edit_item'          => 'Edit Neighborhood',
            'new_item'           => 'New Neighborhood',
            'view_item'          => 'View Neighborhood',
            'search_items'       => 'Search Neighborhoods',
            'not_found'          => 'No neighborhoods found',
            'not_found_in_trash' => 'No neighborhoods found in trash',
            'menu_name'          => 'Neighborhoods',
        ],
        'public'             => false,   // no public URL
        'publicly_queryable' => true,   // allows importer to see it
        'show_ui'            => true,    // show in admin
        'show_in_menu'       => true,
        'show_in_rest'       => true,    // Gutenberg + REST support
        'show_in_nav_menus'  => false,
        'query_var'          => false,
        'rewrite'            => false,
        'capability_type'    => 'post',
        'has_archive'        => false,
        'hierarchical'       => false,
        'menu_position'      => 7,
        'menu_icon'          => 'dashicons-building',
        'supports'           => [ 'title', 'thumbnail' ],
    ] );
} );


// ── 2. ADD TO functions.php — ACF Field Group ────────────────────────────────
// Paste after the neighborhood CPT registration
// All field keys use prefix: field_nh_ to avoid conflicts
 
if ( function_exists( 'acf_add_local_field_group' ) ) :
 
acf_add_local_field_group( [
    'key'    => 'group_neighborhood_details',
    'title'  => 'Neighborhood Details',
    'fields' => [
 
        [
            'key'           => 'field_nh_image',
            'label'         => 'Neighborhood Image',
            'name'          => 'neighborhood_image',
            'type'          => 'image',
            'instructions'  => 'Photo representing this neighborhood. Used as thumbnail on city and landing pages.',
            'return_format' => 'array',
            'preview_size'  => 'medium',
            'library'       => 'all',
        ],
        [
            'key'          => 'field_nh_description',
            'label'        => 'Description',
            'name'         => 'neighborhood_description',
            'type'         => 'textarea',
            'instructions' => '2–3 lines. Lifestyle summary. e.g. "Tree-lined streets south of Ventura — ideal for families seeking space and top schools."',
            'rows'         => 3,
        ],
        [
            'key'          => 'field_nh_highlight',
            'label'        => 'Highlight',
            'name'         => 'neighborhood_highlight',
            'type'         => 'text',
            'instructions' => 'One punchy line. e.g. "Best for luxury hillside living" or "Top pick for families"',
        ],
        [
            'key'          => 'field_nh_link',
            'label'        => 'Link',
            'name'         => 'neighborhood_link',
            'type'         => 'url',
            'instructions' => 'Optional. Links neighborhood name to an internal or external page.',
        ],
 
    ],
 
    'location' => [
        [
            [
                'param'    => 'post_type',
                'operator' => '==',
                'value'    => 'neighborhood',
            ],
        ],
    ],
 
    'menu_order'            => 0,
    'position'              => 'normal',
    'style'                 => 'default',
    'label_placement'       => 'top',
    'instruction_placement' => 'label',
    'active'                => true,
    'description'           => 'Fields for neighborhood data records. Tagged by post_city taxonomy.',
] );
 
endif;
 

 
// ── 3. NEIGHBORHOOD QUERY HELPER FUNCTION ────────────────────────────────────
// Add to functions.php — reusable across city + landing page templates
 
function blayne_get_neighborhoods( $city_slug, $limit = 8 ) {
    if ( ! $city_slug ) return null;

    $query = new WP_Query( [
        'post_type'              => 'neighborhood',
        'post_status'            => 'publish',
        'posts_per_page'         => $limit,
        'orderby'                => 'menu_order',
        'order'                  => 'ASC',
        'suppress_filters'       => true,
        'ignore_sticky_posts'    => true,
        'tax_query'              => [ [
            'taxonomy' => 'post_city',
            'field'    => 'slug',
            'terms'    => $city_slug,
        ] ],
    ] );

    return $query->have_posts() ? $query : null;
}


// ── Custom Permalink: /keyword/location/ ─────────────────────────────────────

add_action( 'init', function () {
    add_rewrite_tag( '%lp_service%', '([^/]+)', 'lp_service=' );
} );

add_filter( 'post_type_link', function ( $link, $post ) {
    if ( $post->post_type !== 'landing_page' ) {
        return $link;
    }
    $terms = get_the_terms( $post->ID, 'post_service' );
    $service_slug = ( $terms && ! is_wp_error( $terms ) ) ? $terms[0]->slug : 'service';
    return str_replace( '%lp_service%', $service_slug, $link );
}, 10, 2 );

add_action( 'init', function () {
    // Exclude known WP and CPT slugs from the landing page rewrite
    // so /services/, /cities/, /faqs/, /wp-admin/, etc. are not intercepted
    $excluded = implode( '|', [
      'services',
	    'cities',
	    'faqs',
	    'about-us',
 	   'news',
 	   'contact',
 	   'wp-admin',
 	   'wp-content',
 	   'wp-includes',
  	  'wp-json',
   	 'feed',
   	 'sitemap',
  	  'page',
 	   'tag',
  	  'category',
 	   'author',
  	  'search',
  	  'cart',
 	   'checkout',
    ] );

    add_rewrite_rule(
        '^(?!' . $excluded . '/)([^/]+)/([^/]+)/?$',
        'index.php?post_type=landing_page&name=$matches[2]&lp_service=$matches[1]',
        'top'
    );
} );




// ── Add landing_page to sitemap ───────────────────────────────────────────────

add_filter( 'wp_sitemaps_post_types', function ( $post_types ) {
    if ( ! isset( $post_types['landing_page'] ) ) {
        $post_types['landing_page'] = get_post_type_object( 'landing_page' );
    }
    return $post_types;
} );


/**
 * ACF Field Group: Landing Page Details
 * CPT: landing_page
 * Covers all 10 blueprint sections + SEO/schema fields
 *
 * HOW TO INSTALL:
 * Option A — Paste into functions.php (runs on every load, always in sync)
 * Option B — ACF UI → Tools → Import JSON (use the JSON version instead)
 *
 * All field keys use prefix: field_lp_ to avoid conflicts
 */

if ( function_exists( 'acf_add_local_field_group' ) ) :

acf_add_local_field_group( [
    'key'                   => 'group_landing_page_details',
    'title'                 => 'Landing Page Details',
    'fields'                => [

        // ================================================================
        // SECTION 01 — HERO
        // ================================================================

        [
            'key'               => 'field_lp_tab_hero',
            'label'             => '01 — Hero',
            'name'              => '',
            'type'              => 'tab',
            'placement'         => 'top',
            'endpoint'          => 0,
        ],
        [
            'key'               => 'field_lp_hero_image',
            'label'             => 'Hero Image',
            'name'              => 'lp_hero_image',
            'type'              => 'image',
            'instructions'      => 'City-specific photo or video poster. ALT text auto-generates from H1.',
            'return_format'     => 'array',
            'preview_size'      => 'medium',
            'library'           => 'all',
        ],
        [
            'key'               => 'field_lp_hero_video_url',
            'label'             => 'Hero Video URL',
            'name'              => 'lp_hero_video_url',
            'type'              => 'url',
            'instructions'      => 'Optional. MP4 URL for background video. Falls back to hero image if empty.',
        ],
        [
            'key'               => 'field_lp_page_h1',
            'label'             => 'H1 Headline',
            'name'              => 'lp_page_h1',
            'type'              => 'text',
            'instructions'      => 'Exact-match keyword + city. e.g. "Buying a Home in Sherman Oaks — Blayne Pacelli, Realtor"',
            'required'          => 1,
            'maxlength'         => 80,
        ],
        [
            'key'               => 'field_lp_page_subheadline',
            'label'             => 'Hero Subheadline',
            'name'              => 'lp_page_subheadline',
            'type'              => 'text',
            'instructions'      => 'LSI keyword variation + value prop. e.g. "Local expert helping families buy in [City] since [year]."',
            'maxlength'         => 120,
        ],
        [
            'key'               => 'field_lp_hero_cta_label',
            'label'             => 'CTA Button Label',
            'name'              => 'lp_hero_cta_label',
            'type'              => 'text',
            'instructions'      => 'e.g. "Search Sherman Oaks Listings" or "Get Your Free Home Valuation"',
            'default_value'     => 'Get a Free Consultation',
        ],
        [
            'key'               => 'field_lp_hero_cta_url',
            'label'             => 'CTA Button URL',
            'name'              => 'lp_hero_cta_url',
            'type'              => 'url',
            'instructions'      => 'Leave blank to scroll to lead form (#lp-contact)',
        ],

        // ================================================================
        // SECTION 02 — AEO QUICK ANSWER BLOCK
        // ================================================================

        [
            'key'               => 'field_lp_tab_aeo',
            'label'             => '02 — AEO Quick Answers',
            'name'              => '',
            'type'              => 'tab',
            'placement'         => 'top',
            'endpoint'          => 0,
        ],
        [
            'key'               => 'field_lp_aeo_intro',
            'label'             => 'AEO Section Intro',
            'name'              => 'lp_aeo_intro',
            'type'              => 'text',
            'instructions'      => 'Optional one-liner above the Q&A blocks. e.g. "Quick answers about buying a home in Sherman Oaks"',
        ],
        [
            'key'               => 'field_lp_aeo_questions',
            'label'             => 'Quick Answer Q&As',
            'name'              => 'lp_aeo_questions',
            'type'              => 'repeater',
            'instructions'      => '3–5 high-intent questions. Short answer ≤60 words — written to be the AI snippet. Long answer 100–200 words with local data.',
            'min'               => 0,
            'max'               => 6,
            'layout'            => 'block',
            'button_label'      => 'Add Question',
            'sub_fields'        => [
                [
                    'key'           => 'field_lp_aeo_question',
                    'label'         => 'Question (H2)',
                    'name'          => 'lp_aeo_question',
                    'type'          => 'text',
                    'instructions'  => 'e.g. "What is the average home price in Sherman Oaks?"',
                    'required'      => 1,
                ],
                [
                    'key'           => 'field_lp_aeo_short_answer',
                    'label'         => 'Short Answer (AEO snippet — ≤60 words)',
                    'name'          => 'lp_aeo_short_answer',
                    'type'          => 'textarea',
                    'rows'          => 3,
                    'required'      => 1,
                ],
                [
                    'key'           => 'field_lp_aeo_long_answer',
                    'label'         => 'Long Answer (SEO — 100–200 words)',
                    'name'          => 'lp_aeo_long_answer',
                    'type'          => 'wysiwyg',
                    'tabs'          => 'all',
                    'toolbar'       => 'basic',
                    'media_upload'  => 0,
                ],
            ],
        ],

        // ================================================================
        // SECTION 03 — LOCAL MARKET SNAPSHOT
        // ================================================================

        [
            'key'               => 'field_lp_tab_market',
            'label'             => '03 — Market Snapshot',
            'name'              => '',
            'type'              => 'tab',
            'placement'         => 'top',
            'endpoint'          => 0,
        ],
        [
            'key'               => 'field_lp_market_section_h2',
            'label'             => 'Market Section H2',
            'name'              => 'lp_market_section_h2',
            'type'              => 'text',
            'instructions'      => 'e.g. "Sherman Oaks Real Estate Market — 2025"',
        ],
        [
            'key'               => 'field_lp_median_price',
            'label'             => 'Median Home Price',
            'name'              => 'lp_median_price',
            'type'              => 'text',
            'instructions'      => 'e.g. "$1,250,000" — displayed as large stat with trend indicator',
        ],
        [
            'key'               => 'field_lp_price_trend',
            'label'             => 'Price Trend',
            'name'              => 'lp_price_trend',
            'type'              => 'select',
            'instructions'      => 'Shows ↑ or ↓ next to median price',
            'choices'           => [
                'up'     => '↑ Up',
                'down'   => '↓ Down',
                'stable' => '→ Stable',
            ],
            'default_value'     => 'up',
            'return_format'     => 'value',
        ],
        [
            'key'               => 'field_lp_days_on_market',
            'label'             => 'Avg Days on Market',
            'name'              => 'lp_days_on_market',
            'type'              => 'text',
            'instructions'      => 'e.g. "18 days" — AEO target for "how fast do homes sell in [city]"',
        ],
        [
            'key'               => 'field_lp_homes_sold_30',
            'label'             => 'Homes Sold Last 30 Days',
            'name'              => 'lp_homes_sold_30',
            'type'              => 'text',
            'instructions'      => 'e.g. "42" — velocity signal, updatable without code changes',
        ],
        [
            'key'               => 'field_lp_price_per_sqft',
            'label'             => 'Price Per Sq Ft',
            'name'              => 'lp_price_per_sqft',
            'type'              => 'text',
            'instructions'      => 'e.g. "$680/sqft" — comparison anchor vs nearby cities',
        ],
        [
            'key'               => 'field_lp_market_type',
            'label'             => 'Market Type',
            'name'              => 'lp_market_type',
            'type'              => 'select',
            'instructions'      => 'Changes copy tone throughout the page',
            'choices'           => [
                'seller' => "Seller's Market",
                'buyer'  => "Buyer's Market",
                'balanced' => 'Balanced Market',
            ],
            'default_value'     => 'seller',
            'return_format'     => 'value',
        ],
        [
            'key'               => 'field_lp_market_summary',
            'label'             => 'Market Summary',
            'name'              => 'lp_market_summary',
            'type'              => 'textarea',
            'instructions'      => '2–3 sentence narrative below the stats. Include local data points.',
            'rows'              => 4,
        ],

        // ================================================================
        // SECTION 04 — ABOUT THE AREA
        // ================================================================

        [
            'key'               => 'field_lp_tab_area',
            'label'             => '04 — About the Area',
            'name'              => '',
            'type'              => 'tab',
            'placement'         => 'top',
            'endpoint'          => 0,
        ],
        [
            'key'               => 'field_lp_city_name',
            'label'             => 'City Name (display)',
            'name'              => 'lp_city_name',
            'type'              => 'text',
            'instructions'      => 'e.g. "Sherman Oaks" — used throughout template for city references',
            'required'          => 1,
        ],
        [
            'key'               => 'field_lp_service_name',
            'label'             => 'Service Name (display)',
            'name'              => 'lp_service_name',
            'type'              => 'text',
            'instructions'      => 'e.g. "Buying a Home" — used in headings, schema, breadcrumbs',
            'required'          => 1,
        ],
        [
            'key'               => 'field_lp_service_slug',
            'label'             => 'Service Slug',
            'name'              => 'lp_service_slug',
            'type'              => 'text',
            'instructions'      => 'e.g. "buying-a-home" — used in breadcrumb schema URL',
            'required'          => 1,
        ],
        [
            'key'               => 'field_lp_city_description',
            'label'             => 'City Description',
            'name'              => 'lp_city_description',
            'type'              => 'wysiwyg',
            'instructions'      => '200–400 words. Lifestyle narrative unique per page. Include school names, landmarks, neighborhoods as local entities.',
            'tabs'              => 'all',
            'toolbar'           => 'full',
            'media_upload'      => 0,
        ],
        [
            'key'               => 'field_lp_who_lives_here',
            'label'             => 'Who Lives Here',
            'name'              => 'lp_who_lives_here',
            'type'              => 'textarea',
            'instructions'      => 'Buyer persona signal. e.g. "Sherman Oaks attracts young families, professionals, and move-up buyers..."',
            'rows'              => 3,
        ],
       [
            'key'               => 'field_lp_proximity_note',
            'label'             => 'Proximity Callout',
            'name'              => 'lp_proximity_note',
            'type'              => 'text',
            'instructions'      => 'Geographic entity signals. e.g. "12 miles from Downtown LA, 20 min to Santa Monica"',
        ],
        // ================================================================
        // SECTION 05 — SERVICES + VALUE PROP
        // ================================================================

        [
            'key'               => 'field_lp_tab_services',
            'label'             => '05 — Services',
            'name'              => '',
            'type'              => 'tab',
            'placement'         => 'top',
            'endpoint'          => 0,
        ],
        [
            'key'               => 'field_lp_service_cards',
            'label'             => 'Service Cards',
            'name'              => 'lp_service_cards',
            'type'              => 'repeater',
            'instructions'      => '3 cards. Each pulls city name into copy. Links to Service CPT page.',
            'min'               => 0,
            'max'               => 3,
            'layout'            => 'block',
            'button_label'      => 'Add Service Card',
            'sub_fields'        => [
                [
                    'key'       => 'field_lp_card_title',
                    'label'     => 'Card H3',
                    'name'      => 'lp_card_title',
                    'type'      => 'text',
                    'instructions' => 'e.g. "Buy a Home in Sherman Oaks"',
                ],
                [
                    'key'       => 'field_lp_card_icon',
                    'label'     => 'Icon (emoji or SVG class)',
                    'name'      => 'lp_card_icon',
                    'type'      => 'text',
                    'instructions' => 'e.g. 🏠 or dashicon class',
                ],
                [
                    'key'       => 'field_lp_card_desc',
                    'label'     => 'Description',
                    'name'      => 'lp_card_desc',
                    'type'      => 'textarea',
                    'rows'      => 3,
                ],
                [
                    'key'       => 'field_lp_card_differentiator',
                    'label'     => 'Differentiator Line',
                    'name'      => 'lp_card_differentiator',
                    'type'      => 'text',
                    'instructions' => '1 sentence on what makes Blayne unique for this service in this city',
                ],
                [
                    'key'       => 'field_lp_card_link',
                    'label'     => 'Card Link (Service CPT URL)',
                    'name'      => 'lp_card_link',
                    'type'      => 'url',
                ],
            ],
        ],

        // ================================================================
        // SECTION 06 — SOCIAL PROOF / REVIEWS
        // ================================================================

        [
            'key'               => 'field_lp_tab_reviews',
            'label'             => '06 — Social Proof',
            'name'              => '',
            'type'              => 'tab',
            'placement'         => 'top',
            'endpoint'          => 0,
        ],
        [
            'key'               => 'field_lp_reviews_heading',
            'label'             => 'Reviews Section Heading',
            'name'              => 'lp_reviews_heading',
            'type'              => 'text',
            'default_value'     => 'What Our Clients Say',
        ],
        [
            'key'               => 'field_lp_testimonials',
            'label'             => 'Testimonial Cards',
            'name'              => 'lp_testimonials',
            'type'              => 'repeater',
            'instructions'      => '3–5 cards. Location-tagged reviews. Falls back to Google Reviews (Trustindex) if empty.',
            'min'               => 0,
            'max'               => 5,
            'layout'            => 'block',
            'button_label'      => 'Add Testimonial',
            'sub_fields'        => [
                [
                    'key'       => 'field_lp_testi_name',
                    'label'     => 'Client Name',
                    'name'      => 'lp_testi_name',
                    'type'      => 'text',
                ],
                [
                    'key'       => 'field_lp_testi_city',
                    'label'     => 'Client City',
                    'name'      => 'lp_testi_city',
                    'type'      => 'text',
                ],
                [
                    'key'       => 'field_lp_testi_type',
                    'label'     => 'Transaction Type',
                    'name'      => 'lp_testi_type',
                    'type'      => 'select',
                    'choices'   => [
                        'bought' => 'Bought',
                        'sold'   => 'Sold',
                        'both'   => 'Bought & Sold',
                    ],
                    'return_format' => 'value',
                ],
                [
                    'key'       => 'field_lp_testi_result',
                    'label'     => 'Result Callout',
                    'name'      => 'lp_testi_result',
                    'type'      => 'text',
                    'instructions' => 'e.g. "Sold in 9 days, $14k over asking"',
                ],
                [
                    'key'       => 'field_lp_testi_quote',
                    'label'     => 'Quote',
                    'name'      => 'lp_testi_quote',
                    'type'      => 'textarea',
                    'rows'      => 3,
                ],
                [
                    'key'       => 'field_lp_testi_stars',
                    'label'     => 'Star Rating',
                    'name'      => 'lp_testi_stars',
                    'type'      => 'select',
                    'choices'   => [
                        '5' => '★★★★★ 5',
                        '4' => '★★★★☆ 4',
                    ],
                    'default_value' => '5',
                    'return_format' => 'value',
                ],
            ],
        ],
        [
            'key'               => 'field_lp_show_google_reviews',
            'label'             => 'Show Google Reviews (Trustindex)',
            'name'              => 'lp_show_google_reviews',
            'type'              => 'true_false',
            'instructions'      => 'Shows [trustindex] shortcode below testimonial cards',
            'default_value'     => 1,
            'ui'                => 1,
        ],

        // ================================================================
        // SECTION 07 — ABOUT BLAYNE
        // ================================================================

        [
            'key'               => 'field_lp_tab_bio',
            'label'             => '07 — About Blayne',
            'name'              => '',
            'type'              => 'tab',
            'placement'         => 'top',
            'endpoint'          => 0,
        ],
        [
            'key'               => 'field_lp_city_bio_line',
            'label'             => 'City-Specific Bio Line',
            'name'              => 'lp_city_bio_line',
            'type'              => 'textarea',
            'instructions'      => '2–3 sentences with years in market, familiarity with THIS city, and what drives his approach. e.g. "Blayne has helped dozens of families find their perfect home in Sherman Oaks..."',
            'rows'              => 3,
        ],

        // ================================================================
        // SECTION 08 — FEATURED LISTINGS
        // ================================================================

        [
            'key'               => 'field_lp_tab_listings',
            'label'             => '08 — Featured Listings',
            'name'              => '',
            'type'              => 'tab',
            'placement'         => 'top',
            'endpoint'          => 0,
        ],
        [
            'key'               => 'field_lp_listings_h2',
            'label'             => 'Listings Section H2',
            'name'              => 'lp_listings_h2',
            'type'              => 'text',
            'instructions'      => 'e.g. "Homes for Sale in Sherman Oaks" — exact keyword target',
        ],
        [
            'key'               => 'field_lp_featured_listings',
            'label'             => 'Featured Listings',
            'name'              => 'lp_featured_listings',
            'type'              => 'repeater',
            'instructions'      => '3–4 listings. Different addresses/prices on every page = strongest uniqueness driver.',
            'min'               => 0,
            'max'               => 4,
            'layout'            => 'block',
            'button_label'      => 'Add Listing',
            'sub_fields'        => [
                [
                    'key'       => 'field_lp_listing_photo',
                    'label'     => 'Listing Photo',
                    'name'      => 'lp_listing_photo',
                    'type'      => 'image',
                    'return_format' => 'array',
                    'preview_size'  => 'thumbnail',
                ],
                [
                    'key'       => 'field_lp_listing_address',
                    'label'     => 'Address',
                    'name'      => 'lp_listing_address',
                    'type'      => 'text',
                ],
                [
                    'key'       => 'field_lp_listing_price',
                    'label'     => 'Price',
                    'name'      => 'lp_listing_price',
                    'type'      => 'text',
                    'instructions' => 'e.g. "$1,350,000"',
                ],
                [
                    'key'       => 'field_lp_listing_beds',
                    'label'     => 'Beds',
                    'name'      => 'lp_listing_beds',
                    'type'      => 'text',
                ],
                [
                    'key'       => 'field_lp_listing_baths',
                    'label'     => 'Baths',
                    'name'      => 'lp_listing_baths',
                    'type'      => 'text',
                ],
                [
                    'key'       => 'field_lp_listing_sqft',
                    'label'     => 'Sq Ft',
                    'name'      => 'lp_listing_sqft',
                    'type'      => 'text',
                ],
                [
                    'key'       => 'field_lp_listing_description',
                    'label'     => 'Short Description',
                    'name'      => 'lp_listing_description',
                    'type'      => 'textarea',
                    'rows'      => 2,
                ],
                [
                    'key'       => 'field_lp_listing_url',
                    'label'     => 'Listing URL',
                    'name'      => 'lp_listing_url',
                    'type'      => 'url',
                ],
            ],
        ],
        [
            'key'               => 'field_lp_view_all_listings_url',
            'label'             => 'View All Listings URL',
            'name'              => 'lp_view_all_listings_url',
            'type'              => 'url',
            'instructions'      => 'IDX link filtered by this city. e.g. Zillow/Realtor.com city search URL',
        ],

        // ================================================================
        // SECTION 09 — LEAD CAPTURE
        // ================================================================

        [
            'key'               => 'field_lp_tab_lead',
            'label'             => '09 — Lead Capture',
            'name'              => '',
            'type'              => 'tab',
            'placement'         => 'top',
            'endpoint'          => 0,
        ],
        [
            'key'               => 'field_lp_lead_section_h2',
            'label'             => 'Lead Section H2',
            'name'              => 'lp_lead_section_h2',
            'type'              => 'text',
            'instructions'      => 'e.g. "Ready to Buy or Sell in Sherman Oaks?" — includes keyword',
            'default_value'     => 'Ready to Buy or Sell? Let\'s Talk.',
        ],
        [
            'key'               => 'field_lp_lead_section_copy',
            'label'             => 'Lead Section Supporting Copy',
            'name'              => 'lp_lead_section_copy',
            'type'              => 'textarea',
            'instructions'      => '2–3 sentences. City name, personal tone, clear value offer. e.g. "I want to help you find a home in Sherman Oaks..."',
            'rows'              => 3,
        ],
        // Note: GHL embed pulls from Site Settings options — no field needed here

        // ================================================================
        // SECTION 10 — NEARBY AREAS
        // ================================================================

        [
            'key'               => 'field_lp_tab_nearby',
            'label'             => '10 — Nearby Areas',
            'name'              => '',
            'type'              => 'tab',
            'placement'         => 'top',
            'endpoint'          => 0,
        ],
        [
            'key'               => 'field_lp_nearby_heading',
            'label'             => 'Nearby Areas Heading',
            'name'              => 'lp_nearby_heading',
            'type'              => 'text',
            'default_value'     => 'Also Serving These Communities',
        ],
        [
            'key'               => 'field_lp_nearby_cities',
            'label'             => 'Nearby Cities',
            'name'              => 'lp_nearby_cities',
            'type'              => 'repeater',
            'instructions'      => '4–8 nearby cities. Each links to its own landing page. Creates silo link structure.',
            'min'               => 0,
            'max'               => 8,
            'layout'            => 'table',
            'button_label'      => 'Add Nearby City',
            'sub_fields'        => [
                [
                    'key'       => 'field_lp_nearby_city_name',
                    'label'     => 'City Name',
                    'name'      => 'lp_nearby_city_name',
                    'type'      => 'text',
                ],
                [
                    'key'       => 'field_lp_nearby_city_url',
                    'label'     => 'Landing Page URL',
                    'name'      => 'lp_nearby_city_url',
                    'type'      => 'text',
                    'instructions' => 'e.g. /buying-a-home/studio-city/',
                ],
                [
                    'key'       => 'field_lp_nearby_city_distance',
                    'label'     => 'Distance',
                    'name'      => 'lp_nearby_city_distance',
                    'type'      => 'text',
                    'instructions' => 'e.g. "3 miles away"',
                ],
                [
                    'key'       => 'field_lp_nearby_city_descriptor',
                    'label'     => 'Market Descriptor (1 line)',
                    'name'      => 'lp_nearby_city_descriptor',
                    'type'      => 'text',
                    'instructions' => 'e.g. "Active seller\'s market, median $1.1M"',
                ],
                [
                    'key'       => 'field_lp_nearby_city_thumb',
                    'label'     => 'Thumbnail',
                    'name'      => 'lp_nearby_city_thumb',
                    'type'      => 'image',
                    'return_format' => 'array',
                    'preview_size'  => 'thumbnail',
                ],
            ],
        ],

        // ================================================================
        // SEO / SCHEMA TAB
        // ================================================================

        [
            'key'               => 'field_lp_tab_seo',
            'label'             => 'SEO + Schema',
            'name'              => '',
            'type'              => 'tab',
            'placement'         => 'top',
            'endpoint'          => 0,
        ],
        [
            'key'               => 'field_lp_meta_title',
            'label'             => 'Meta Title',
            'name'              => 'lp_meta_title',
            'type'              => 'text',
            'instructions'      => '≤60 chars. e.g. "Buying a Home in Sherman Oaks | Blayne Pacelli — Realtor"',
            'maxlength'         => 60,
        ],
        [
            'key'               => 'field_lp_meta_description',
            'label'             => 'Meta Description',
            'name'              => 'lp_meta_description',
            'type'              => 'textarea',
            'instructions'      => '≤155 chars. City + keyword + CTA. Write as a direct answer for AEO.',
            'rows'              => 3,
            'maxlength'         => 155,
       ],
        [
            'key'           => 'field_lp_faq_heading',
            'label'         => 'FAQ Section Heading',
            'name'          => 'lp_faq_heading',
            'type'          => 'text',
            'instructions'  => 'Default: "People Also Ask" — override per page if needed',
            'default_value' => 'People Also Ask',
        ],

    ], // end fields

    'location' => [
        [
            [
                'param'    => 'post_type',
                'operator' => '==',
                'value'    => 'landing_page',
            ],
        ],
    ],

    'menu_order'            => 0,
    'position'              => 'normal',
    'style'                 => 'default',
    'label_placement'       => 'top',
    'instruction_placement' => 'label',
    'active'                => true,
    'description'           => 'All fields for the 10-section keyword + location landing page blueprint.',
] );

endif;


// Fix IMPress / ACF load order conflict
add_filter( 'impress_idx_load_priority', function() {
    return 99;
});

// Load IDX CSS before our stylesheet so ours wins
add_action( 'wp_enqueue_scripts', function() {
    if ( is_page_template( 'page-search.php' ) ) {
        wp_dequeue_style( 'idx-omnibar-css' );
        wp_enqueue_style( 'idx-omnibar-css', 
            content_url( '/plugins/idx-broker-platinum/idx/widgets/../../assets/css/widgets/idx-omnibar.min.css' ),
            array(),
            '6.9.4'
        );
    }
}, 999 );