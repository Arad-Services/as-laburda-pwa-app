<?php
/**
 * Funtionality related to business listing management.
 *
 * @link       https://arad-services.com
 * @since      1.0.0
 * @package    AS_Laburda_PWA_App
 * @subpackage AS_Laburda_PWA_App/includes
 */

/**
 * The business listing management functionality of the plugin.
 *
 * This class handles the submission, editing, and retrieval of business listings.
 *
 * @package    AS_Laburda_PWA_App
 * @subpackage AS_Laburda_PWA_App/includes
 * @author     Arad Services <aradservices.il@gmail.com>
 */
class AS_Laburda_PWA_App_Business_Listings {

    private $database;
    private $main_plugin; // Reference to the main plugin class

    public function __construct( $main_plugin ) {
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-as-laburda-pwa-app-database.php';
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-as-laburda-pwa-app-utils.php';
        $this->database = new AS_Laburda_PWA_App_Database();
        $this->main_plugin = $main_plugin;
    }

    /**
     * Handle AJAX request to submit/update a business listing.
     *
     * @since 1.0.0
     */
    public function handle_submit_business_listing() {
        check_ajax_referer( 'aslp_submit_business_listing_nonce', 'nonce' );

        $user_id = get_current_user_id();
        if ( ! $user_id ) {
            wp_send_json_error( array( 'message' => __( 'You must be logged in to submit a business listing.', 'as-laburda-pwa-app' ) ) );
        }

        // Check if user has capability to submit/manage listings
        if ( ! current_user_can( 'aslp_submit_business_listing' ) && ! current_user_can( 'aslp_manage_own_business_listings' ) ) {
            wp_send_json_error( array( 'message' => __( 'You do not have permission to submit or edit business listings.', 'as-laburda-pwa-app' ) ) );
        }

        $listing_id = isset( $_POST['listing_id'] ) ? absint( $_POST['listing_id'] ) : 0;

        // If editing, verify ownership
        if ( $listing_id ) {
            $existing_listing = $this->database->get_business_listing( $listing_id );
            if ( ! $existing_listing || ( $existing_listing->user_id != $user_id && ! current_user_can( 'aslp_manage_all_business_listings' ) ) ) {
                wp_send_json_error( array( 'message' => __( 'You do not have permission to edit this business listing.', 'as-laburda-pwa-app' ) ) );
            }
        }

        // Sanitize and validate input
        $listing_title = sanitize_text_field( $_POST['listing_title'] );
        if ( empty( $listing_title ) ) {
            wp_send_json_error( array( 'message' => __( 'Business Name is required.', 'as-laburda-pwa-app' ) ) );
        }

        $data = array(
            'user_id'             => $user_id,
            'app_id'              => sanitize_text_field( $_POST['app_id'] ?? $this->main_plugin->get_current_app_id() ), // Associate with current app or provided app_id
            'listing_title'       => $listing_title,
            'short_description'   => sanitize_text_field( $_POST['short_description'] ),
            'description'         => wp_kses_post( $_POST['description'] ),
            'logo_url'            => esc_url_raw( $_POST['logo_url'] ),
            'featured_image_url'  => esc_url_raw( $_POST['featured_image_url'] ),
            'gallery_images_urls' => sanitize_text_field( $_POST['gallery_images_urls'] ), // Comma-separated URLs
            'youtube_video_id'    => sanitize_text_field( $_POST['youtube_video_id'] ),
            'city'                => sanitize_text_field( $_POST['city'] ),
            'address'             => sanitize_textarea_field( $_POST['address'] ),
            'phone_number'        => sanitize_text_field( $_POST['phone_number'] ),
            'whatsapp_number'     => sanitize_text_field( $_POST['whatsapp_number'] ),
            'website_url'         => esc_url_raw( $_POST['website_url'] ),
            'business_hours'      => AS_Laburda_PWA_App_Utils::safe_json_encode( $_POST['business_hours'] ), // Assuming array/JSON
            'categories'          => sanitize_text_field( $_POST['categories'] ), // Comma-separated
            'more_info_under_map' => sanitize_text_field( $_POST['more_info_under_map'] ),
            'features'            => sanitize_text_field( $_POST['features'] ), // Comma-separated
            'price_range'         => sanitize_text_field( $_POST['price_range'] ),
            'faq'                 => AS_Laburda_PWA_App_Utils::safe_json_encode( $_POST['faq'] ), // Assuming array/JSON
            'social_links'        => AS_Laburda_PWA_App_Utils::safe_json_encode( $_POST['social_links'] ), // Assuming array/JSON
            'tags'                => sanitize_text_field( $_POST['tags'] ), // Comma-separated
            'keywords'            => sanitize_text_field( $_POST['keywords'] ), // Comma-separated
            'booking_options'     => AS_Laburda_PWA_App_Utils::safe_json_encode( $_POST['booking_options'] ), // Assuming array/JSON
            'menu_option'         => AS_Laburda_PWA_App_Utils::safe_json_encode( $_POST['menu_option'] ), // Assuming array/JSON
            // 'current_plan_id' will be set via subscription logic
            // 'is_claimed' will be set via claim logic
            'status'              => 'pending', // Default status, admin can change to 'active'
        );

        // Handle custom fields dynamically
        $custom_fields = $this->database->get_all_custom_fields( 'business_listing', true );
        foreach ( $custom_fields as $field ) {
            $field_name = $field->field_name;
            if ( isset( $_POST['custom_field_' . $field_name] ) ) {
                $value = $_POST['custom_field_' . $field_name];
                // Sanitize based on field type (simplified, a full implementation would use a dedicated sanitizer)
                if ( in_array( $field->field_type, array('text', 'email', 'url', 'number', 'select', 'radio') ) ) {
                    $data['custom_field_' . $field_name] = sanitize_text_field( $value );
                } elseif ( $field->field_type === 'textarea' ) {
                    $data['custom_field_' . $field_name] = sanitize_textarea_field( $value );
                } elseif ( $field->field_type === 'checkbox' ) {
                    $data['custom_field_' . $field_name] = (bool) $value;
                } else {
                    $data['custom_field_' . $field_name] = sanitize_text_field( $value );
                }
            } else {
                $data['custom_field_' . $field_name] = ''; // Ensure field exists even if empty
            }
        }


        if ( $listing_id ) {
            $updated = $this->database->update_business_listing( $listing_id, $data );
            if ( $updated !== false ) {
                wp_send_json_success( array( 'message' => __( 'Business listing updated successfully! Awaiting admin review.', 'as-laburda-pwa-app' ), 'listing_id' => $listing_id ) );
            } else {
                wp_send_json_error( array( 'message' => __( 'Failed to update business listing.', 'as-laburda-pwa-app' ) ) );
            }
        } else {
            $inserted_id = $this->database->add_business_listing( $data );
            if ( $inserted_id ) {
                wp_send_json_success( array( 'message' => __( 'Business listing submitted successfully! Awaiting admin review.', 'as-laburda-pwa-app' ), 'listing_id' => $inserted_id ) );
            } else {
                wp_send_json_error( array( 'message' => __( 'Failed to submit business listing.', 'as-laburda-pwa-app' ) ) );
            }
        }
    }

