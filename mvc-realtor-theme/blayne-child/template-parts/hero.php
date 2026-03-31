<?php
$headline     = get_field( 'hero_headline' );
$subtext      = get_field( 'hero_subtext' );
$video_file   = get_field( 'hero_video' );
$video_url    = get_field( 'hero_video_url' );
$poster       = get_field( 'hero_video_poster' );
$show_cta     = get_field( 'show_cta' );
$cta_label    = get_field( 'cta_label' );
$phone        = get_field( 'phone_number', 'option' );

$poster_url   = $poster ? $poster['url'] : '';
$fallback_h1  = 'Los Angeles County Realtor';
?>

<section class="hero">

    <!-- Video Background -->
    <?php if ( $video_file || $video_url ) : ?>
        <div class="hero__video-wrap">
            <?php if ( $video_file ) : ?>
                <video class="hero__video"
                       autoplay
                       muted
                       loop
                       playsinline
                       poster="<?php echo esc_url( $poster_url ); ?>">
                    <source src="<?php echo esc_url( $video_file['url'] ); ?>" type="video/mp4">
                </video>
            <?php elseif ( $video_url ) : ?>
                <iframe class="hero__video hero__video--iframe"
                        src="<?php echo esc_url( $video_url ); ?>?autoplay=1&mute=1&loop=1&controls=0&showinfo=0&rel=0&playsinline=1"
                        frameborder="0"
                        allow="autoplay; fullscreen"
                        allowfullscreen>
                </iframe>
            <?php endif; ?>
            <div class="hero__overlay"></div>
        </div>
    <?php elseif ( $poster_url ) : ?>
        <div class="hero__image-wrap">
            <img src="<?php echo esc_url( $poster_url ); ?>"
                 alt="<?php echo esc_attr( $headline ? $headline : $fallback_h1 ); ?>"
                 class="hero__image">
            <div class="hero__overlay"></div>
        </div>
    <?php endif; ?>

    <!-- Hero Content -->
    <div class="hero__content">
        <div class="hero__inner">
            <h1 class="hero__headline">
                <?php echo esc_html( $headline ? $headline : $fallback_h1 ); ?>
            </h1>
            <?php if ( $subtext ) : ?>
                <p class="hero__subtext">
                    <?php echo esc_html( $subtext ); ?>
                </p>
            <?php endif; ?>

<div class="hero__search">
                <?php echo do_shortcode('[idx-omnibar styles="1" extra="0" min_price="0" remove_price_validation="0" ]'); ?>
            </div>  
 <div class="hero__cta-wrap">
    <a href="<?php echo esc_url( home_url( '/contact' ) ); ?>"
       class="hero__cta-btn">
        Let's Meet Up
    </a>
    <?php if ( $phone ) : ?>
        <a href="tel:<?php echo esc_attr( $phone ); ?>"
           class="hero__cta-btn hero__cta-btn--outline">
            Call Now
        </a>
    <?php endif; ?>
</div>
       
        </div>
    </div>
</section>