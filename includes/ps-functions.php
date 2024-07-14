<?php

/**
 * Assorted utility and convenience functions.
 * @author Ian Mendes <ianlucamendes02@gmail.com>
 *
 * @since 1.0.0
 */

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
    $num                      = explode( '.', strval( $val ) );
    $int                      = $num[0];
    $dec                      = $num[1] ?? 0;
    if ( (int) $dec < 10 ) $dec = '0' . $dec;
    if ( $type == 'fixed' ) return "R$ $int.$dec";
    else return "$int.$dec%";
}

/**
 * Prints a subscription prompt,
 * @since 1.0.0
 *
 * @param string $d
 */
function ps_push_subscribe( $d ) {
    print( "
        <span class='ps-subscribe'>
            <span>Com o Palmilhando® você economiza <strong>$d</strong></span>
            <a href='https://palmilhando.com.br/assinatura/' target='_blank'>Saiba mais.</a>
        </span>
    " );
}

/**
 * Logs message to file.
 * @since 1.0.0
 */
function ps_log( $origin, $line = null, $content = 'undefined' ) {
    date_default_timezone_set( 'America/Sao_Paulo' );
    $time  = date( 'Y-m-d H:i:s' );
    $file  = PS_ROOT . 'ps.log';
    $log   = "[$time]: $content in $origin" . ( ! empty( $line ) && ":$line" );
    $open  = fopen( $file, "a" );
    $write = fputs( $open, $log );
    fclose( $open );
}