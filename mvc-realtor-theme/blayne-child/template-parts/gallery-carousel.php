<?php




$photos = get_field( 'gallery_photos', 'option' );
$phone  = get_field( 'phone_number', 'option' );
?>

<?php if ( $photos ) : ?>
<section class="gallery-carousel">

    <div class="gallery-carousel__header">
        <h2 class="gallery-carousel__heading">From buying to staging and selling I'll be your real estate guide</h2>
    </div>

    <div class="gallery-carousel__wrap">
        <div class="splide" id="property-gallery">
            <div class="splide__track">
                <ul class="splide__list">
                    <?php foreach ( $photos as $item ) :
                        $photo   = $item['gallery_photo'];
                        $caption = $item['gallery_caption'];
                        $alt     = $item['gallery_alt'] ? $item['gallery_alt'] : $caption;
                    ?>
                        <?php if ( $photo ) : ?>
                        <li class="splide__slide">
                            <div class="gallery-carousel__slide">
                                <img src="<?php echo esc_url( $photo['url'] ); ?>"
                                     alt="<?php echo esc_attr( $alt ); ?>"
                                     class="gallery-carousel__img"
                                     loading="lazy">
                                <?php if ( $caption ) : ?>
                                    <div class="gallery-carousel__caption">
                                        <?php echo esc_html( $caption ); ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </li>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>

    <div class="gallery-carousel__cta">
        <?php if ( $phone ) : ?>
            <a href="tel:<?php echo esc_attr( $phone ); ?>"
               class="gallery-carousel__btn">
                Call Blayne
            </a>
        <?php endif; ?>
    </div>

</section>
<?php endif; ?>