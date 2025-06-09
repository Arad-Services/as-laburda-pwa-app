<?php
/**
 * Funtionality related to notification management.
 *
 * @link       https://arad-services.com
 * @since      1.0.0
 * @package    AS_Laburda_PWA_App
 * @subpackage AS_Laburda_PWA_App/includes
 */

/**
 * The notification management functionality of the plugin.
 *
 * This class handles sending notifications by business owners and managing user subscriptions.
 *
 * @package    AS_Laburda_PWA_App
 * @subpackage AS_Laburda_PWA_App/includes
 * @author     Arad Services <aradservices.il@gmail.com>
 */
class AS_Laburda_PWA_App_Notifications {

    /**
     * The database object.
     *
     * @since    1.0.0
     * @access   private
     * @var      AS_Laburda_PWA_App_Database    $database    The database manager instance.
     */
    private $database;

    /**
     * The main plugin instance.
     *
     * @since    1.0.0
     * @access   private
     * @var      AS_Laburda_PWA_App    $main_plugin    The main plugin instance.
     */
    private $main_plugin;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param    AS_Laburda_PWA_App $main_plugin The main plugin instance.
     */
    public function __construct( $main_plugin ) {
        $this->main_plugin = $main_plugin;
        $this->database = $main_plugin->get_database_manager();
    }

    /**
     * Handle AJAX request to toggle user subscription to a business's notifications.
     *
     * @since 1.0.0
     */
    public function handle_toggle_business_notification() {
        check_ajax_referer( 'aslp_public_nonce', 'nonce' );

        if ( ! is_user_logged_in() ) {
            wp_send_json_error( array( 'message' => __( 'You must be logged in to manage notification subscriptions.', 'as-laburda-pwa-app' ) ) );
        }
        if ( ! current_user_can( 'aslp_manage_notifications' ) ) {
            wp_send_json_error( array( 'message' => __( 'You do not have permission to manage notification subscriptions.', 'as-laburda-pwa-app' ) ) );
        }

        // Check if feature is enabled
        $global_features = $this->main_plugin->get_global_feature_settings();
        if ( ! ( $global_features['enable_notifications'] ?? false ) ) {
            wp_send_json_error( array( 'message' => __( 'Notification feature is currently disabled.', 'as-laburda-pwa-app' ) ) );
        }

        $user_id = get_current_user_id();
        $business_listing_id = absint( $_POST['business_listing_id'] ?? 0 );
        $is_subscribed = isset( $_POST['is_subscribed'] ) ? (bool) $_POST['is_subscribed'] : false;

        if ( empty( $business_listing_id ) ) {
            wp_send_json_error( array( 'message' => __( 'Business Listing ID is required.', 'as-laburda-pwa-app' ) ) );
        }

        $updated = $this->update_user_subscription_status( $user_id, $business_listing_id, $is_subscribed );

        if ( $updated !== false ) {
            $message = $is_subscribed ? __( 'Successfully subscribed to notifications.', 'as-laburda-pwa-app' ) : __( 'Successfully unsubscribed from notifications.', 'as-laburda-pwa-app' );
            wp_send_json_success( array( 'message' => $message ) );
        } else {
            wp_send_json_error( array( 'message' => __( 'Failed to update subscription status.', 'as-laburda-pwa-app' ) ) );
        }
    }

    /**
     * Update a user's notification subscription status for a business.
     *
     * @since 1.0.0
     * @param int $user_id The ID of the user.
     * @param int $business_listing_id The ID of the business listing.
     * @param bool $is_subscribed Whether the user is subscribed (true) or unsubscribed (false).
     * @return int|bool The ID of the inserted/updated row, or false on failure.
     */
    public function update_user_subscription_status( $user_id, $business_listing_id, $is_subscribed ) {
        // Check if feature is enabled
        $global_features = $this->main_plugin->get_global_feature_settings();
        if ( ! ( $global_features['enable_notifications'] ?? false ) ) {
            error_log( 'ASLP: Notification feature is disabled. Subscription status not updated.' );
            return false; // Feature not enabled by admin
        }
        return $this->database->update_user_notification_subscription( $user_id, $business_listing_id, $is_subscribed );
    }

