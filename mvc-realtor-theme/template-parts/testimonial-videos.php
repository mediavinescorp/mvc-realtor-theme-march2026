<?php
$heading  = get_field( 'testimonial_heading', 'option' );
$subtext  = get_field( 'testimonial_subtext', 'option' );
$videos   = get_field( 'testimonial_videos', 'option' );
?>

<?php if ( $videos ) : ?>
<section class="testimonial-videos">
    <div class="testimonial-videos__inner">

        <!-- Section Header -->
        <div class="testimonial-videos__header">
            <?php if ( $heading ) : ?>
                <h2 class="testimonial-videos__heading">
                    <?php echo esc_html( $heading ); ?>
                </h2>
            <?php endif; ?>
            <?php if ( $subtext ) : ?>
                <p class="testimonial-videos__subtext">
                    <?php echo esc_html( $subtext ); ?>
                </p>
            <?php endif; ?>
        </div>

        <!-- Videos Grid -->
        <div class="testimonial-videos__grid">
            <?php foreach ( $videos as $video ) :
                $youtube_url = $video['youtube_url'];
                // Convert YouTube URL to embed URL
                preg_match( '/(?:youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]+)/', $youtube_url, $matches );
                $video_id = isset( $matches[1] ) ? $matches[1] : '';
            ?>
                <?php if ( $video_id ) : ?>
                <div class="testimonial-videos__item">
                    <?php if ( $video['video_title'] ) : ?>
                        <h3 class="testimonial-videos__video-title">
                            <?php echo esc_html( $video['video_title'] ); ?>
                        </h3>
                    <?php endif; ?>
                    <div class="testimonial-videos__embed">
                        <iframe
                            src="https://www.youtube.com/embed/<?php echo esc_attr( $video_id ); ?>?rel=0"
                            title="<?php echo esc_attr( $video['video_title'] ); ?>"
                            frameborder="0"
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                            allowfullscreen
                            loading="lazy">
                        </iframe>
                    </div>
                    <?php if ( $video['video_description'] ) : ?>
                        <p class="testimonial-videos__video-desc">
                            <?php echo esc_html( $video['video_description'] ); ?>
                        </p>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>

    </div>
</section>
<?php endif; ?>