<?php

/**
 * Assorted utility and convenience functions.
 * @author Ian Mendes <ianlucamendes02@gmail.com>
 *
 * @since 1.0.0
 */

/**
 * Returns a new PS_Customer object.
 * @since 1.0.0
 */
function ps_customer() {
    return new PS_Customer();
}

/**
 * Formats datetime strings.
 * @since 1.0.0
 *
 * @param string $expr        Precedes the formatted expression.
 * @param string $date_string A string in SQL datetime format.
 */
function ps_esc_datetime( $date_string, $expr = '' ) {
    if ( ps_is_empty( $date_string ) || $date_string === '0000-00-00 00:00:00' ) {
        return '-';
    }
    [$date, $time]      = explode( ' ', $date_string );
    [$year, $day, $mon] = explode( '-', $date );
    [$h, $m, $s]        = explode( ':', $time );
    $datetime           = "$day/$mon/$year às $h:$m";
    if ( $expr != '' ) return $expr . " " . $datetime;
    else return $datetime;
}

/**
 * Returns `true` if expression is null or an empty element.
 * @since 1.0.0
 *
 * @param any $var expression to be evaluated.
 */
function ps_is_empty( &$var ) {
    return $var === null || $var === '' || $var == [];
}

/**
 * Returns `true` if `$var` is set and equals `$val`.
 * @since 1.0.0
 *
 * @param any $var variable to check for.
 * @param any $val desired value of `$var`.
 */
function ps_var( &$var, $val ) {
    return isset( $var ) && $var == $val;
}

/**
 * Prints a tooltip next to an input field.
 * @since 1.0.0
 *
 * @param string $message
 */
function ps_tooltip( $message ) {
    printf( '<span class="ps-tooltip"><p class="hidden">%s</p></span>', $message );
}

/**
 * Formats a value in percent or fixed.
 * @since 1.0.0
 *
 * @param string|float $val
 * @param string       $type
 */
function ps_format_value( $val, $type = 'fixed' ) {
    $num = explode( '.', strval( $val ) );
    $int = $num[0];
    $dec = $num[1] ?? 0;
    if ( (int) $dec < 10 ) $dec = '0' . $dec;
    if ( $type == 'fixed' ) return "R$$int.$dec";
    else return "$int.$dec%";
}