    /**
     * Handle AJAX request to get business listing data.
     *
     * @since 1.0.0
     */
    public function handle_get_business_listing_data() {
        // No nonce check here if this is intended for public display (read-only)
        // If it's for editing, add check_ajax_referer('aslp_edit_listing_nonce', 'nonce'); and capability check.

        $listing_id = isset( $_POST['listing_id'] ) ? absint( $_POST['listing_id'] ) : 0;

        if ( empty( $listing_id ) ) {
            wp_send_json_error( array( 'message' => __( 'Invalid listing ID.', 'as-laburda-pwa-app' ) ) );
        }

        $listing = $this->database->get_business_listing( $listing_id );

        if ( $listing ) {
            // Decode JSON fields for frontend use
            $listing->business_hours = AS_Laburda_PWA_App_Utils::safe_json_decode( $listing->business_hours, true );
            $listing->faq = AS_Laburda_PWA_App_Utils::safe_json_decode( $listing->faq, true );
            $listing->social_links = AS_Laburda_PWA_App_Utils::safe_json_decode( $listing->social_links, true );
            $listing->booking_options = AS_Laburda_PWA_App_Utils::safe_json_decode( $listing->booking_options, true );
            $listing->menu_option = AS_Laburda_PWA_App_Utils::safe_json_decode( $listing->menu_option, true );

            wp_send_json_success( array( 'listing' => $listing ) );
        } else {
            wp_send_json_error( array( 'message' => __( 'Business listing not found.', 'as-laburda-pwa-app' ) ) );
        }
    }
}

