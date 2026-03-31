<?php get_header(); ?>

<?php
$api_key = get_field( 'google_maps_api_key', 'option' );
$phone   = get_field( 'phone_number', 'option' );

$cities = new WP_Query( array(
    'post_type'      => 'city',
    'posts_per_page' => -1,
    'orderby'        => 'title',
    'order'          => 'ASC',
) );
?>

<!-- Hero -->
<section class="page-hero">
    <div class="page-hero__inner">
        <h1 class="page-hero__title">Explore Our Service Areas</h1>
        <p class="page-hero__tagline">Serving 30+ cities across Greater Los Angeles County — find your perfect neighborhood today.</p>
    </div>
</section>

<!-- Map -->
<?php if ( $api_key ) : ?>
<section class="cities-map">
    <div id="blayne-cities-map" class="cities-map__canvas"></div>
</section>
<?php endif; ?>

<!-- City Cards Grid -->
<section class="cities-archive">
    <div class="cities-archive__inner">

        <div class="cities-archive__header">
            <h2 class="cities-archive__heading">All Service Areas</h2>
            <p class="cities-archive__subtext">Click any city to learn about the neighborhood, schools, restaurants, home prices and more.</p>
        </div>

        <?php if ( $cities->have_posts() ) : ?>
        <div class="cities-archive__grid">
            <?php while ( $cities->have_posts() ) : $cities->the_post();
                $hero_image  = get_field( 'hero_image' );
                $city_intro  = get_field( 'city_intro' );
                $avg_price   = get_field( 'avg_home_cost' );
                $lat         = get_field( 'city_latitude' );
                $lng         = get_field( 'city_longitude' );
            ?>
                <a href="<?php the_permalink(); ?>" class="cities-archive__card">
                    <div class="cities-archive__card-image-wrap">
                        <?php if ( $hero_image ) : ?>
                            <img src="<?php echo esc_url( $hero_image['url'] ); ?>"
                                 alt="<?php echo esc_attr( $hero_image['alt'] ? $hero_image['alt'] : get_the_title() . ' Real Estate' ); ?>"
                                 class="cities-archive__card-image"
                                 loading="lazy">
                        <?php else : ?>
                            <div class="cities-archive__card-placeholder"></div>
                        <?php endif; ?>
                        <?php if ( $avg_price ) : ?>
                            <div class="cities-archive__card-price">
                                Avg: <?php echo esc_html( $avg_price ); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="cities-archive__card-content">
                        <h3 class="cities-archive__card-title"><?php the_title(); ?></h3>
                        <?php if ( $city_intro ) : ?>
                            <p class="cities-archive__card-desc">
                                <?php echo esc_html( wp_trim_words( $city_intro, 15, '...' ) ); ?>
                            </p>
                        <?php endif; ?>
                        <span class="cities-archive__card-link">View City Guide →</span>
                    </div>
                </a>
            <?php endwhile; wp_reset_postdata(); ?>
        </div>
        <?php endif; ?>

    </div>
</section>

<!-- Lead Form -->
<?php get_template_part( 'template-parts/lead-form' ); ?>

<!-- Map Script -->
<?php if ( $api_key ) : ?>
<script>
var blayneCities = <?php
    $map_cities = new WP_Query( array(
        'post_type'      => 'city',
        'posts_per_page' => -1,
        'orderby'        => 'title',
        'order'          => 'ASC',
    ) );
    $city_data = array();
    if ( $map_cities->have_posts() ) :
        while ( $map_cities->have_posts() ) : $map_cities->the_post();
            $lat       = get_field( 'city_latitude' );
            $lng       = get_field( 'city_longitude' );
            $intro     = get_field( 'city_intro' );
            $avg_price = get_field( 'avg_home_cost' );
            if ( $lat && $lng ) :
                $city_data[] = array(
                    'name'  => get_the_title(),
                    'lat'   => (float) trim( $lat ),
                    'lng'   => (float) trim( $lng ),
                    'url'   => get_permalink(),
                    'intro' => wp_trim_words( $intro, 12, '...' ),
                    'price' => $avg_price,
                );
            endif;
        endwhile;
        wp_reset_postdata();
    endif;
    echo json_encode( $city_data );
?>;


function initBlayneCitiesMap() {
    var mapCenter = { lat: 34.0522, lng: -118.2437 };
    var map = new google.maps.Map( document.getElementById( 'blayne-cities-map' ), {
        zoom:              10,
        center:            mapCenter,
        mapTypeControl:    false,
        streetViewControl: false,
        fullscreenControl: true,
        styles: [
            { featureType: 'poi', elementType: 'labels', stylers: [{ visibility: 'off' }] }
        ]
    });

    var infoWindow = new google.maps.InfoWindow();

    blayneCities.forEach( function( city ) {
        var marker = new google.maps.Marker({
            position: { lat: city.lat, lng: city.lng },
            map:      map,
            title:    city.name,
          icon: {
    url:        'https://maps.google.com/mapfiles/ms/icons/red.png',
    scaledSize: new google.maps.Size( 28, 28 ),
    anchor:     new google.maps.Point( 14, 28 ),
}
        });

        marker.addListener( 'click', function() {
            var content = '<div class="map-popup">' +
                '<h3 class="map-popup__title">' + city.name + '</h3>' +
                ( city.intro ? '<p class="map-popup__desc">' + city.intro + '</p>' : '' ) +
                ( city.price ? '<p class="map-popup__price"><strong>Avg Home:</strong> ' + city.price + '</p>' : '' ) +
                '<a href="' + city.url + '" class="map-popup__btn">View City Guide →</a>' +
                '</div>';
            infoWindow.setContent( content );
            infoWindow.open( map, marker );
        });
    });
}
</script>
<script src="https://maps.googleapis.com/maps/api/js?key=<?php echo esc_attr( $api_key ); ?>&callback=initBlayneCitiesMap" async defer></script>
<?php endif; ?>
<?php get_footer(); ?>