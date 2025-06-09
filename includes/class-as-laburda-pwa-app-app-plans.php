<?php
/**
 * Functionality related to App Plan management.
 *
 * @link       https://arad-services.com
 * @since      2.0.0
 * @package    AS_Laburda_PWA_App
 * @subpackage AS_Laburda_PWA_App/includes
 */

/**
 * The App Plan management functionality of the plugin.
 *
 * This class handles the creation, display, and management of PWA app subscription plans.
 *
 * @since      2.0.0
 * @package    AS_Laburda_PWA_App
 * @subpackage AS_Laburda_PWA_App/includes
 * @author     Arad Services <aradservices.il@gmail.com>
 */
class AS_Laburda_PWA_App_App_Plans {

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
     * Get an app plan by its ID.
     *
     * @since 2.0.0
     * @param int $plan_id Plan ID.
     * @return object|null Plan object on success, null if not found.
     */
    public function get_app_plan( $plan_id ) {
        // Check if app builder feature is enabled
        $global_features = $this->main_plugin->get_global_feature_settings();
        if ( ! ( $global_features['enable_app_builder'] ?? false ) ) {
            return null;
        }
        $plan = $this->database->get_app_plan( $plan_id );
        if ( $plan && ! empty( $plan->features ) ) {
            $plan->features = AS_Laburda_PWA_App_Utils::safe_json_decode( $plan->features, true );
        }
        return $plan;
    }

    /**
     * Get all app plans.
     *
     * @since 2.0.0
     * @param bool $active_only Optional. If true, only return active plans.
     * @return array Array of plan objects.
     */
    public function get_all_app_plans( $active_only = false ) {
        // Check if app builder feature is enabled
        $global_features = $this->main_plugin->get_global_feature_settings();
        if ( ! ( $global_features['enable_app_builder'] ?? false ) ) {
            return array();
        }
        $plans = $this->database->get_all_app_plans( $active_only );
        foreach ( $plans as $plan ) {
            if ( ! empty( $plan->features ) ) {
                $plan->features = AS_Laburda_PWA_App_Utils::safe_json_decode( $plan->features, true );
            }
        }
        return $plans;
    }

    /**
     * Add a new app plan.
     *
     * @since 2.0.0
     * @param array $data Associative array of plan properties.
     * @return int|false The ID of the new plan on success, false on failure.
     */
    public function add_app_plan( $data ) {
        // Check if app builder feature is enabled
        $global_features = $this->main_plugin->get_global_feature_settings();
        if ( ! ( $global_features['enable_app_builder'] ?? false ) ) {
            error_log( 'ASLP: App Builder feature is disabled. App plan not added.' );
            return false;
        }

        $sanitized_data = array(
            'plan_name'   => sanitize_text_field( $data['plan_name'] ?? '' ),
            'description' => sanitize_textarea_field( $data['description'] ?? '' ),
            'price'       => floatval( $data['price'] ?? 0.00 ),
            'duration'    => absint( $data['duration'] ?? 0 ),
            'features'    => AS_Laburda_PWA_App_Utils::safe_json_encode( $data['features'] ?? array() ),
            'is_active'   => isset( $data['is_active'] ) ? (bool) $data['is_active'] : false,
        );

        if ( empty( $sanitized_data['plan_name'] ) || ! isset( $sanitized_data['price'] ) ) {
            error_log( 'ASLP: Missing required app plan data (name or price).' );
            return false;
        }

        return $this->database->add_app_plan( $sanitized_data );
    }

    /**
     * Update an existing app plan.
     *
     * @since 2.0.0
     * @param int $plan_id The ID of the plan to update.
     * @param array $data Associative array of plan properties to update.
     * @return int|bool Number of rows updated on success, false on failure.
     */
    public function update_app_plan( $plan_id, $data ) {
        // Check if app builder feature is enabled
        $global_features = $this->main_plugin->get_global_feature_settings();
        if ( ! ( $global_features['enable_app_builder'] ?? false ) ) {
            error_log( 'ASLP: App Builder feature is disabled. App plan not updated.' );
            return false;
        }

        $plan = $this->database->get_app_plan( $plan_id );
        if ( ! $plan ) {
            error_log( 'ASLP: App plan ' . $plan_id . ' not found for update.' );
            return false;
        }

        $sanitized_data = array(
            'plan_name'   => sanitize_text_field( $data['plan_name'] ?? $plan->plan_name ),
            'description' => sanitize_textarea_field( $data['description'] ?? $plan->description ),
            'price'       => floatval( $data['price'] ?? $plan->price ),
            'duration'    => absint( $data['duration'] ?? $plan->duration ),
            'features'    => AS_Laburda_PWA_App_Utils::safe_json_encode( $data['features'] ?? AS_Laburda_PWA_App_Utils::safe_json_decode($plan->features, true) ),
            'is_active'   => isset( $data['is_active'] ) ? (bool) $data['is_active'] : (bool) $plan->is_active,
        );

        return $this->database->update_app_plan( $plan_id, $sanitized_data );
    }

    /**
     * Delete an app plan.
     *
     * @since 2.0.0
     * @param int $plan_id The ID of the plan to delete.
     * @return int|bool Number of rows deleted on success, false on failure.
     */
    public function delete_app_plan( $plan_id ) {
        // Check if app builder feature is enabled
        $global_features = $this->main_plugin->get_global_feature_settings();
        if ( ! ( $global_features['enable_app_builder'] ?? false ) ) {
            error_log( 'ASLP: App Builder feature is disabled. App plan not deleted.' );
            return false;
        }

        $plan = $this->database->get_app_plan( $plan_id );
        if ( ! $plan ) {
            error_log( 'ASLP: App plan ' . $plan_id . ' not found for deletion.' );
            return false;
        }

        // Check if any apps are currently assigned to this plan before deleting
        $apps_with_plan = $this->database->get_all_apps( array( 'current_app_plan_id' => $plan_id ) );
        if ( ! empty( $apps_with_plan ) ) {
            error_log( 'ASLP: Cannot delete App Plan ' . $plan_id . ' because it is assigned to existing apps.' );
            return false; // Prevent deletion if apps are using this plan
        }

        return $this->database->delete_app_plan( $plan_id );
    }
}
