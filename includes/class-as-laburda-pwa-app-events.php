<?php
/**
 * Functionality related to event management.
 *
 * @link       https://arad-services.com
 * @since      1.0.0
 * @package    AS_Laburda_PWA_App
 * @subpackage AS_Laburda_PWA_App/includes
 */

/**
 * The event management functionality of the plugin.
 *
 * This class handles the creation, display, and management of events for business listings.
 *
 * @package    AS_Laburda_PWA_App
 * @subpackage AS_Laburda_PWA_App/includes
 * @author     Arad Services <aradservices.il@gmail.com>
 */
class AS_Laburda_PWA_App_Events {

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
     * Get events for a specific business listing.
     *
     * @since 1.0.0
     * @param int $business_listing_id Business Listing ID.
     * @param bool $active_only Optional. If true, only return active/future events.
     * @return array Array of event objects.
     */
    public function get_events( $business_listing_id, $active_only = false ) {
        // Check if feature is enabled
        $global_features = $this->main_plugin->get_global_feature_settings();
        if ( ! ( $global_features['enable_events'] ?? false ) ) {
            return array(); // Feature not enabled by admin
        }
        return $this->database->get_events_for_business( $business_listing_id, $active_only );
    }

    /**
     * Get a single event by ID.
     *
     * @since 1.0.0
     * @param int $event_id Event ID.
     * @return object|null Event object on success, null if not found.
     */
    public function get_single_event( $event_id ) {
        // Check if feature is enabled
        $global_features = $this->main_plugin->get_global_feature_settings();
        if ( ! ( $global_features['enable_events'] ?? false ) ) {
            return null; // Feature not enabled by admin
        }
        return $this->database->get_event( $event_id );
    }

    /**
     * Add a new event.
     *
     * @since 1.0.0
     * @param array $data Associative array of event properties.
     * @return int|false The ID of the new event on success, false on failure.
     */
    public function add_event( $data ) {
        // Check if feature is enabled
        $global_features = $this->main_plugin->get_global_feature_settings();
        if ( ! ( $global_features['enable_events'] ?? false ) ) {
            error_log( 'ASLP: Events feature is disabled. Event not added.' );
            return false; // Feature not enabled by admin
        }
        return $this->database->add_event( $data );
    }

    /**
     * Update an existing event.
     *
     * @since 1.0.0
     * @param int $event_id The ID of the event to update.
     * @param array $data Associative array of event properties to update.
     * @return int|bool Number of rows updated on success, false on failure.
     */
    public function update_event( $event_id, $data ) {
        // Check if feature is enabled
        $global_features = $this->main_plugin->get_global_feature_settings();
        if ( ! ( $global_features['enable_events'] ?? false ) ) {
            error_log( 'ASLP: Events feature is disabled. Event not updated.' );
            return false; // Feature not enabled by admin
        }
        return $this->database->update_event( $event_id, $data );
    }

    /**
     * Delete an event.
     *
     * @since 1.0.0
     * @param int $event_id The ID of the event to delete.
     * @param int $user_id Optional. User ID for permission check (if not admin).
     * @return int|bool Number of rows deleted on success, false on failure.
     */
    public function delete_event( $event_id, $user_id = 0 ) {
        // Check if feature is enabled
        $global_features = $this->main_plugin->get_global_feature_settings();
        if ( ! ( $global_features['enable_events'] ?? false ) ) {
            error_log( 'ASLP: Events feature is disabled. Event not deleted.' );
            return false; // Feature not enabled by admin
        }

        $event = $this->database->get_event( $event_id );
        if ( ! $event ) {
            error_log( 'ASLP: Event ' . $event_id . ' not found for deletion.' );
            return false;
        }

        // Verify ownership of the associated business listing
        $listing = $this->main_plugin->get_database_manager()->get_business_listing( $event->business_listing_id );
        if ( ! $listing || ( $user_id && $listing->user_id != $user_id && ! current_user_can( 'aslp_manage_all_business_listings' ) ) ) {
            error_log( 'ASLP: User ' . $user_id . ' does not have permission to delete event ' . $event_id . '.' );
            return false;
        }

        return $this->database->delete_event( $event_id );
    }

