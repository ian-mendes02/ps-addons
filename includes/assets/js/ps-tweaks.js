( function( $ ) {

    // Add 'continue shopping' option after item has been added to cart
    $( document.body ).on( 'added_to_cart', () => {
        let cart = $( 'form.cart button[type=submit]' );
        if ( cart ) $( "<a href='https://podoshop.com.br' class='ps-continue-shopping-prompt'>CONTINUAR COMPRANDO</a>" ).insertAfter( cart );
    } );

    // Set menu cart href to cart page
    $( '#elementor-menu-cart__toggle_button' ).attr( 'href', 'https://podoshop.com.br/carrinho/' );

    // Same thing for mobile version
    $( '#ps-mobile-goto-cart' ).on( 'click', () => document.location = 'https://podoshop.com.br/carrinho/' );

    // Dismiss WooCommerce messages on click
    $( '.woocommerce-error,.woocommerce-info,.woocommerce-message' ).on( "click", ( e ) => {
        var el = $( e.currentTarget );
        el.addClass( 'ps-slide-up' );
        setTimeout( () => el.remove(), 350 );
    } );

    // Set user login button text
    $( '#ps-user-dashboard span.elementor-button-text' ).text( 'Entrar' );

} )( jQuery );