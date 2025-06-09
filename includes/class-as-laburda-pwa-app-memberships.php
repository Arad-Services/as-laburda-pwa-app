<?php
/**
 * Functionality related to membership management.
 *
 * @link       https://arad-services.com
 * @since      1.0.0
 * @package    AS_Laburda_PWA_App
 * @subpackage AS_Laburda_PWA_App/includes
 */

/**
 * The membership management functionality of the plugin.
 *
 * This class handles user subscriptions to listing plans and their associated features.
 *
 * @package    AS_Laburda_PWA_App
 * @subpackage AS_Laburda_PWA_App/includes
 * @author     Arad Services <aradservices.il@gmail.com>
 */
class AS_Laburda_PWA_App_Memberships {

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
     * Get active subscriptions for a user.
     *
     * @since 1.0.0
     * @param int $user_id The ID of the user.
     * @param int $business_listing_id Optional. Filter by business listing ID.
     * @return array Array of subscription objects.
     */
    public function get_user_active_subscriptions( $user_id, $business_listing_id = 0 ) {
        // Check if feature is enabled
        $global_features = $this->main_plugin->get_global_feature_settings();
        if ( ! ( $global_features['enable_memberships'] ?? false ) ) {
            return array(); // Feature not enabled by admin
        }

        $args = array(
            'user_id' => $user_id,
            'status'  => 'active',
        );
        if ( $business_listing_id > 0 ) {
            $args['business_listing_id'] = $business_listing_id;
        }
        return $this->database->get_user_subscriptions( $args );
    }

    /**
     * Assign a plan to a business listing.
     * This method acts as a wrapper to the listing plans manager's assign_plan_to_listing.
     *
     * @since 1.0.0
     * @param int $user_id The ID of the user (owner of the listing).
     * @param int $business_listing_id The ID of the business listing.
     * @param int $plan_id The ID of the plan to assign.
     * @param string $payment_status The payment status for the subscription ('pending', 'completed', 'failed').
     * @param string $transaction_id Optional. The transaction ID if payment is involved.
     * @return bool True on success, false on failure.
     */
    public function assign_plan_to_listing( $user_id, $business_listing_id, $plan_id, $payment_status = 'completed', $transaction_id = '' ) {
        // Check if feature is enabled
        $global_features = $this->main_plugin->get_global_feature_settings();
        if ( ! ( $global_features['enable_memberships'] ?? false ) || ! ( $global_features['enable_listing_plans'] ?? false ) ) {
            error_log( 'ASLP: Memberships or Listing Plans feature is disabled. Plan not assigned.' );
            return false; // Feature not enabled by admin
        }
        return $this->main_plugin->get_listing_plans_manager()->assign_plan_to_listing( $user_id, $business_listing_id, $plan_id, $payment_status, $transaction_id );
    }

    /**
     * Claim a business listing for a user.
     * This automatically assigns the 'Claim Listing (Free)' plan if available.
     *
     * @since 1.0.0
     * @param int $user_id The ID of the user claiming the listing.
     * @param int $business_listing_id The ID of the business listing to claim.
     * @return bool True on success, false on failure.
     */
    public function claim_business_listing( $user_id, $business_listing_id ) {
        // Check if feature is enabled
        $global_features = $this->main_plugin->get_global_feature_settings();
        if ( ! ( $global_features['enable_memberships'] ?? false ) || ! ( $global_features['enable_listing_plans'] ?? false ) ) {
            error_log( 'ASLP: Memberships or Listing Plans feature is disabled. Listing not claimed.' );
            return false; // Feature not enabled by admin
        }

        // Find the 'Claim Listing (Free)' plan ID
        $claim_plan = $this->database->wpdb->get_row(
            $this->database->wpdb->prepare(
                "SELECT id FROM {$this->database->wpdb->prefix}aslp_listing_plans WHERE is_claim_plan = TRUE AND is_active = TRUE LIMIT 1"
            )
        );

        if ( ! $claim_plan ) {
            error_log( 'ASLP: No active claim plan found.' );
            return false; // No active claim plan found
        }

        // Check if the listing is already claimed
        $listing = $this->database->get_business_listing( $business_listing_id );
        if ( $listing && $listing->is_claimed ) {
            error_log( 'ASLP: Listing ' . $business_listing_id . ' is already claimed.' );
            return false; // Listing already claimed
        }

        // Assign the claim plan to the user for this listing
        $assigned = $this->assign_plan_to_listing( $user_id, $business_listing_id, $claim_plan->id, 'completed' );

        if ( $assigned ) {
            // Update the listing to be claimed and assign to the user
            $this->database->update_business_listing( $business_listing_id, array(
                'user_id'    => $user_id,
                'is_claimed' => true,
                'status'     => 'active' // Or 'pending_review' if admin needs to approve claims
            ) );
            // Add business_owner role to the user
            $user = new WP_User( $user_id );
            if ( ! $user->has_cap( 'business_owner' ) ) {
                $user->add_role( 'business_owner' );
            }
            return true;
        }
        return false;
    }

    /**
     * Check if a user has an active membership for a specific feature.
     * This would typically check the features array of the user's active plan.
     *
     * @since 1.0.0
     * @param int $user_id The ID of the user.
     * @param string $feature_slug The slug of the feature to check (e.g., 'products', 'events', 'notifications').
     * @param int $business_listing_id Optional. The business listing ID if feature is tied to a specific listing.
     * @return bool True if the user has access to the feature, false otherwise.
     */
    public function user_has_feature_access( $user_id, $feature_slug, $business_listing_id = 0 ) {
        // Check if feature is enabled globally
        $global_features = $this->main_plugin->get_global_feature_settings();
        if ( ! ( $global_features['enable_memberships'] ?? false ) ) {
            return false; // Memberships feature is disabled globally
        }

        // Admins always have access
        if ( current_user_can( 'manage_options' ) ) {
            return true;
        }

        // If the feature is not tied to a specific listing, check user's general active subscriptions
        if ( $business_listing_id === 0 ) {
            $subscriptions = $this->get_user_active_subscriptions( $user_id );
        } else {
            // If tied to a listing, check subscription specific to that listing
            $subscriptions = $this->get_user_active_subscriptions( $user_id, $business_listing_id );
            // Also, check if the user owns the listing, if so, they should have access
            $listing = $this->database->get_business_listing( $business_listing_id );
            if ( $listing && $listing->user_id == $user_id ) {
                return true; // Owner always has access to their listing's features
            }
        }

        foreach ( $subscriptions as $subscription ) {
            $plan = $this->database->get_listing_plan( $subscription->plan_id );
            if ( $plan ) {
                $features = AS_Laburda_PWA_App_Utils::safe_json_decode( $plan->features, true );
                if ( is_array( $features ) && in_array( $feature_slug, $features ) ) {
                    return true;
                }
            }
        }

        return false;
    }
}
