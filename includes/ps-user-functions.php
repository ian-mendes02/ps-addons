<?php
/**
 * Customer and user-related functions.
 * @since 1.0.0
 */

/**
 * Redirects user to shop on login.
 * @since 1.0.0
 */
function ps_redirect_login( $redirect ) {
    $redirect_page_id = url_to_postid( $redirect );
    $checkout_page_id = wc_get_page_id( 'checkout' );

    if ( $redirect_page_id == $checkout_page_id ) {
        return $redirect;
    }

    return wc_get_page_permalink( 'shop' );
}

/**
 * Verifies an user on registration.
 * @since 1.0.0
 *
 * @param int $id Passed from registration hook.
 */
function ps_user_registration( $id ) {
    PS_Customer::instance( $id )->verify_membership();
}

add_filter( 'woocommerce_login_redirect', 'ps_redirect_login' );

add_filter( 'registration_redirect', 'ps_redirect_login' );

add_action( 'user_register', 'ps_user_registration' );