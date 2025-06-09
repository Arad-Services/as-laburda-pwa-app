<?php
/**
 * Funtionality related to listing plan management.
 *
 * @link       https://arad-services.com
 * @since      1.0.0
 * @package    AS_Laburda_PWA_App
 * @subpackage AS_Laburda_PWA_App/includes
 */

/**
 * The listing plan management functionality of the plugin.
 *
 * This class handles the retrieval and application of listing plans.
 *
 * @package    AS_Laburda_PWA_App
 * @subpackage AS_Laburda_PWA_App/includes
 * @author     Arad Services <aradservices.il@gmail.com>
 */
class AS_Laburda_PWA_App_Listing_Plans {

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
     * Get a listing plan by its ID.
     *
     * @since 1.0.0
     * @param int $plan_id Plan ID.
     * @return object|null Plan object on success, null if not found.
     */
    public function get_listing_plan( $plan_id ) {
        // Check if feature is enabled
        $global_features = $this->main_plugin->get_global_feature_settings();
        if ( ! ( $global_features['enable_listing_plans'] ?? false ) ) {
            return null; // Feature not enabled by admin
        }
        return $this->database->get_listing_plan( $plan_id );
    }

    /**
     * Get all listing plans.
     *
     * @since 1.0.0
     * @param bool $active_only Optional. If true, only return active plans.
     * @return array Array of plan objects.
     */
    public function get_all_listing_plans( $active_only = false ) {
        // Check if feature is enabled
        $global_features = $this->main_plugin->get_global_feature_settings();
        if ( ! ( $global_features['enable_listing_plans'] ?? false ) ) {
            return array(); // Feature not enabled by admin
        }
        return $this->database->get_all_listing_plans( $active_only );
    }

    /**
     * Add a new listing plan.
     *
     * @since 1.0.0
     * @param array $data Associative array of plan properties.
     * @return int|false The ID of the new plan on success, false on failure.
     */
    public function add_listing_plan( $data ) {
        // Check if feature is enabled
        $global_features = $this->main_plugin->get_global_feature_settings();
        if ( ! ( $global_features['enable_listing_plans'] ?? false ) ) {
            error_log( 'ASLP: Listing Plans feature is disabled. Plan not added.' );
            return false; // Feature not enabled by admin
        }
        return $this->database->add_listing_plan( $data );
    }

    /**
     * Update an existing listing plan.
     *
     * @since 1.0.0
     * @param int $plan_id The ID of the plan to update.
     * @param array $data Associative array of plan properties to update.
     * @return int|bool Number of rows updated on success, false on failure.
     */
    public function update_listing_plan( $plan_id, $data ) {
        // Check if feature is enabled
        $global_features = $this->main_plugin->get_global_feature_settings();
        if ( ! ( $global_features['enable_listing_plans'] ?? false ) ) {
            error_log( 'ASLP: Listing Plans feature is disabled. Plan not updated.' );
            return false; // Feature not enabled by admin
        }
        return $this->database->update_listing_plan( $plan_id, $data );
    }

    /**
     * Delete a listing plan.
     *
     * @since 1.0.0
     * @param int $plan_id The ID of the plan to delete.
     * @return int|bool Number of rows deleted on success, false on failure.
     */
    public function delete_listing_plan( $plan_id ) {
        // Check if feature is enabled
        $global_features = $this->main_plugin->get_global_feature_settings();
        if ( ! ( $global_features['enable_listing_plans'] ?? false ) ) {
            error_log( 'ASLP: Listing Plans feature is disabled. Plan not deleted.' );
            return false; // Feature not enabled by admin
        }
        return $this->database->delete_listing_plan( $plan_id );
    }

    /**
     * Assign a listing plan to a business listing.
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
        if ( ! ( $global_features['enable_listing_plans'] ?? false ) ) {
            error_log( 'ASLP: Listing Plans feature is disabled. Plan not assigned.' );
            return false; // Feature not enabled by admin
        }

        $plan = $this->database->get_listing_plan( $plan_id );
        $listing = $this->database->get_business_listing( $business_listing_id );

        if ( ! $plan || ! $listing ) {
            error_log( 'ASLP: Plan or Listing not found for assignment.' );
            return false;
        }

        // Ensure the user owns the listing or is an admin
        if ( $listing->user_id !== $user_id && ! current_user_can( 'aslp_manage_all_business_listings' ) ) {
            error_log( 'ASLP: User ' . $user_id . ' does not have permission to assign plan to listing ' . $business_listing_id . '.' );
            return false;
        }

        $end_date = null;
        if ( $plan->duration > 0 ) {
            $end_date = date( 'Y-m-d H:i:s', strtotime( '+' . $plan->duration . ' days' ) );
        }

        $subscription_data = array(
            'user_id'             => $user_id,
            'business_listing_id' => $business_listing_id,
            'plan_id'             => $plan_id,
            'start_date'          => current_time( 'mysql' ),
            'end_date'            => $end_date,
            'status'              => ( 'completed' === $payment_status ) ? 'active' : 'pending',
            'payment_status'      => sanitize_text_field( $payment_status ),
            'transaction_id'      => sanitize_text_field( $transaction_id ),
        );

        // Check for existing active subscription for this listing
        $existing_subscriptions = $this->database->get_user_subscriptions( array(
            'user_id'             => $user_id,
            'business_listing_id' => $business_listing_id,
            'status'              => 'active',
        ) );

        // If there's an existing active subscription, cancel it or mark as expired
        if ( ! empty( $existing_subscriptions ) ) {
            foreach ( $existing_subscriptions as $sub ) {
                $this->database->update_user_subscription( $sub->id, array( 'status' => 'expired', 'end_date' => current_time( 'mysql' ) ) );
            }
        }

        $subscription_id = $this->database->add_user_subscription( $subscription_data );

        if ( $subscription_id ) {
            // Update the business listing's current plan ID
            $this->database->update_business_listing( $business_listing_id, array(
                'current_plan_id' => $plan_id,
                'status'          => 'active', // Mark listing as active if plan is assigned and payment completed
            ) );
            return true;
        }
        return false;
    }

    /**
     * Get active subscription for a specific business listing.
     *
     * @since 1.0.0
     * @param int $business_listing_id Business Listing ID.
     * @return object|null Active subscription object on success, null if not found.
     */
    public function get_active_listing_subscription( $business_listing_id ) {
        // Check if feature is enabled
        $global_features = $this->main_plugin->get_global_feature_settings();
        if ( ! ( $global_features['enable_listing_plans'] ?? false ) ) {
            return null; // Feature not enabled by admin
        }

        $subscriptions = $this->database->get_user_subscriptions( array(
            'business_listing_id' => $business_listing_id,
            'status'              => 'active',
        ) );

        return ! empty( $subscriptions ) ? $subscriptions[0] : null;
    }
}