    /* --- AJAX Handlers --- */

    /**
     * Handle AJAX request to submit an event. (Public/Business Owner side)
     *
     * @since 1.0.0
     */
    public function handle_submit_event() {
        check_ajax_referer( 'aslp_public_nonce', 'nonce' );

        if ( ! is_user_logged_in() ) {
            wp_send_json_error( array( 'message' => __( 'You must be logged in to submit an event.', 'as-laburda-pwa-app' ) ) );
        }
        if ( ! current_user_can( 'aslp_manage_events' ) ) {
            wp_send_json_error( array( 'message' => __( 'You do not have permission to manage events.', 'as-laburda-pwa-app' ) ) );
        }

        // Check if feature is enabled
        $global_features = $this->main_plugin->get_global_feature_settings();
        if ( ! ( $global_features['enable_events'] ?? false ) ) {
            wp_send_json_error( array( 'message' => __( 'Event management feature is currently disabled.', 'as-laburda-pwa-app' ) ) );
        }

        $user_id = get_current_user_id();
        $event_data = json_decode( stripslashes( $_POST['event_data'] ?? '{}' ), true );
        $event_id = absint( $_POST['event_id'] ?? 0 );

        if ( empty( $event_data['business_listing_id'] ) || empty( $event_data['event_name'] ) || empty( $event_data['event_date'] ) ) {
            wp_send_json_error( array( 'message' => __( 'Missing required event data (Business Listing ID, Event Name, Event Date).', 'as-laburda-pwa-app' ) ) );
        }

        // Verify ownership of the business listing
        $listing = $this->main_plugin->get_database_manager()->get_business_listing( absint( $event_data['business_listing_id'] ) );
        if ( ! $listing || ( $listing->user_id != $user_id && ! current_user_can( 'aslp_manage_all_business_listings' ) ) ) {
            wp_send_json_error( array( 'message' => __( 'You do not have permission to add events to this business listing.', 'as-laburda-pwa-app' ) ) );
        }

        $data_to_save = array(
            'business_listing_id' => absint( $event_data['business_listing_id'] ),
            'event_name'          => sanitize_text_field( $event_data['event_name'] ),
            'description'         => sanitize_textarea_field( $event_data['description'] ?? '' ),
            'event_date'          => sanitize_text_field( $event_data['event_date'] ),
            'event_time'          => sanitize_text_field( $event_data['event_time'] ?? '' ),
            'location'            => sanitize_text_field( $event_data['location'] ?? '' ),
            'image_url'           => esc_url_raw( $event_data['image_url'] ?? '' ),
            'status'              => sanitize_text_field( $event_data['status'] ?? 'active' ),
        );

        if ( $event_id ) {
            $updated = $this->update_event( $event_id, $data_to_save );
            if ( $updated !== false ) {
                wp_send_json_success( array( 'message' => __( 'Event updated successfully.', 'as-laburda-pwa-app' ), 'event_id' => $event_id ) );
            } else {
                wp_send_json_error( array( 'message' => __( 'Failed to update event.', 'as-laburda-pwa-app' ) ) );
            }
        } else {
            $inserted_id = $this->add_event( $data_to_save );
            if ( $inserted_id ) {
                wp_send_json_success( array( 'message' => __( 'Event added successfully.', 'as-laburda-pwa-app' ), 'event_id' => $inserted_id ) );
            } else {
                wp_send_json_error( array( 'message' => __( 'Failed to add event.', 'as-laburda-pwa-app' ) ) );
            }
        }
    }

