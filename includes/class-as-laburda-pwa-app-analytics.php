<?php
/**
 * Functionality related to Analytics management.
 *
 * @link       https://arad-services.com
 * @since      2.0.0
 * @package    AS_Laburda_PWA_App
 * @subpackage AS_Laburda_PWA_App/includes
 */

/**
 * The Analytics management functionality of the plugin.
 *
 * This class handles tracking of views and clicks for PWA apps and business listings.
 *
 * @since      2.0.0
 * @package    AS_Laburda_PWA_App
 * @subpackage AS_Laburda_PWA_App/includes
 * @author     Arad Services <aradservices.il@gmail.com>
 */
class AS_Laburda_PWA_App_Analytics {

    /**
     * The database object.
     *
     * @since    2.0.0
     * @access   private
     * @var      AS_Laburda_PWA_App_Database    $database    The database manager instance.
     */
    private $database;

    /**
     * The main plugin instance.
     *
     * @since    2.0.0
     * @access   private
     * @var      AS_Laburda_PWA_App    $main_plugin    The main plugin instance.
     */
    private $main_plugin;

    /**
     * Initialize the class and set its properties.
     *
     * @since    2.0.0
     * @param    AS_Laburda_PWA_App $main_plugin The main plugin instance.
     */
    public function __construct( $main_plugin ) {
        $this->main_plugin = $main_plugin;
        $this->database = $main_plugin->get_database_manager();
    }

    /**
     * Track a view for a specific item (app or listing).
     *
     * @since 2.0.0
     * @param string $item_id The ID (UUID for app, numeric ID for listing) of the item.
     * @param string $item_type The type of item ('app' or 'listing').
     * @return bool True on success, false on failure.
     */
    public function track_view( $item_id, $item_type ) {
        // Check if feature is enabled
        $global_features = $this->main_plugin->get_global_feature_settings();
        if ( ! ( $global_features['enable_analytics'] ?? false ) ) {
            error_log( 'ASLP: Analytics feature is disabled. View not tracked.' );
            return false; // Feature not enabled by admin
        }

        if ( empty( $item_id ) || ! in_array( $item_type, array( 'app', 'listing' ) ) ) {
            error_log( 'ASLP: Invalid item_id or item_type provided for view tracking.' );
            return false;
        }

        $data = array(
            'item_id'   => sanitize_text_field( $item_id ),
            'item_type' => sanitize_text_field( $item_type ),
            'ip_address' => AS_Laburda_PWA_App_Utils::get_user_ip(),
            'user_id'   => get_current_user_id() ? get_current_user_id() : null, // Log user if logged in
        );

        return $this->database->add_analytics_view( $data );
    }

    /**
     * Track a click for a specific item (app or listing) on a particular target.
     *
     * @since 2.0.0
     * @param string $item_id The ID (UUID for app, numeric ID for listing) of the item.
     * @param string $item_type The type of item ('app' or 'listing').
     * @param string $click_target The specific target of the click (e.g., 'phone_number', 'website_link', 'product_page').
     * @return bool True on success, false on failure.
     */
    public function track_click( $item_id, $item_type, $click_target ) {
        // Check if feature is enabled
        $global_features = $this->main_plugin->get_global_feature_settings();
        if ( ! ( $global_features['enable_analytics'] ?? false ) ) {
            error_log( 'ASLP: Analytics feature is disabled. Click not tracked.' );
            return false; // Feature not enabled by admin
        }

        if ( empty( $item_id ) || ! in_array( $item_type, array( 'app', 'listing' ) ) || empty( $click_target ) ) {
            error_log( 'ASLP: Invalid item_id, item_type, or click_target provided for click tracking.' );
            return false;
        }

        $data = array(
            'item_id'      => sanitize_text_field( $item_id ),
            'item_type'    => sanitize_text_field( $item_type ),
            'click_target' => sanitize_text_field( $click_target ),
            'ip_address'   => AS_Laburda_PWA_App_Utils::get_user_ip(),
            'user_id'      => get_current_user_id() ? get_current_user_id() : null, // Log user if logged in
        );

        return $this->database->add_analytics_click( $data );
    }

    /**
     * Get analytics data for a specific item.
     *
     * @since 2.0.0
     * @param string $item_id The ID (UUID for app, numeric ID for listing) of the item.
     * @param string $item_type The type of item ('app' or 'listing').
     * @param string $period Optional. Filter by period ('day', 'week', 'month', 'year', 'total').
     * @return array Associative array of analytics data.
     */
    public function get_analytics_data( $item_id, $item_type, $period = 'total' ) {
        // Check if feature is enabled
        $global_features = $this->main_plugin->get_global_feature_settings();
        if ( ! ( $global_features['enable_analytics'] ?? false ) ) {
            return array(); // Feature not enabled by admin
        }

        if ( empty( $item_id ) || ! in_array( $item_type, array( 'app', 'listing' ) ) ) {
            error_log( 'ASLP: Invalid item_id or item_type provided for analytics data retrieval.' );
            return array();
        }

        $view_count = $this->database->get_analytics_view_count( $item_id, $item_type, $period );
        $click_count_total = $this->database->get_analytics_click_count( $item_id, $item_type, $period );

        // Get specific click counts (e.g., for business listings)
        $specific_click_counts = array();
        if ( $item_type === 'listing' ) {
            $specific_click_counts['phone_clicks'] = $this->database->get_analytics_click_count( $item_id, $item_type, $period, 'phone_number' );
            $specific_click_counts['website_clicks'] = $this->database->get_analytics_click_count( $item_id, $item_type, $period, 'website_link' );
            $specific_click_counts['product_views'] = $this->database->get_analytics_click_count( $item_id, $item_type, $period, 'product_view' );
            $specific_click_counts['event_views'] = $this->database->get_analytics_click_count( $item_id, $item_type, $period, 'event_view' );
        } elseif ( $item_type === 'app' ) {
            $specific_click_counts['start_url_clicks'] = $this->database->get_analytics_click_count( $item_id, $item_type, $period, 'start_url' );
            $specific_click_counts['visit_app_clicks'] = $this->database->get_analytics_click_count( $item_id, $item_type, $period, 'visit_app' );
        }

        return array(
            'item_id'             => $item_id,
            'item_type'           => $item_type,
            'period'              => $period,
            'total_views'         => $view_count,
            'total_clicks'        => $click_count_total,
            'specific_clicks'     => $specific_click_counts,
        );
    }
}
