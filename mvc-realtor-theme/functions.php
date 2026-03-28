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

   wp_enqueue_script(
        'blayne-nav',
        get_stylesheet_directory_uri() . '/js/nav.js',
        array( 'splide-js' ),
        filemtime( get_stylesheet_directory() . '/js/nav.js' ),
        true
    );

// Splide.js — carousel library
    wp_enqueue_script(
        'splide-js',
        'https://cdn.jsdelivr.net/npm/@splidejs/splide@4.1.4/dist/js/splide.min.js',
        array(),
        null,
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
    register_taxonomy( 'post_city', [ 'post', 'faq' ], [
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
    register_taxonomy( 'post_service', [ 'post', 'faq' ], [
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
        'taxonomy'  => '',
        'term_slug' => '',
        'limit'     => 6,
        'heading'   => 'Frequently Asked Questions',
    ];
    $args = wp_parse_args( $args, $defaults );

    $query_args = [
        'post_type'      => 'faq',
        'post_status'    => 'publish',
        'posts_per_page' => $args['limit'],
        'orderby'        => [ 'meta_value_num' => 'ASC', 'title' => 'ASC' ],
        'meta_key'       => 'sort_order',
    ];

    if ( $args['taxonomy'] && $args['term_slug'] ) {
        $query_args['tax_query'] = [ [
            'taxonomy' => $args['taxonomy'],
            'field'    => 'slug',
            'terms'    => $args['term_slug'],
        ] ];
    }

   $faq_q = new WP_Query( $query_args );
if ( ! $faq_q->have_posts() ) return;

// ── FAQPage Schema for this section ──────────────────────────────────────────
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
                var panel    = document.getElementById(btn.getAttribute('aria-controls'));
                var item     = btn.closest('.page-faq-item');
                var isOpen   = btn.getAttribute('aria-expanded') === 'true';

                // close all in this section
                acc.querySelectorAll('.page-faq-trigger').forEach(function(b){
                    b.setAttribute('aria-expanded','false');
                    b.closest('.page-faq-item').classList.remove('page-faq-item--open');
                    var p = document.getElementById(b.getAttribute('aria-controls'));
                    if (p) p.hidden = true;
                });

                // open clicked one if it was closed
                if (!isOpen) {
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