    /**
     * Handle AJAX request to delete an event. (Public/Business Owner side)
     *
     * @since 1.0.0
     */
    public function handle_delete_event() {
        check_ajax_referer( 'aslp_public_nonce', 'nonce' );

        if ( ! is_user_logged_in() ) {
            wp_send_json_error( array( 'message' => __( 'You must be logged in to delete an event.', 'as-laburda-pwa-app' ) ) );
        }
        if ( ! current_user_can( 'aslp_manage_events' ) ) {
            wp_send_json_error( array( 'message' => __( 'You do not have permission to delete events.', 'as-laburda-pwa-app' ) ) );
        }

        // Check if feature is enabled
        $global_features = $this->main_plugin->get_global_feature_settings();
        if ( ! ( $global_features['enable_events'] ?? false ) ) {
            wp_send_json_error( array( 'message' => __( 'Event management feature is currently disabled.', 'as-laburda-pwa-app' ) ) );
        }

        $event_id = absint( $_POST['event_id'] ?? 0 );
        $user_id = get_current_user_id();

        if ( empty( $event_id ) ) {
            wp_send_json_error( array( 'message' => __( 'Event ID is required.', 'as-laburda-pwa-app' ) ) );
        }

        $deleted = $this->delete_event( $event_id, $user_id );

        if ( $deleted ) {
            wp_send_json_success( array( 'message' => __( 'Event deleted successfully.', 'as-laburda-pwa-app' ) ) );
        } else {
            wp_send_json_error( array( 'message' => __( 'Failed to delete event. Check if you own this event\'s business listing.', 'as-laburda-pwa-app' ) ) );
        }
    }

    /**
     * Handle AJAX request to get a single event. (Public/Business Owner side)
     *
     * @since 1.0.0
     */
    public function handle_get_event() {
        check_ajax_referer( 'aslp_public_nonce', 'nonce' );

        // Check if feature is enabled
        $global_features = $this->main_plugin->get_global_feature_settings();
        if ( ! ( $global_features['enable_events'] ?? false ) ) {
            wp_send_json_error( array( 'message' => __( 'Event management feature is currently disabled.', 'as-laburda-pwa-app' ) ) );
        }

        $event_id = absint( $_POST['event_id'] ?? 0 );
        if ( empty( $event_id ) ) {
            wp_send_json_error( array( 'message' => __( 'Event ID is required.', 'as-laburda-pwa-app' ) ) );
        }

        $event = $this->get_single_event( $event_id );

        if ( ! $event ) {
            wp_send_json_error( array( 'message' => __( 'Event not found.', 'as-laburda-pwa-app' ) ) );
        }

        // Check if user has permission to view this event (e.g., if it's from their business or public)
        $listing = $this->main_plugin->get_database_manager()->get_business_listing( $event->business_listing_id );
        if ( ! $listing || ( $event->status !== 'active' && ( ! is_user_logged_in() || ( get_current_user_id() !== $listing->user_id && ! current_user_can( 'aslp_manage_all_business_listings' ) ) ) ) ) {
            wp_send_json_error( array( 'message' => __( 'You do not have permission to view this event.', 'as-laburda-pwa-app' ) ) );
        }

        wp_send_json_success( array( 'event' => $event ) );
    }

    /**
     * Handle AJAX request to get all events for a specific business listing. (Public/Business Owner side)
     *
     * @since 1.0.0
     */
    public function handle_get_events_for_business() {
        check_ajax_referer( 'aslp_public_nonce', 'nonce' );

        // Check if feature is enabled
        $global_features = $this->main_plugin->get_global_feature_settings();
        if ( ! ( $global_features['enable_events'] ?? false ) ) {
            wp_send_json_error( array( 'message' => __( 'Event management feature is currently disabled.', 'as-laburda-pwa-app' ) ) );
        }

        $business_listing_id = isset( $_POST['business_listing_id'] ) ? absint( $_POST['business_listing_id'] ) : 0;
        if ( empty( $business_listing_id ) ) {
            wp_send_json_error( array( 'message' => __( 'Business Listing ID is required.', 'as-laburda-pwa-app' ) ) );
        }

        $listing = $this->main_plugin->get_database_manager()->get_business_listing( $business_listing_id );
        if ( ! $listing ) {
            wp_send_json_error( array( 'message' => __( 'Business listing not found.', 'as-laburda-pwa-app' ) ) );
        }

        // Determine if user can view all events (active/inactive) or only active ones
        $active_only = true;
        if ( is_user_logged_in() && ( get_current_user_id() === $listing->user_id || current_user_can( 'aslp_manage_all_business_listings' ) ) ) {
            $active_only = false; // Business owner or admin can see all
        }

        $events = $this->get_events( $business_listing_id, $active_only );
        wp_send_json_success( array( 'events' => $events ) );
    }
}
