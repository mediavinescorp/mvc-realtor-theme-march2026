<?php
$years        = get_field( 'years_experience', 'option' );
$listings     = get_field( 'listings_per_month', 'option' );
$stats_bg     = get_field( 'stats_bg_image', 'option' );
$years_val    = $years ? intval( $years ) : 20;
$listings_val = $listings ? intval( str_replace( ',', '', $listings ) ) : 6000;
$bg_url       = $stats_bg ? $stats_bg['url'] : '';
?>

<section class="stats-bar" <?php if ( $bg_url ) : ?>style="background-image: url('<?php echo esc_url( $bg_url ); ?>');"<?php endif; ?>>
    <div class="stats-bar__overlay"></div>
    <div class="stats-bar__inner">

        <div class="stats-bar__item">
            <div class="stats-bar__number">
                <span class="stats-counter" data-target="<?php echo $years_val; ?>" data-suffix="+">0</span>
            </div>
            <hr class="stats-bar__divider">
            <div class="stats-bar__label">Years matching your needs</div>
            <div class="stats-bar__sublabel">to the right home &amp; city</div>
        </div>

        <div class="stats-bar__item">
            <div class="stats-bar__number">
                <span class="stats-counter" data-target="30" data-suffix="+">0</span>
            </div>
            <hr class="stats-bar__divider">
            <div class="stats-bar__label">Cities we help you find</div>
            <div class="stats-bar__sublabel">the perfect home &amp; neighborhood</div>
        </div>

        <div class="stats-bar__item">
            <div class="stats-bar__number">
                <span class="stats-counter" data-target="<?php echo $listings_val; ?>" data-suffix="+" data-format="true">0</span>
            </div>
            <hr class="stats-bar__divider">
            <div class="stats-bar__label">Listings we'll help review</div>
            <div class="stats-bar__sublabel">every month for you</div>
        </div>

    </div>
</section>