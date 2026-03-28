<?php
$phone = get_field( 'phone_number', 'option' );
$email = get_field( 'email_address', 'option' );
?>

<div class="topbar">
    <div class="topbar__inner">
        <span class="topbar__text">Want to Buy or Sell a Property?</span>
        <div class="topbar__contact">
            <?php if ( $phone ) : ?>
                <a href="tel:<?php echo esc_attr( $phone ); ?>" class="topbar__phone">
                    <?php echo esc_html( $phone ); ?>
                </a>
            <?php endif; ?>
            <?php if ( $email ) : ?>
                <a href="mailto:<?php echo esc_attr( $email ); ?>" class="topbar__email">
                    <?php echo esc_html( $email ); ?>
                </a>
            <?php endif; ?>
        </div>
    </div>
</div>