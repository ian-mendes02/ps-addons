<?php

/**
 * Assorted utility and convenience functions.
 * @author Ian Mendes <ianlucamendes02@gmail.com>
 * @since 1.0.0
 */

/**
 * Returns true if current logged in user has member status.
 * @since 1.0.0
 */
function ps_is_member() {
    global $ps_customer;
    return $ps_customer->is_member;
}

/**
 * Returns the store logo as a Base64-encoded svg.
 * @since 1.0.0
 */
function ps_store_icon() {
    return 'data:image/svg+xml;base64,' . base64_encode("<svg width='20' height='20' xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'><path fill='rgba(240,246,252,.6)' d='M14.8,8c0,3.7-3,6.8-6.8,6.8c-0.4,0-0.8,0-1.1-0.1v-3.5c0.4,0.1,0.7,0.2,1.1,0.2c1.9,0,3.4-1.5,3.4-3.4S9.9,4.6,8,4.6S4.6,6.1,4.6,8v6.8H1.2V8c0-3.7,3-6.8,6.8-6.8C11.7,1.2,14.8,4.3,14.8,8'/></svg>");
}