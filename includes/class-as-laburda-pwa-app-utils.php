<?php
/**
 * Functionality related to utility functions.
 *
 * @link       https://arad-services.com
 * @since      1.0.0
 * @package    AS_Laburda_PWA_App
 * @subpackage AS_Laburda_PWA_App/includes
 */

/**
 * The utility functions of the plugin.
 *
 * This class provides various helper functions used throughout the plugin.
 *
 * @since      1.0.0
 * @package    AS_Laburda_PWA_App
 * @subpackage AS_Laburda_PWA_App/includes
 * @author     Arad Services <aradservices.il@gmail.com>
 */
class AS_Laburda_PWA_App_Utils {

    /**
     * Safely encode data to JSON.
     * Handles potential JSON errors.
     *
     * @since 1.0.0
     * @param mixed $data The data to encode.
     * @return string JSON encoded string, or an empty JSON array/object on error.
     */
    public static function safe_json_encode( $data ) {
        $json = json_encode( $data );
        if ( json_last_error() !== JSON_ERROR_NONE ) {
            error_log( 'JSON Encode Error: ' . json_last_error_msg() );
            return '[]'; // Return empty array or object based on context
        }
        return $json;
    }

    /**
     * Safely decode JSON data.
     * Handles potential JSON errors.
     *
     * @since 1.0.0
     * @param string $json The JSON string to decode.
     * @param bool $assoc When true, returned objects will be converted into associative arrays.
     * @return mixed Decoded data, or an empty array/object on error.
     */
    public static function safe_json_decode( $json, $assoc = false ) {
        $data = json_decode( $json, $assoc );
        if ( json_last_error() !== JSON_ERROR_NONE ) {
            error_log( 'JSON Decode Error: ' . json_last_error_msg() . ' for JSON: ' . $json );
            return $assoc ? array() : new stdClass(); // Return empty array or object based on context
        }
        return $data;
    }

    /**
     * Generate a unique alphanumeric code.
     *
     * @since 1.0.0
     * @param string $table_name The name of the database table to check for uniqueness.
     * @param string $column_name The name of the column to check for uniqueness.
     * @param int $length The length of the code to generate.
     * @return string A unique alphanumeric code.
     */
    public static function generate_unique_code( $table_name, $column_name, $length = 10 ) {
        global $wpdb;
        $code = '';
        $found = true;
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $characters_length = strlen( $characters );

        while ( $found ) {
            $code = '';
            for ( $i = 0; $i < $length; $i++ ) {
                $code .= $characters[ wp_rand( 0, $characters_length - 1 ) ];
            }

            $check_sql = $wpdb->prepare( "SELECT COUNT(*) FROM {$wpdb->prefix}{$table_name} WHERE {$column_name} = %s", $code );
            $code_exists = $wpdb->get_var( $check_sql );

            if ( $code_exists == 0 ) {
                $found = false;
            }
        }
        return $code;
    }

    /**
     * Generate a UUID (Universally Unique Identifier).
     *
     * @since 2.0.0
     * @return string A UUID.
     */
    public static function generate_uuid() {
        if ( function_exists( 'wp_generate_uuid4' ) ) {
            return wp_generate_uuid4();
        }
        // Fallback for older WordPress versions or if function is not available
        return sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),
            mt_rand( 0, 0xffff ),
            mt_rand( 0, 0x0fff ) | 0x4000,
            mt_rand( 0, 0x3fff ) | 0x8000,
            mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff )
        );
    }

    /**
     * Get the user's IP address.
     *
     * @since 1.0.0
     * @return string The user's IP address.
     */
    public static function get_user_ip() {
        if ( ! empty( $_SERVER['HTTP_CLIENT_IP'] ) ) {
            // Check ip from share internet
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
            // Check ip is passed from proxy
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            // Get ip address from remote address
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return sanitize_text_field( $ip );
    }

    /**
     * Format a currency value.
     *
     * @since 1.0.0
     * @param float $amount The amount to format.
     * @param string $currency_code The currency code (e.g., 'USD', 'EUR').
     * @return string Formatted currency string.
     */
    public static function format_currency( $amount, $currency_code = 'USD' ) {
        // You might want to make currency_code configurable in global settings
        // For now, hardcode to USD or use a plugin setting.
        return sprintf( '%s%s', html_entity_decode( self::get_currency_symbol( $currency_code ) ), number_format( $amount, 2 ) );
    }

    /**
     * Get currency symbol based on code.
     *
     * @since 1.0.0
     * @param string $currency_code The currency code.
     * @return string The currency symbol.
     */
    public static function get_currency_symbol( $currency_code ) {
        $symbols = array(
            'USD' => '$',
            'EUR' => '€',
            'GBP' => '£',
            'ILS' => '₪', // Israeli New Shekel
            // Add more as needed
        );
        return $symbols[ strtoupper( $currency_code ) ] ?? '$';
    }
}