    /**
     * Send a notification from a business listing.
     *
     * @since 1.0.0
     * @param int $business_listing_id The ID of the business listing sending the notification.
     * @param string $notification_title The title of the notification.
     * @param string $notification_content The content of the notification.
     * @param string $target_audience Optional. 'all' or 'subscribers'.
     * @param string $notification_type Optional. 'general', 'promotion', 'event_reminder'.
     * @return int|false The ID of the new notification on success, false on failure.
     */
    public function send_notification( $business_listing_id, $notification_title, $notification_content, $target_audience = 'all', $notification_type = 'general' ) {
        // Check if feature is enabled
        $global_features = $this->main_plugin->get_global_feature_settings();
        if ( ! ( $global_features['enable_notifications'] ?? false ) ) {
            error_log( 'ASLP: Notification feature is disabled. Notification not sent.' );
            return false; // Feature not enabled by admin
        }

        // Verify that the current user has permission to send notifications for this listing
        $user_id = get_current_user_id();
        $listing = $this->database->get_business_listing( $business_listing_id );
        if ( ! $listing || ( $listing->user_id != $user_id && ! current_user_can( 'aslp_manage_all_business_listings' ) ) ) {
            error_log( 'ASLP: User ' . $user_id . ' does not have permission to send notifications for listing ' . $business_listing_id . '.' );
            return false;
        }

        $data = array(
            'business_listing_id'  => absint( $business_listing_id ),
            'notification_title'   => sanitize_text_field( $notification_title ),
            'notification_content' => sanitize_textarea_field( $notification_content ),
            'target_audience'      => sanitize_text_field( $target_audience ),
            'notification_type'    => sanitize_text_field( $notification_type ),
        );

        $inserted_id = $this->database->add_notification( $data );

        if ( $inserted_id ) {
            // In a real PWA, you would trigger a push notification here
            // using a Push API service (e.g., Firebase Cloud Messaging, OneSignal).
            // This would involve sending a request to your push service with the notification payload
            // and targeting the appropriate user segments.
            // For now, we just record it in the database.
            return $inserted_id;
        }
        return false;
    }

    /**
     * Get notifications for a specific business listing.
     *
     * @since 1.0.0
     * @param int $business_listing_id Business Listing ID.
     * @return array Array of notification objects.
     */
    public function get_business_notifications( $business_listing_id ) {
        // Check if feature is enabled
        $global_features = $this->main_plugin->get_global_feature_settings();
        if ( ! ( $global_features['enable_notifications'] ?? false ) ) {
            return array(); // Feature not enabled by admin
        }
        return $this->database->get_notifications_for_business( $business_listing_id );
    }

    /**
     * Get all businesses a user is subscribed to for notifications.
     *
     * @since 1.0.0
     * @param int $user_id The ID of the user.
     * @return array Array of business listing objects the user is subscribed to.
     */
    public function get_user_subscribed_businesses( $user_id ) {
        // Check if feature is enabled
        $global_features = $this->main_plugin->get_global_feature_settings();
        if ( ! ( $global_features['enable_notifications'] ?? false ) ) {
            return array(); // Feature not enabled by admin
        }

        $subscribed_settings = $this->database->get_user_subscribed_businesses( $user_id );
        $businesses = array();
        foreach ( $subscribed_settings as $setting ) {
            $business = $this->database->get_business_listing( $setting->business_listing_id );
            if ( $business ) {
                $businesses[] = $business;
            }
        }
        return $businesses;
    }

    /**
     * Check if a user is subscribed to a specific business's notifications.
     *
     * @since 1.0.0
     * @param int $user_id The ID of the user.
     * @param int $business_listing_id The ID of the business listing.
     * @return bool True if subscribed, false otherwise.
     */
    public function is_user_subscribed_to_business( $user_id, $business_listing_id ) {
        // Check if feature is enabled
        $global_features = $this->main_plugin->get_global_feature_settings();
        if ( ! ( $global_features['enable_notifications'] ?? false ) ) {
            return false; // Feature not enabled by admin
        }

        $subscription = $this->database->get_user_notification_subscription( $user_id, $business_listing_id );
        return ( $subscription && $subscription->is_subscribed );
    }
}
