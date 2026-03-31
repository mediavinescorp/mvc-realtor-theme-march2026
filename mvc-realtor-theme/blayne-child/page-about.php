<?php
/*
 * Template Name: About Page
 */
?>

<?php get_header(); ?>

<?php
$photo        = get_field( 'blayne_photo_2', 'option' );
$phone        = get_field( 'phone_number', 'option' );
$email        = get_field( 'email_address', 'option' );
$listing_rate = get_field( 'listing_price_rate', 'option' );
$closing_rate = get_field( 'closing_success_rate', 'option' );
$years        = get_field( 'years_experience', 'option' );
?>

<!-- Hero -->
<!-- Hero -->
<?php
$hero_image = get_field( 'hero_image' );
$hero_url   = $hero_image ? $hero_image['url'] : '';
?>
<section class="page-hero" <?php if ( $hero_url ) : ?>style="background-image: url('<?php echo esc_url( $hero_url ); ?>'); background-size: cover; background-position: center;"<?php endif; ?>>
    <div class="page-hero__overlay"></div>
    <div class="page-hero__inner">        <h1 class="page-hero__title">About Blayne Pacelli Realtor</h1>
        <p class="page-hero__tagline">Honest, Strategic, Fast, Knowledgeable, and Fair</p>
  </div>
    </div>
</section>

<!-- About Video Hero -->
<?php $about_video = get_field( 'about_video_url', 'option' ); ?>
<?php if ( $about_video ) :
    preg_match( '/(?:youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]+)/', $about_video, $matches );
    $video_id = isset( $matches[1] ) ? $matches[1] : '';
?>
<?php if ( $video_id ) : ?>
<section class="about-video">
    <div class="about-video__inner">
        <div class="about-video__embed">
            <iframe
                src="https://www.youtube.com/embed/<?php echo esc_attr( $video_id ); ?>?rel=0"
                title="About Blayne Pacelli Realtor"
                frameborder="0"
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                allowfullscreen
                loading="lazy">
            </iframe>
        </div>
    </div>
</section>
<?php endif; ?>
<?php endif; ?>

<!-- My Story -->
<section class="about-story">
    <div class="about-story__inner">

        <div class="about-story__content">
            <h2 class="about-story__heading">My Story</h2>
            <p>I'm Blayne Pacelli, and I'm truly passionate about my work as a real estate agent. What I find most rewarding is the opportunity to assist people like you during one of the most significant moments in their lives. Whether you're embarking on the journey of buying your first home or downsizing from a cherished family residence, my primary goal is to provide you with the highest level of service while minimizing any stress that may come your way.</p>
            <p>For me, real estate isn't just about the transaction; it's about fostering relationships based on trust, honesty, and integrity. My approach is far from pushy or agenda-driven. Instead, I take the time to genuinely understand your needs and preferences.</p>
            <p>With a <?php echo $listing_rate ? esc_html( $listing_rate ) : '96.4%'; ?> listing price and <?php echo $closing_rate ? esc_html( $closing_rate ) : '82.9%'; ?> closing success rate over <?php echo $years ? esc_html( $years ) : '20'; ?> years, I can tell you that I find the right buyer population for your listing, stage it to perfection and ensure you get your home listed, pitched, offered, and closed at a higher rate than anyone within the Greater Los Angeles area.</p>
            <p>Outside of work, I cherish spending quality time with my wife, Dawn, and our daughter, Gianna. I'm a devoted sports fan and make it a point to attend Raiders, Trojans, and Dodgers games whenever I can. Staying active is also a big part of my life, whether it's through playing volleyball, basketball, or indulging in my passion for snowboarding.</p>
        </div>

        <?php if ( $photo ) : ?>
        <div class="about-story__photo-wrap">
            <img src="<?php echo esc_url( $photo['url'] ); ?>"
                 alt="<?php echo esc_attr( $photo['alt'] ); ?>"
                 class="about-story__photo">
        </div>
        <?php endif; ?>

    </div>
</section>

<!-- Value Props -->
<section class="about-values">
    <div class="about-values__inner">

        <div class="about-values__card">
            <h3 class="about-values__title">Exclusive Service</h3>
            <p class="about-values__text">At Blayne Pacelli Realtor we are the top local real estate producers, providing every client with the highest level of service to achieve their goals.</p>
        </div>

        <div class="about-values__card">
            <h3 class="about-values__title">Location is Everything</h3>
            <p class="about-values__text">We represent residential properties throughout the West San Fernando Valley and Greater Los Angeles Area.</p>
        </div>

        <div class="about-values__card">
            <h3 class="about-values__title">Guaranteed Service</h3>
            <p class="about-values__text">With exceptional integrity, negotiating skills and marketing strategies, we promise the best price for your property.</p>
        </div>

    </div>
</section>

<!-- Stats -->
<?php get_template_part( 'template-parts/stats-bar' ); ?>

<?php blayne_faq_section( [
    'heading' => 'Common Questions About Working With Blayne',
    'limit'   => 6,
] ); ?>

<!-- Google Reviews -->
<section class="reviews-section">
    <h2 class="reviews-section__heading" style="text-align:center;">What Our Clients Say</h2>
    <h3 class="reviews-section__sub" style="text-align:center;">Real reviews from real clients on Google</h3>
    <div class="reviews-section__inner">
        <?php echo do_shortcode('[trustindex no-registration=google]'); ?>
    </div>
</section>

<!-- CTA -->
<?php get_template_part( 'template-parts/lead-form' ); ?>

<?php get_footer(); ?>