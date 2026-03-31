document.addEventListener( 'DOMContentLoaded', function() {

    // --------------------------------------------------------
    // Mobile hamburger toggle
    // --------------------------------------------------------
    const hamburger = document.querySelector( '.site-header__hamburger' );
    const nav       = document.querySelector( '.site-nav' );

    if ( hamburger && nav ) {
        hamburger.addEventListener( 'click', function() {
            const expanded = this.getAttribute( 'aria-expanded' ) === 'true';
            this.setAttribute( 'aria-expanded', ! expanded );
            nav.classList.toggle( 'site-nav--open' );
            hamburger.classList.toggle( 'site-header__hamburger--open' );
        } );
    }

    // --------------------------------------------------------
    // Dropdown toggle
    // --------------------------------------------------------
    const dropdownToggles = document.querySelectorAll( '.site-nav__dropdown-toggle' );

    dropdownToggles.forEach( function( toggle ) {
        toggle.addEventListener( 'click', function() {
            const expanded = this.getAttribute( 'aria-expanded' ) === 'true';
            this.setAttribute( 'aria-expanded', ! expanded );
            const dropdown = this.nextElementSibling;
            if ( dropdown ) {
                dropdown.classList.toggle( 'site-nav__dropdown--open' );
            }
        } );
    } );

    // --------------------------------------------------------
    // Close dropdown when clicking outside
    // --------------------------------------------------------
    document.addEventListener( 'click', function( e ) {
        if ( ! e.target.closest( '.site-nav__item--dropdown' ) ) {
            dropdownToggles.forEach( function( toggle ) {
                toggle.setAttribute( 'aria-expanded', 'false' );
                const dropdown = toggle.nextElementSibling;
                if ( dropdown ) {
                    dropdown.classList.remove( 'site-nav__dropdown--open' );
                }
            } );
        }
    } );

    // --------------------------------------------------------
    // Close nav on window resize to desktop
    // --------------------------------------------------------
    window.addEventListener( 'resize', function() {
        if ( window.innerWidth >= 1024 ) {
            nav.classList.remove( 'site-nav--open' );
            if ( hamburger ) {
                hamburger.setAttribute( 'aria-expanded', 'false' );
                hamburger.classList.remove( 'site-header__hamburger--open' );
            }
        }
    } );

} );

// --------------------------------------------------------
    // Stats counter animation
    // --------------------------------------------------------
    const counters = document.querySelectorAll( '.stats-counter' );

    if ( counters.length ) {

        const countUp = ( el ) => {
        const target   = parseInt( el.getAttribute( 'data-target' ) );
        const suffix   = el.getAttribute( 'data-suffix' ) || '';
        const prefix   = el.getAttribute( 'data-prefix' ) || '';
        const format   = el.getAttribute( 'data-format' ) === 'true';
        const duration = 2000;
        const steps    = 60;
        const increment = target / steps;
        let current    = 0;
        let step       = 0;

        const timer = setInterval( () => {
            step++;
            current = Math.min( Math.round( increment * step ), target );

            if ( format ) {
                el.textContent = prefix + current.toLocaleString() + suffix;
            } else {
                el.textContent = prefix + current + suffix;
            }

            if ( current >= target ) {
                clearInterval( timer );
            }
        }, duration / steps );
    };

        const observer = new IntersectionObserver( ( entries ) => {
            entries.forEach( ( entry ) => {
                if ( entry.isIntersecting ) {
                    countUp( entry.target );
                    observer.unobserve( entry.target );
                }
            } );
        }, { threshold: 0.5 } );

        counters.forEach( ( counter ) => {
            observer.observe( counter );
        } );

    }


// --------------------------------------------------------
    // Property gallery carousel
    // --------------------------------------------------------
    const galleryEl = document.getElementById( 'property-gallery' );

    if ( galleryEl ) {
        new Splide( '#property-gallery', {
            type:        'loop',
            perPage:     3,
            perMove:     1,
            gap:         '1.5rem',
            autoplay:    true,
            interval:    4000,
            pauseOnHover: true,
            lazyLoad:    'nearby',
            breakpoints: {
                1023: {
                    perPage: 2,
                },
                767: {
                    perPage: 1,
                },
            },
        } ).mount();
    }


// --------------------------------------------------------
    // City reasons accordion
    // --------------------------------------------------------
    const reasonItems = document.querySelectorAll( '.city-reasons__item' );

    if ( reasonItems.length ) {
        reasonItems.forEach( function( item ) {
            const trigger  = item.querySelector( '.city-reasons__trigger' );
            const content  = item.querySelector( '.city-reasons__content' );
            const icon     = item.querySelector( '.city-reasons__trigger-icon' );
            const image    = document.getElementById( 'city-reasons-image' );

            if ( trigger ) {
                trigger.addEventListener( 'click', function() {
                    const isOpen = item.classList.contains( 'city-reasons__item--open' );

                    // Close all
                    reasonItems.forEach( function( el ) {
                        el.classList.remove( 'city-reasons__item--open' );
                        el.querySelector( '.city-reasons__trigger' ).setAttribute( 'aria-expanded', 'false' );
                        el.querySelector( '.city-reasons__content' ).setAttribute( 'hidden', '' );
                        el.querySelector( '.city-reasons__trigger-icon' ).textContent = '+';
                    });

                    // Open clicked
                    if ( ! isOpen ) {
                        item.classList.add( 'city-reasons__item--open' );
                        trigger.setAttribute( 'aria-expanded', 'true' );
                        content.removeAttribute( 'hidden' );
                        icon.textContent = '−';
                    }
                });
            }
        });
    }